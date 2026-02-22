<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Insumo;
use App\Models\Producto;
use App\Models\Kardex;
use Illuminate\Support\Facades\DB;

class ValidateInventoryIntegrity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:validate-inventory-integrity';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Valida la integridad del inventario comparando el stock actual con los registros del Kardex';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Iniciando validación de integridad de inventario...");

        $inconsistencias = 0;

        // 1. Validar Insumos (Materias Primas)
        $this->info("\n--- Validando Insumos ---");
        $insumos = Insumo::all();
        foreach ($insumos as $insumo) {
            $ultimoKardex = Kardex::where('insumo_id', $insumo->id)->orderBy('id', 'desc')->first();
            $saldoKardex = $ultimoKardex ? $ultimoKardex->saldo : 0;

            // Usamos una pequeña tolerancia para decimales si fuera necesario, pero Insumo suele ser float
            if (abs($insumo->stock_actual - $saldoKardex) > 0.001) {
                $this->error("❌ Insumo: {$insumo->nombre} (ID: {$insumo->id})");
                $this->line("   - Stock Actual: {$insumo->stock_actual}");
                $this->line("   - Saldo Kardex: {$saldoKardex}");
                $inconsistencias++;
            }
        }

        // 2. Validar Productos Retail (Snacks/Bebidas con stock directo)
        $this->info("\n--- Validando Productos Retail ---");
        $productosRetail = Producto::retail()->get();
        foreach ($productosRetail as $producto) {
            // Un producto retail puede tener inventario directo o depender de insumos.
            // Si tiene registro en la tabla 'inventario', validamos contra Kardex de producto.
            $stockActual = $producto->inventario->cantidad ?? 0;
            $ultimoKardex = Kardex::where('producto_id', $producto->id)->orderBy('id', 'desc')->first();
            $saldoKardex = $ultimoKardex ? $ultimoKardex->saldo : 0;

            if ($stockActual != $saldoKardex) {
                $this->error("❌ Producto Retail: {$producto->nombre} (ID: {$producto->id})");
                $this->line("   - Stock Actual: {$stockActual}");
                $this->line("   - Saldo Kardex: {$saldoKardex}");
                $inconsistencias++;
            }
        }

        // 3. Excluir específicamente productos de servicio (Confirmación visual)
        $this->info("\n--- Verificando Exclusión de Servicios (Tickets) ---");
        $ticketsCount = Producto::where('es_venta_retail', false)->count();
        $this->info("✅ Se han excluido {$ticketsCount} productos de tipo servicio/boletería del análisis de integridad física.");

        if ($inconsistencias === 0) {
            $this->info("\n✅ RESULTADO: 0 inconsistencias encontradas. El inventario está íntegro.");
            return 0;
        } else {
            $this->warn("\n⚠️ RESULTADO: Se encontraron {$inconsistencias} inconsistencias.");
            return 1;
        }
    }
}
