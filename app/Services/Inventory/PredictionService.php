<?php

namespace App\Services\Inventory;

use App\Models\Insumo;
use App\Models\Venta;
use App\Models\FuncionAsiento;
use App\Models\Producto;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PredictionService
{
    /**
     * Calcula la sugerencia de compra para un insumo específico
     */
    public function sugerirCompra(Insumo $insumo, $diasProyeccion = 7)
    {
        $diasAnalisis = 14; // Histórico base
        $inicio = now()->subDays($diasAnalisis);

        // 1. Promedio Histórico (Ventas pasadas)
        // Obtenemos los productos que usan este insumo
        $productosIds = DB::table('recetas')
            ->where('insumo_id', $insumo->id)
            ->pluck('producto_id');

        $consumoHistoricoTotal = DB::table('producto_venta as pv')
            ->join('ventas as v', 'v.id', '=', 'pv.venta_id')
            ->join('recetas as r', 'r.producto_id', '=', 'pv.producto_id')
            ->where('r.insumo_id', $insumo->id)
            ->where('v.created_at', '>=', $inicio)
            ->where('v.estado_pago', 'PAGADA')
            ->select(DB::raw('SUM(pv.cantidad * r.cantidad) as total_consumido'))
            ->value('total_consumido') ?? 0;

        $promedioDiario = $consumoHistoricoTotal / $diasAnalisis;
        $proyeccionHistorica = $promedioDiario * $diasProyeccion;

        // 2. Preventas de Cine (Demanda Futura)
        // Tickets vendidos para funciones futuras
        $consumoPreventas = DB::table('funcion_asientos as fa')
            ->join('funciones as f', 'f.id', '=', 'fa.funcion_id')
            ->join('recetas as r', function ($join) use ($insumo) {
                // ...
            })
            ->where('f.fecha_hora', '>', now())
            ->count();

        // Factor de conversión Ocupación Cine -> Consumo Insumo (Basado en historial)
        // Si no hay preventas directas de productos, usamos el ratio de ocupación.
        $ratioConsumoPorTicket = $consumoHistoricoTotal / max(1, $this->getTicketsVendidosEnPeriodo($insumo->empresa_id, $inicio, now()));

        $preventasFuturas = $this->getTicketsVendidosEnPeriodo($insumo->empresa_id, now(), now()->addDays($diasProyeccion));
        $demandaProyectadaPreventas = $preventasFuturas * $ratioConsumoPorTicket;

        // 3. Resultado Final
        $sugerido = max($proyeccionHistorica, $demandaProyectadaPreventas);

        // Sumar Stock de Seguridad
        $sugerido += $insumo->stock_seguridad;

        // Restar lo que ya hay
        $compraNecesaria = max(0, $sugerido - $insumo->stock_actual);

        return [
            'insumo' => $insumo->nombre,
            'stock_actual' => $insumo->stock_actual,
            'promedio_diario' => $promedioDiario,
            'demanda_por_preventas' => $demandaProyectadaPreventas,
            'sugerido_total' => $sugerido,
            'compra_necesaria' => $compraNecesaria,
            'riesgo_desabastecimiento' => $demandaProyectadaPreventas > $insumo->stock_actual
        ];
    }

    private function getTicketsVendidosEnPeriodo($empresaId, $inicio, $fin)
    {
        return DB::table('funcion_asientos as fa')
            ->join('funciones as f', 'f.id', '=', 'fa.funcion_id')
            ->where('f.empresa_id', $empresaId)
            ->whereBetween('f.fecha_hora', [$inicio, $fin])
            ->count();
    }

    /**
     * Reporte consolidado de Compras
     */
    public function generarPlanCompras($empresaId, $dias = 7)
    {
        $insumos = Insumo::where('empresa_id', $empresaId)->get();
        $plan = [];

        foreach ($insumos as $insumo) {
            $analisis = $this->sugerirCompra($insumo, $dias);
            if ($analisis['compra_necesaria'] > 0) {
                $plan[] = $analisis;
            }
        }

        return $plan;
    }

    /**
     * Wrapper for backward compatibility
     */
    public function generarPlanComprasSemanal($empresaId)
    {
        return $this->generarPlanCompras($empresaId, 7);
    }
}
