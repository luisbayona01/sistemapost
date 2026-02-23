<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Limpieza de tabla legacy
        Schema::dropIfExists('venta_pagos');

        // Unificación de tipos de pago en la entidad definitiva
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->string('payment_method')->change(); // Cambiamos a string para mayor flexibilidad o Enum compatible
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            // No revertimos el drop de venta_pagos ya que es una eliminación definitiva
        });
    }
};
