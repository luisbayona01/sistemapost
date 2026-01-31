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
        Schema::create('saas_plans', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->string('stripe_price_id')->nullable();
            $table->decimal('precio_mensual_cop', 15, 2);
            $table->text('descripcion')->nullable();
            $table->json('caracteristicas')->nullable();
            $table->integer('dias_trial')->default(14);
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index('activo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saas_plans');
    }
};
