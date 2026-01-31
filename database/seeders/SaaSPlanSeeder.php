<?php

namespace Database\Seeders;

use App\Models\SaaSPlan;
use Illuminate\Database\Seeder;

class SaaSPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $planes = [
            [
                'nombre' => 'Básico',
                'stripe_price_id' => 'price_basic_monthly',
                'precio_mensual_cop' => 299000,
                'descripcion' => 'Plan básico para pequeños cines',
                'dias_trial' => 14,
                'caracteristicas' => [
                    'Hasta 1 caja',
                    'POS completo',
                    'Reportes básicos',
                    'Soporte por email',
                    'Tarifa: 2.5%',
                ],
                'activo' => true,
            ],
            [
                'nombre' => 'Profesional',
                'stripe_price_id' => 'price_pro_monthly',
                'precio_mensual_cop' => 399000,
                'descripcion' => 'Plan profesional para medianas/grandes empresas',
                'dias_trial' => 14,
                'caracteristicas' => [
                    'Hasta 5 cajas',
                    'POS avanzado',
                    'Reportes avanzados',
                    'API REST',
                    'Integración Stripe',
                    'Soporte prioritario',
                    'Tarifa: 2.0%',
                ],
                'activo' => true,
            ],
            [
                'nombre' => 'Empresa',
                'stripe_price_id' => 'price_enterprise_monthly',
                'precio_mensual_cop' => 599000,
                'descripcion' => 'Plan empresarial con soporte dedicado',
                'dias_trial' => 30,
                'caracteristicas' => [
                    'Cajas ilimitadas',
                    'POS premium',
                    'Reportes personalizados',
                    'API REST avanzada',
                    'Analytics en tiempo real',
                    'Soporte 24/7',
                    'Tarifa personalizada',
                    'Cuenta dedicada',
                ],
                'activo' => true,
            ],
        ];

        foreach ($planes as $plan) {
            SaaSPlan::firstOrCreate(
                ['nombre' => $plan['nombre']],
                $plan
            );
        }

        $this->command->info('✅ SaaS Plans creados exitosamente');
    }
}
