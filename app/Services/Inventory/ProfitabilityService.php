<?php

namespace App\Services\Inventory;

use App\Models\Producto;
use App\Models\Receta;
use App\Models\Empresa;
use Illuminate\Support\Facades\DB;

class ProfitabilityService
{
    use \App\Traits\Inventory\UnitConversionTrait;

    /**
     * Calcula el costo base de un producto basado en su receta y rendimiento del insumo
     */
    public function calcularCostoReceta(Producto $producto)
    {
        $recetas = Receta::where('producto_id', $producto->id)->with('insumo')->get();
        $costoTotal = 0;

        foreach ($recetas as $item) {
            // Conversión de Unidades: 
            // Si el insumo está en KG y la receta en G, convertimos la cantidad de la receta a la unidad del insumo
            // para que la multiplicación por el costo_unitario sea correcta.
            try {
                $cantidadConvertida = $this->convert($item->cantidad, $item->unidad_medida, $item->insumo->unidad_medida);
            } catch (\Exception $e) {
                // Si hay error de compatibilidad (ej: kg a ml), usamos la cantidad original como fallback pero logueamos
                \Illuminate\Support\Facades\Log::warning("Error de conversión en receta de {$producto->nombre}: " . $e->getMessage());
                $cantidadConvertida = $item->cantidad;
            }

            $costoBase = $cantidadConvertida * ($item->insumo->costo_unitario ?? 0);

            // 1. Ajuste por Rendimiento del Insumo (Yield Factor)
            // Si el rendimiento es 80%, el costo real es costoBase / 0.8
            $rendimiento = ($item->insumo->rendimiento ?? 100) / 100;
            $costoRealInsumo = $rendimiento > 0 ? ($costoBase / $rendimiento) : $costoBase;

            // 2. Ajuste por Merma específica de la receta
            $merma = $item->merma_esperada ?? 0;
            $divisor = 1 - ($merma / 100);

            $costoTotal += ($divisor > 0) ? ($costoRealInsumo / $divisor) : $costoRealInsumo;
        }

        return $costoTotal;
    }

    /**
     * Calcula el análisis de rentabilidad completo
     */
    public function analizarRentabilidad(Producto $producto)
    {
        $empresa = Empresa::find($producto->empresa_id);

        $costoReceta = $this->calcularCostoReceta($producto);

        $porcentajeIndirectos = $empresa->gastos_indirectos_porcentaje ?? 0;
        $porcentajeMerma = $empresa->merma_esperada_porcentaje ?? 0;

        $costoIndirectos = ($costoReceta * ($porcentajeIndirectos / 100));
        $costoMerma = ($costoReceta * ($porcentajeMerma / 100));
        $gastoOperativoFijo = (float) $producto->gasto_operativo_fijo;

        $costoFinal = $costoReceta + $costoIndirectos + $costoMerma + $gastoOperativoFijo;

        $precioVenta = (float) $producto->precio;

        // Calcular impuestos para saber el ingreso real (asumiendo que el precio incluye el impuesto)
        $porcentajeImpuesto = (float) ($producto->porcentaje_impuesto ?? 0);
        $ingresoNeto = $precioVenta;
        $montoImpuesto = 0;

        if ($producto->tipo_impuesto != 'EXENTO' && $porcentajeImpuesto > 0) {
            $ingresoNeto = $precioVenta / (1 + ($porcentajeImpuesto / 100));
            $montoImpuesto = $precioVenta - $ingresoNeto;
        }

        $utilidadNeta = $ingresoNeto - $costoFinal;
        $margenPorcentaje = $ingresoNeto > 0 ? ($utilidadNeta / $ingresoNeto) * 100 : 0;

        return [
            'costo_receta' => $costoReceta,
            'costo_indirectos' => $costoIndirectos,
            'costo_merma' => $costoMerma,
            'gasto_operativo_fijo' => $gastoOperativoFijo,
            'costo_final' => $costoFinal,
            'monto_impuesto' => $montoImpuesto,
            'ingreso_neto' => $ingresoNeto,
            'utilidad_neta' => $utilidadNeta,
            'margen_porcentaje' => $margenPorcentaje,
            'precio_actual' => $precioVenta
        ];
    }

    /**
     * Matriz de Boston (Ingeniería de Menú)
     */
    public function generarMatrizBoston($empresaId)
    {
        $productos = Producto::retail()->where('empresa_id', $empresaId)->get();
        $analisisCompleto = [];

        $totalVentas = 0;
        $totalUtilidad = 0;

        foreach ($productos as $p) {
            $ventasCount = DB::table('producto_venta')->where('producto_id', $p->id)->sum('cantidad');
            $rentabilidad = $this->analizarRentabilidad($p);

            $analisisCompleto[] = [
                'id' => $p->id,
                'nombre' => $p->nombre,
                'ventas' => $ventasCount,
                'utilidad_unitaria' => $rentabilidad['utilidad_neta'],
                'margen' => $rentabilidad['margen_porcentaje']
            ];

            $totalVentas += $ventasCount;
            $totalUtilidad += ($ventasCount * $rentabilidad['utilidad_neta']);
        }

        $promedioVentas = count($productos) > 0 ? $totalVentas / count($productos) : 0;
        $promedioUtilidad = count($productos) > 0 ? $totalUtilidad / count($productos) : 0;

        return collect($analisisCompleto)->map(function ($item) use ($promedioVentas, $promedioUtilidad) {
            $altaVenta = $item['ventas'] >= $promedioVentas;
            $altaUtilidad = $item['utilidad_unitaria'] >= $promedioUtilidad;

            if ($altaVenta && $altaUtilidad)
                $categoria = 'ESTRELLA';
            elseif ($altaVenta && !$altaUtilidad)
                $categoria = 'CABALLO_DE_BATALLA';
            elseif (!$altaVenta && $altaUtilidad)
                $categoria = 'PUZZLE';
            else
                $categoria = 'PERRO';

            $item['categoria_boston'] = $categoria;
            return $item;
        });
    }

    /**
     * Sugiere un precio de venta para obtener un margen objetivo
     */
    public function sugerirPrecio(Producto $producto, $margenObjetivoPorcentaje = 40)
    {
        $analisis = $this->analizarRentabilidad($producto);
        $costoFinal = $analisis['costo_final'];

        if ($margenObjetivoPorcentaje >= 100)
            return 0;

        // Formula: PV = Costo / (1 - Margen)
        $factor = 1 - ($margenObjetivoPorcentaje / 100);
        return $factor > 0 ? ($costoFinal / $factor) : 0;
    }
}
