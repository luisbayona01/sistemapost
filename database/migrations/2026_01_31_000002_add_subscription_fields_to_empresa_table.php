<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('empresa', function (Blueprint $table) {
            // Relación con planes SaaS
            $table->foreignId('plan_id')->nullable()->after('id')->constrained('saas_plans')->nullOnDelete();

            // Datos de Stripe
            $table->string('stripe_subscription_id')->nullable()->after('plan_id')->unique();
            $table->string('stripe_customer_id')->nullable()->after('stripe_subscription_id');

            // Estado de suscripción
            $table->enum('estado_suscripcion', ['active', 'cancelled', 'past_due', 'trial'])->default('active')->after('stripe_customer_id');
            $table->timestamp('fecha_proximo_pago')->nullable()->after('estado_suscripcion');
            $table->timestamp('fecha_vencimiento_suscripcion')->nullable()->after('fecha_proximo_pago');

            // Tarifa por transacción
            $table->decimal('tarifa_servicio_porcentaje', 5, 2)->default(2.50)->after('fecha_vencimiento_suscripcion');
            $table->decimal('tarifa_servicio_monto', 15, 2)->default(0)->after('tarifa_servicio_porcentaje');

            // Estado de empresa (para super-admin)
            $table->enum('estado', ['activa', 'suspendida'])->default('activa')->after('tarifa_servicio_monto');

            // Timestamps del onboarding
            $table->timestamp('fecha_onboarding_completado')->nullable()->after('estado');

            // Índices
            $table->index('plan_id');
            $table->index('estado_suscripcion');
            $table->index('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empresa', function (Blueprint $table) {
            $table->dropForeign(['plan_id']);
            $table->dropIndex(['plan_id']);
            $table->dropIndex(['estado_suscripcion']);
            $table->dropIndex(['estado']);
            $table->dropColumn([
                'plan_id',
                'stripe_subscription_id',
                'stripe_customer_id',
                'estado_suscripcion',
                'fecha_proximo_pago',
                'fecha_vencimiento_suscripcion',
                'tarifa_servicio_porcentaje',
                'tarifa_servicio_monto',
                'estado',
                'fecha_onboarding_completado',
            ]);
        });
    }
};
