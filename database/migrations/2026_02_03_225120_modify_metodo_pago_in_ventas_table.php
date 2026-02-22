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
        Schema::table('ventas', function (Blueprint $table) {
            // Cambiamos el enum por un string más flexible o permitimos nulo
            // para que la tabla venta_pagos tome el control de los medios de pago.
            $table->string('metodo_pago')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->enum('metodo_pago', ['EFECTIVO', 'TARJETA'])->nullable(false)->change();
        });
    }
};
