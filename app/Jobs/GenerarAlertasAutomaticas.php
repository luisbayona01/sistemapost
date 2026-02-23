<?php

namespace App\Jobs;

use App\Models\{Alerta, Producto, Funcion, FuncionAsiento, Caja};
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GenerarAlertasAutomaticas implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $empresas = \App\Models\Empresa::all();

        foreach ($empresas as $empresa) {
            $this->generarAlertasInventario($empresa);
            $this->generarAlertasOcupacion($empresa);
            $this->generarAlertasCaja($empresa);
            $this->generarAlertasPrecios($empresa);
        }
    }

    private function generarAlertasInventario($empresa)
    {
        // CRÍTICO: Stock crítico con ventas activas
        $productosStockCritico = Producto::where('empresa_id', $empresa->id)
            ->where('es_venta_retail', true)
            ->whereRaw('stock_actual < stock_minimo')
            ->get();

        foreach ($productosStockCritico as $producto) {
            // Verificar si no existe ya esta alerta
            $existeAlerta = Alerta::where('empresa_id', $empresa->id)
                ->where('categoria', 'INVENTARIO')
                ->where('datos->producto_id', $producto->id)
                ->where('resuelta', false)
                ->exists();

            if (!$existeAlerta) {
                Alerta::create([
                    'empresa_id' => $empresa->id,
                    'tipo' => 'CRITICA',
                    'categoria' => 'INVENTARIO',
                    'titulo' => "Stock crítico: {$producto->nombre}",
                    'mensaje' => "El producto {$producto->nombre} tiene {$producto->stock_actual} unidades (mínimo: {$producto->stock_minimo}). Se requiere reposición urgente.",
                    'datos' => [
                        'producto_id' => $producto->id,
                        'stock_actual' => $producto->stock_actual,
                        'stock_minimo' => $producto->stock_minimo,
                    ],
                ]);
            }
        }

        // ADVERTENCIA: Productos sin movimiento en 7 días
        $fechaLimite = Carbon::now()->subDays(7);
        $productosSinMovimiento = Producto::where('empresa_id', $empresa->id)
            ->where('es_venta_retail', true)
            ->whereDoesntHave('ventas', function ($q) use ($fechaLimite) {
                $q->where('fecha_hora', '>=', $fechaLimite)
                    ->where('estado_pago', 'PAGADA');
            })
            ->get();

        foreach ($productosSinMovimiento as $producto) {
            $existeAlerta = Alerta::where('empresa_id', $empresa->id)
                ->where('categoria', 'INVENTARIO')
                ->where('datos->producto_id', $producto->id)
                ->where('titulo', 'LIKE', '%sin ventas%')
                ->where('resuelta', false)
                ->where('created_at', '>=', Carbon::now()->subDays(7))
                ->exists();

            if (!$existeAlerta) {
                Alerta::create([
                    'empresa_id' => $empresa->id,
                    'tipo' => 'ADVERTENCIA',
                    'categoria' => 'INVENTARIO',
                    'titulo' => "Producto sin ventas: {$producto->nombre}",
                    'mensaje' => "El producto {$producto->nombre} no ha tenido ventas en los últimos 7 días. Considere ajustar precio o promocionarlo.",
                    'datos' => [
                        'producto_id' => $producto->id,
                        'stock_actual' => $producto->stock_actual,
                    ],
                ]);
            }
        }
    }

    private function generarAlertasOcupacion($empresa)
    {
        // ADVERTENCIA: Funciones con baja ocupación próximas a iniciar
        $funcionesProximas = Funcion::where('empresa_id', $empresa->id)
            ->whereBetween('fecha_hora', [Carbon::now()->addHour(), Carbon::now()->addHours(3)])
            ->get();

        foreach ($funcionesProximas as $funcion) {
            $totalAsientos = $funcion->sala->capacidad_total ?? 0;
            $vendidos = FuncionAsiento::where('funcion_id', $funcion->id)
                ->where('estado', 'VENDIDO')
                ->count();

            $ocupacion = $totalAsientos > 0 ? ($vendidos / $totalAsientos) * 100 : 0;

            if ($ocupacion < 30) {
                $existeAlerta = Alerta::where('empresa_id', $empresa->id)
                    ->where('categoria', 'OCUPACION')
                    ->where('datos->funcion_id', $funcion->id)
                    ->where('resuelta', false)
                    ->exists();

                if (!$existeAlerta) {
                    $tituloPelicula = $funcion->pelicula->titulo ?? 'Sin título';
                    $mensaje = "La función de '{$tituloPelicula}' a las {$funcion->fecha_hora->format('H:i')} tiene solo {$ocupacion}% de ocupación. Inicia en " . $funcion->fecha_hora->diffForHumans() . ".";

                    Alerta::create([
                        'empresa_id' => $empresa->id,
                        'tipo' => 'ADVERTENCIA',
                        'categoria' => 'OCUPACION',
                        'titulo' => "Baja ocupación: {$tituloPelicula}",
                        'mensaje' => $mensaje,
                        'datos' => [
                            'funcion_id' => $funcion->id,
                            'ocupacion' => round($ocupacion, 1),
                            'vendidos' => $vendidos,
                            'total' => $totalAsientos,
                        ],
                    ]);
                }
            }
        }
    }

    private function generarAlertasCaja($empresa)
    {
        // CRÍTICO: Cajas cerradas con diferencias grandes
        $cajasConDiferencia = Caja::where('empresa_id', $empresa->id)
            ->where('estado', 'CERRADA')
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->whereRaw('ABS(diferencia) > 20000') // Diferencia de 20mil pesos (configurable)
            ->get();

        foreach ($cajasConDiferencia as $caja) {
            $existeAlerta = Alerta::where('empresa_id', $empresa->id)
                ->where('categoria', 'CAJA')
                ->where('datos->caja_id', $caja->id)
                ->exists();

            if (!$existeAlerta) {
                Alerta::create([
                    'empresa_id' => $empresa->id,
                    'tipo' => 'CRITICA',
                    'categoria' => 'CAJA',
                    'titulo' => "Diferencia significativa en Caja #{$caja->id}",
                    'mensaje' => "La caja #{$caja->id} cerrada el {$caja->fecha_cierre->format('d/m/Y')} tiene una diferencia de \$" . number_format(abs($caja->diferencia), 0) . " (" . ($caja->diferencia > 0 ? 'sobrante' : 'faltante') . ").",
                    'datos' => [
                        'caja_id' => $caja->id,
                        'diferencia' => $caja->diferencia,
                    ],
                ]);
            }
        }
    }

    private function generarAlertasPrecios($empresa)
    {
        // CRÍTICO: Productos con margen negativo
        $productosMargenNegativo = Producto::where('empresa_id', $empresa->id)
            ->where('es_venta_retail', true)
            ->whereNotNull('costo_total_unitario')
            ->whereRaw('precio < costo_total_unitario')
            ->get();

        foreach ($productosMargenNegativo as $producto) {
            $existeAlerta = Alerta::where('empresa_id', $empresa->id)
                ->where('categoria', 'PRECIO')
                ->where('datos->producto_id', $producto->id)
                ->where('resuelta', false)
                ->exists();

            if (!$existeAlerta) {
                Alerta::create([
                    'empresa_id' => $empresa->id,
                    'tipo' => 'CRITICA',
                    'categoria' => 'PRECIO',
                    'titulo' => "Margen negativo: {$producto->nombre}",
                    'mensaje' => "El producto {$producto->nombre} se vende a \$" . number_format($producto->precio, 0) . " pero su costo es \$" . number_format($producto->costo_total_unitario, 0) . ". Estás perdiendo dinero en cada venta.",
                    'datos' => [
                        'producto_id' => $producto->id,
                        'precio' => $producto->precio,
                        'costo' => $producto->costo_total_unitario,
                    ],
                ]);
            }
        }
    }
}
