<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Empresa;
use App\Models\BusinessConfiguration;

class BusinessConfigurationSeeder extends Seeder
{
    public function run(): void
    {
        $empresas = Empresa::all();

        if ($empresas->isEmpty()) {
            $this->command->warn('No hay empresas en el sistema. Crea una empresa primero.');
            return;
        }

        foreach ($empresas as $empresa) {
            BusinessConfiguration::updateOrCreate(
                ['empresa_id' => $empresa->id],
                [
                    'business_type' => 'cinema', // Por defecto todos son cines
                    'modules_enabled' => [
                        'cinema' => true,
                        'pos' => true,
                        'inventory' => true,
                        'reports' => true,
                        'api' => false,
                    ],
                    'settings' => [
                        'currency' => 'COP',
                        'timezone' => 'America/Bogota',
                        'tax_included' => false,
                    ],
                ]
            );
        }

        $this->command->info('✅ Configuraciones de negocio creadas para ' . $empresas->count() . ' empresas');
    }
}
