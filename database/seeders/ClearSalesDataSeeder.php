<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Venta;
use App\Models\Caja;
use App\Models\Movimiento;
use App\Models\PaymentTransaction;
use App\Models\FuncionAsiento;
use App\Models\PeriodoOperativo;
use App\Models\Kardex;
use App\Enums\TipoTransaccionEnum;

class ClearSalesDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🧹 Limpiando datos de ventas y simulaciones...');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 1. Limpiar Tablas de Ventas
        $this->command->info('🛒 Limpiando ventas...');
        if (Schema::hasTable('producto_venta')) {
            DB::table('producto_venta')->truncate();
        }
        if (Schema::hasTable('venta_funcion_asientos')) {
            DB::table('venta_funcion_asientos')->truncate();
        }
        if (Schema::hasTable('ventas')) {
            DB::table('ventas')->truncate();
        }

        // 2. Limpiar Movimientos y Pagos
        $this->command->info('💸 Limpiando movimientos y pagos...');
        if (Schema::hasTable('movimientos_caja')) {
            DB::table('movimientos_caja')->truncate();
        }
        if (Schema::hasTable('movimientos')) {
            DB::table('movimientos')->truncate();
        }
        if (Schema::hasTable('pagos')) {
            DB::table('pagos')->truncate();
        }
        if (Schema::hasTable('payment_transactions')) {
            DB::table('payment_transactions')->truncate();
        }

        // 3. Limpiar Facturación Fiscal
        $this->command->info('🧾 Limpiando documentos fiscales...');
        if (Schema::hasTable('documento_fiscal_lineas')) {
            DB::table('documento_fiscal_lineas')->truncate();
        }
        if (Schema::hasTable('documentos_fiscales')) {
            DB::table('documentos_fiscales')->truncate();
        }

        // 4. Limpiar Devoluciones
        if (Schema::hasTable('devolucion_items')) {
            DB::table('devolucion_items')->truncate();
        }
        if (Schema::hasTable('devoluciones')) {
            DB::table('devoluciones')->truncate();
        }

        // 5. Resetear Asientos de Funciones
        $this->command->info('💺 Reseteando asientos a DISPONIBLE...');
        FuncionAsiento::query()->update([
            'estado' => FuncionAsiento::ESTADO_DISPONIBLE,
            'venta_id' => null,
            'reservado_por' => null,
            'reservado_hasta' => null,
            'session_id' => null
        ]);

        // 6. Limpiar Kardex de Ventas
        $this->command->info('📦 Limpiando movimientos de Kardex por ventas...');
        // Borramos específicamente los registros de Venta y Devolución
        Kardex::whereIn('tipo_transaccion', [
            TipoTransaccionEnum::Venta,
            TipoTransaccionEnum::Devolucion
        ])->delete();

        // 7. Resetear Cajas
        $this->command->info('💰 Reseteando cajas...');
        Caja::truncate();

        // 8. Resetear Periodos Operativos
        $this->command->info('📅 Reseteando periodos operativos...');
        PeriodoOperativo::truncate();

        // 9. Limpiar Alertas
        if (Schema::hasTable('alertas')) {
            DB::table('alertas')->truncate();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('✅ Datos de ventas y simulaciones eliminados. Maestro (Productos, Películas, Insumos) PRESERVADOS.');
    }
}
