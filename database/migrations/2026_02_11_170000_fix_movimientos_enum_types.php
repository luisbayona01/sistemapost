<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Actualizar columna 'tipo' para incluir INGRESO, EGRESO, CORTESIA, BAJA
        // Y cambiar metodo_pago para incluir TRANSFERENCIA, QR, MIXTO, STRIPE

        // Primero nos aseguramos de que no haya nulos si vamos a cambiar a NOT NULL
        DB::statement("ALTER TABLE movimientos MODIFY COLUMN tipo ENUM('VENTA', 'RETIRO', 'INGRESO', 'EGRESO', 'CORTESIA', 'BAJA') NOT NULL");
        DB::statement("ALTER TABLE movimientos MODIFY COLUMN metodo_pago ENUM('EFECTIVO', 'TARJETA', 'TRANSFERENCIA', 'QR', 'MIXTO', 'STRIPE') NOT NULL");

        // Agregar campo user_id si no existe (la migración original no lo tenía, pero VentaService lo usa)
        if (!Schema::hasColumn('movimientos', 'user_id')) {
            Schema::table('movimientos', function (Blueprint $table) {
                $table->foreignId('user_id')->nullable()->after('venta_id')->constrained()->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE movimientos MODIFY COLUMN tipo ENUM('VENTA', 'RETIRO') NOT NULL");
        DB::statement("ALTER TABLE movimientos MODIFY COLUMN metodo_pago ENUM('EFECTIVO', 'TARJETA') NOT NULL");

        if (Schema::hasColumn('movimientos', 'user_id')) {
            Schema::table('movimientos', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            });
        }
    }
};
