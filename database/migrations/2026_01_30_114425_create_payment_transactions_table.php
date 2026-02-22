<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * Tabla para registrar todas las transacciones de pago.
     * Soporta múltiples métodos: efectivo, tarjeta, Stripe, etc.
     * Permite auditoría completa y reconciliación.
     *
     * Diseño:
     * - Una venta puede tener múltiples transacciones (split payment)
     * - Cada transacción registra el método de pago usado
     * - Si es Stripe, se registran los IDs de Stripe para seguimiento
     * - Campos status para controlar el flujo de pago
     */
    public function up(): void
    {
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')
                ->constrained('empresa')
                ->cascadeOnDelete();

            $table->foreignId('venta_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('payment_method', ['CASH', 'CARD', 'STRIPE', 'OTHER'])
                ->default('CASH')
                ->comment('Método de pago: efectivo, tarjeta física, Stripe, etc');

            $table->string('stripe_payment_intent_id', 191)
                ->nullable()
                ->comment('ID del PaymentIntent de Stripe (si aplica)');

            $table->string('stripe_charge_id', 191)
                ->nullable()
                ->comment('ID de la carga de Stripe después de confirmada');

            $table->decimal('amount_paid', 10, 2)
                ->comment('Monto pagado en esta transacción');

            $table->string('currency', 3)
                ->default('USD')
                ->comment('Moneda del pago');

            $table->enum('status', ['PENDING', 'SUCCESS', 'FAILED', 'REFUNDED', 'CANCELLED'])
                ->default('PENDING')
                ->comment('Estado del pago');

            $table->json('metadata')
                ->nullable()
                ->comment('Datos adicionales (referencia externa, notas, etc)');

            $table->text('error_message')
                ->nullable()
                ->comment('Si falló, guardar mensaje de error');

            $table->timestamps();

            // Índices para queries eficientes
            $table->index(['empresa_id', 'venta_id']);
            $table->index(['empresa_id', 'status']);
            $table->index(['stripe_payment_intent_id']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
