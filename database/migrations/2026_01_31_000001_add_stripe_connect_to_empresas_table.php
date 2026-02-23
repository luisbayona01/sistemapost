<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * Agregar campos para Stripe Connect a la tabla empresas
     * Permite que cada empresa tenga su propia cuenta de Stripe Connect
     * y reciba pagos directamente en su cuenta bancaria.
     */
    public function up(): void
    {
        Schema::table('empresa', function (Blueprint $table) {
            // Stripe Account ID para Stripe Connect (acct_xxxx...)
            $table->string('stripe_account_id', 191)
                ->nullable()
                ->unique()
                ->after('ruc')
                ->comment('Stripe Connected Account ID (acct_...) para Split Payments');

            // Estado del onboarding en Stripe Connect
            $table->string('stripe_connect_status', 50)
                ->default('NOT_STARTED') // NOT_STARTED|PENDING|ACTIVE|REJECTED|UNDER_REVIEW
                ->after('stripe_account_id')
                ->comment('Estado del onboarding: NOT_STARTED|PENDING|ACTIVE|REJECTED|UNDER_REVIEW');

            // URL para completar onboarding
            $table->text('stripe_onboarding_url')
                ->nullable()
                ->after('stripe_connect_status')
                ->comment('URL para que el usuario complete el onboarding de Stripe Connect');

            // Fecha del último refresh del estado
            $table->timestamp('stripe_connect_updated_at')
                ->nullable()
                ->after('stripe_onboarding_url')
                ->comment('Última fecha de actualización del estado de Stripe Connect');

            // Índices para queries frecuentes
            $table->index(['stripe_account_id']);
            $table->index(['stripe_connect_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empresa', function (Blueprint $table) {
            $table->dropIndex(['stripe_account_id']);
            $table->dropIndex(['stripe_connect_status']);
            $table->dropColumn([
                'stripe_account_id',
                'stripe_connect_status',
                'stripe_onboarding_url',
                'stripe_connect_updated_at',
            ]);
        });
    }
};
