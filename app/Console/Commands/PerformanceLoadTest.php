<?php

namespace App\Console\Commands;

use App\Models\Caja;
use App\Models\Producto;
use App\Models\User;
use App\Models\Venta;
use App\Models\Insumo;
use App\Models\InsumoLote;
use App\Services\POS\VentaService;
use App\Http\Controllers\POS\CashierController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PerformanceLoadTest extends Command
{
    protected $signature = 'test:load {sales=100}';
    protected $description = 'Simular carga masiva de ventas para validar numeración e incentario';

    public function handle()
    {
        $numSales = $this->argument('sales');
        $this->info("Iniciando prueba de carga: Simulación de $numSales ventas consecutivas...");

        // 1. Setup Environment
        $user = User::whereHas('roles', fn($q) => $q->where('name', 'Root'))->first();
        if (!$user) {
            $this->error("No se encontró usuario Root para la prueba.");
            return;
        }
        Auth::login($user);

        $empresa = $user->empresa;
        $caja = Caja::where('empresa_id', $empresa->id)->where('estado', 'ABIERTA')->first();

        if (!$caja) {
            $this->error("No hay una caja abierta para realizar la prueba.");
            return;
        }

        $producto = Producto::where('empresa_id', $empresa->id)->where('es_venta_retail', true)->first();
        if (!$producto) {
            $this->error("No hay un producto retail para la prueba.");
            return;
        }

        $this->info("Usando Caja #{$caja->id} y Producto: {$producto->nombre}");

        $startTime = microtime(true);
        $errors = 0;
        $duplicates = 0;
        $consecutivos = [];

        $bar = $this->output->createProgressBar($numSales);
        $bar->start();

        for ($i = 0; $i < $numSales; $i++) {
            try {
                DB::beginTransaction();

                // Simulamos la lógica de CashierController
                $tipoComprobante = 'Factura';
                $comprobanteId = 1;

                $venta = new Venta([
                    'empresa_id' => $empresa->id,
                    'user_id' => $user->id,
                    'caja_id' => $caja->id,
                    'cliente_id' => 1, // Genérico
                    'comprobante_id' => $comprobanteId,
                    'metodo_pago' => 'EFECTIVO',
                    'canal' => 'confiteria',
                    'tipo_venta' => 'FISICA',
                    'total_final' => 1000,
                    'subtotal' => 925.93,
                    'inc_total' => 74.07,
                    'iva_total' => 0,
                    'estado_pago' => 'PAGADA',
                    'fecha_operativa' => now()->format('Y-m-d'),
                ]);

                // Generamos número (aquí es donde probamos la colisión)
                $venta->numero_comprobante = $venta->generarNumeroVenta($caja->id, $tipoComprobante);
                $venta->save();

                // Validamos duplicados en memoria
                if (in_array($venta->consecutivo_pos, $consecutivos)) {
                    $duplicates++;
                }
                $consecutivos[] = $venta->consecutivo_pos;

                // Simulamos attach de productos (esto dispara el Observer/Listener)
                $venta->productos()->attach($producto->id, [
                    'cantidad' => 1,
                    'precio_venta' => 1000,
                    'tarifa_unitaria' => 0
                ]);

                // En un flujo real, el evento CreateVentaEvent dispararía el inventario.
                // Como estamos en un comando, nos aseguramos que el listener procese.
                // Event::dispatch(new \App\Events\CreateVentaEvent($venta));

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                $errors++;
                Log::error("Error en LoadTest: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $endTime = microtime(true);
        $totalTime = round($endTime - $startTime, 2);

        $this->newLine(2);
        $this->table(
            ['Métrica', 'Resultado'],
            [
                ['Ventas Procesadas', $numSales - $errors],
                ['Errores', $errors],
                ['Duplicados Detectados', $duplicates],
                ['Tiempo Total (seg)', $totalTime],
                ['Promedio por Venta (seg)', round($totalTime / $numSales, 3)]
            ]
        );

        if ($duplicates > 0) {
            $this->warn("⚠️ ADVERTENCIA: Se detectaron $duplicates colisiones de numeración. La lógica de generación necesita bloqueos (locks).");
        } else {
            $this->info("✅ ÉXITO: Numeración consistente.");
        }
    }
}
