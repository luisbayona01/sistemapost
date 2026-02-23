<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * FASE 4.1: Prevención de Duplicación de Movimientos
 * 
 * Agrega un timestamp para rastrear cuándo se creó el movimiento de caja
 * asociado a una venta. Esto previene la duplicación de movimientos si
 * el listener se ejecuta múltiples veces (idempotencia).
 */
return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->timestamp('movimiento_creado_at')->nullable()->after('inventario_descontado_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn('movimiento_creado_at');
        });
    }
};
