<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Tabla para almacenar configuración de Stripe por empresa.
     * Las claves están encriptadas en la BD pero visible en código como medida.
     * En producción, usar un servicio de vault (AWS Secrets Manager, etc).
     */
    public function up(): void
    {
        Schema::create('stripe_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')
                ->unique()
                ->constrained('empresa')
                ->cascadeOnDelete();

            $table->string('public_key', 255)
                ->comment('Stripe Publishable Key (públicamente segura)');

            $table->text('secret_key')
                ->comment('Stripe Secret Key (ENCRIPTADA en valores)');

            $table->text('webhook_secret')
                ->nullable()
                ->comment('Stripe Webhook Signing Secret (ENCRIPTADA)');

            $table->boolean('test_mode')
                ->default(true)
                ->comment('true = test keys, false = live keys');

            $table->boolean('enabled')
                ->default(false)
                ->comment('Si está habilitada la integración Stripe');

            $table->timestamps();

            $table->index(['empresa_id', 'enabled']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stripe_configs');
    }
};
