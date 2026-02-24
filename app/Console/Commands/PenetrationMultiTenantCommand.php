<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Venta;
use App\Models\Producto;
use App\Models\Empresa;
use App\Models\Rule;
use App\Models\DocumentoFiscal;
use App\Models\FuncionAsiento;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PenetrationMultiTenantCommand extends Command
{
    protected $signature = 'test:multi-tenant';
    protected $description = 'Simula ataques de penetración cruzada entre tenants';

    public function handle()
    {
        $this->info('🛡️ Iniciando Smoke Tests de Penetración Multi-Tenant...');

        // 1. Setup: Asegurar 2 empresas con datos
        $empresaA = Empresa::firstOrCreate(['id' => 1], ['nombre' => 'Cine A', 'slug' => 'cine-a']);
        $empresaB = Empresa::firstOrCreate(['id' => 2], ['nombre' => 'Cine B', 'slug' => 'cine-b']);

        $userA = User::where('empresa_id', 1)->first();
        if (!$userA) {
            $this->error('Falta usuario en Empresa 1 para el test.');
            return;
        }

        // Crear una Venta en Empresa B para intentar "robarla"
        DB::table('ventas')->insertOrIgnore([
            'id' => 9999,
            'empresa_id' => 2,
            'user_id' => $userA->id,
            'total' => 100,
            'estado_pago' => 'PAGADA',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Crear una Regla en Empresa B
        DB::table('rules')->insertOrIgnore([
            'id' => 9999,
            'empresa_id' => 2,
            'name' => 'Regla Secreta B',
            'event_type' => 'VENTA_CREADA',
            'active' => 1,
        ]);

        $this->info("Simulando sesión de Usuario: {$userA->name} (Empresa: {$userA->empresa_id})");
        Auth::login($userA);

        // --- TEST 1: Acceso a Ventas ---
        $this->line("\n--- Test 1: Intento de lectura de Venta ajena ---");
        $ventaAjena = Venta::find(9999);
        if ($ventaAjena) {
            $this->error("❌ FALLO: Se logró leer una Venta de la Empresa 2");
        } else {
            $this->info("✅ OK: Venta de Empresa 2 invisible para Empresa 1");
        }

        // --- TEST 2: Acceso a Reglas ---
        $this->line("\n--- Test 2: Intento de lectura de Reglas ajenas ---");
        $reglaAjena = Rule::find(9999);
        if ($reglaAjena) {
            $this->error("❌ FALLO: Se logró leer una Regla de la Empresa 2");
        } else {
            $this->info("✅ OK: Regla de Empresa 2 invisible para Empresa 1");
        }

        // --- TEST 3: Intento de update cruzado ---
        $this->line("\n--- Test 3: Intento de actualización cruzada ---");
        try {
            // Intento vía Eloquent update masivo
            $affected = Venta::where('id', 9999)->update(['total' => 400]);

            if ($affected > 0) {
                $this->error("❌ FALLO: Eloquent permitió update masivo en registro ajeno");
            } else {
                $this->info("✅ OK: Eloquent bloqueó update en registro ajeno");
            }
        } catch (\Exception $e) {
            $this->info("✅ OK: Error al intentar update: " . $e->getMessage());
        }

        // --- TEST 4: Bloqueo de Asientos ---
        $this->line("\n--- Test 4: Aislamiento de Asientos ---");
        // Insertar asiento en B
        DB::table('funcion_asientos')->insertOrIgnore([
            'id' => 9999,
            'empresa_id' => 2,
            'funcion_id' => 1,
            'codigo_asiento' => 'TEST-B',
            'estado' => 'VENDIDO',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if (FuncionAsiento::find(9999)) {
            $this->error("❌ FALLO: Se logró ver un asiento de la Empresa 2");
        } else {
            $this->info("✅ OK: Asientos aislados correctamente");
        }

        $this->info("\n🏁 Smoke Test Finalizado.");
        Auth::logout();
    }
}
