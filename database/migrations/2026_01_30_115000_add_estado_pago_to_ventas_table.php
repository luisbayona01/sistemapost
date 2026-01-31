<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Agrega campo estado_pago para rastrear el estado del pago Stripe
     */
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->enum('estado_pago', ['PENDIENTE', 'PAGADA', 'FALLIDA', 'CANCELADA'])
                ->default('PENDIENTE')
                ->after('stripe_payment_intent_id')
                ->comment('Estado del pago: PENDIENTE, PAGADA, FALLIDA, CANCELADA');

            // Índice para búsquedas por estado de pago
            $table->index(['empresa_id', 'estado_pago']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropIndex(['empresa_id', 'estado_pago']);
            $table->dropColumn('estado_pago');
        });
    }
};
