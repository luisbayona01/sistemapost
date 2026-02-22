<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Caja;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class CajerosTestSeeder extends Seeder
{
    /**
     * Crear cajeros genéricos para testing
     */
    public function run(): void
    {
        // Obtener empresa ID (asumiendo que existe al menos una)
        $empresaId = \App\Models\Empresa::first()->id ?? 1;

        // Crear o verificar rol "cajero"
        $rolCajero = Role::firstOrCreate(['name' => 'cajero']);

        // Cajero 1
        $cajero1 = User::firstOrCreate(
            ['email' => 'cajero1@test.com'],
            [
                'name' => 'Cajero Uno',
                'password' => Hash::make('password123'),
                'empresa_id' => $empresaId,
                'estado' => 1, // 1 = activo
            ]
        );
        $cajero1->assignRole($rolCajero);

        // Cajero 2
        $cajero2 = User::firstOrCreate(
            ['email' => 'cajero2@test.com'],
            [
                'name' => 'Cajero Dos',
                'password' => Hash::make('password123'),
                'empresa_id' => $empresaId,
                'estado' => 1, // 1 = activo
            ]
        );
        $cajero2->assignRole($rolCajero);

        $this->command->info('✅ Cajeros de prueba creados:');
        $this->command->info('   - cajero1@test.com / password123');
        $this->command->info('   - cajero2@test.com / password123');

        // Crear cajas predefinidas (CERRADAS por defecto)
        // Desactivar observers para evitar error de Auth::user() en seeder
        Caja::withoutEvents(function () use ($empresaId, $cajero1, $cajero2) {
            Caja::updateOrCreate(
                ['nombre' => 'Caja Principal'],
                [
                    'empresa_id' => $empresaId,
                    'user_id' => $cajero1->id,
                    'fecha_apertura' => now()->subDays(1),
                    'fecha_cierre' => now()->subDays(1)->addHours(8),
                    'monto_inicial' => 50000,
                    'estado' => 'CERRADA',
                    'monto_final_declarado' => 250000,
                    'monto_final_esperado' => 248000,
                    'diferencia' => 2000,
                ]
            );

            Caja::updateOrCreate(
                ['nombre' => 'Caja Secundaria'],
                [
                    'empresa_id' => $empresaId,
                    'user_id' => $cajero2->id,
                    'fecha_apertura' => now()->subDays(2),
                    'fecha_cierre' => now()->subDays(2)->addHours(8),
                    'monto_inicial' => 30000,
                    'estado' => 'CERRADA',
                    'monto_final_declarado' => 180000,
                    'monto_final_esperado' => 180000,
                    'diferencia' => 0,
                ]
            );
        });

        $this->command->info('✅ Cajas de prueba creadas (cerradas por defecto)');
        $this->command->warn('⚠️  Los cajeros deben ABRIR UNA CAJA manualmente antes de vender.');
    }
}
