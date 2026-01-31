<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Agrega:
     * - empresa_id: Identificar a qué empresa pertenece la venta
     * - tarifa_servicio: Porcentaje de tarifa (ej: 3.50%)
     * - monto_tarifa: Monto en dinero calculado de la tarifa
     * - stripe_payment_intent_id: Para integración Stripe futura
     */
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->foreignId('empresa_id')
                ->after('id')
                ->constrained('empresa')
                ->cascadeOnDelete();

            $table->decimal('tarifa_servicio', 5, 2)
                ->default(0)
                ->after('total')
                ->comment('Porcentaje de tarifa por servicio');

            $table->decimal('monto_tarifa', 10, 2)
                ->default(0)
                ->after('tarifa_servicio')
                ->comment('Monto de la tarifa calculada');

            $table->string('stripe_payment_intent_id', 255)
                ->nullable()
                ->after('monto_tarifa')
                ->comment('ID del payment intent de Stripe');

            // Índice para búsquedas por empresa
            $table->index(['empresa_id', 'fecha_hora']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropIndex(['empresa_id', 'fecha_hora']);
            $table->dropColumn('stripe_payment_intent_id');
            $table->dropColumn('monto_tarifa');
            $table->dropColumn('tarifa_servicio');
            $table->dropForeign(['empresa_id']);
            $table->dropColumn('empresa_id');
        });
    }
};
