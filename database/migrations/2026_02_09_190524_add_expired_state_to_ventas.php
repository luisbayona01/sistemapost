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
        // Modificar enum de estado_pago para incluir EXPIRADA
        DB::statement("ALTER TABLE ventas MODIFY COLUMN estado_pago ENUM('PENDIENTE', 'PAGADA', 'FALLIDA', 'EXPIRADA', 'CANCELADA') NOT NULL DEFAULT 'PENDIENTE'");

        // Agregar índice para búsquedas rápidas de ventas pendientes
        Schema::table('ventas', function (Blueprint $table) {
            $table->index(['estado_pago', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropIndex(['estado_pago', 'created_at']);
        });

        DB::statement("ALTER TABLE ventas MODIFY COLUMN estado_pago ENUM('PENDIENTE', 'PAGADA', 'FALLIDA', 'CANCELADA') NOT NULL DEFAULT 'PENDIENTE'");
    }
};
