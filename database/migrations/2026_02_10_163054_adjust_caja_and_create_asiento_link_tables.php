<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Added this line for DB::statement

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cajas', function (Blueprint $table) {
            $table->decimal('monto_esperado', 10, 2)->nullable()->after('saldo_final');
            $table->decimal('diferencia', 10, 2)->nullable()->after('monto_esperado');
            $table->foreignId('cerrado_por')->nullable()->constrained('users')->after('user_id');
            // Cambiar estado a string para soportar ABIERTA/CERRADA
            $table->string('estado', 20)->default('ABIERTA')->change();
        });

        Schema::create('venta_funcion_asientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained('ventas')->onDelete('cascade');
            $table->foreignId('funcion_asiento_id')->constrained('funcion_asientos')->onDelete('cascade');
            $table->timestamps();
        });

        // Asegurar que funcion_asientos tenga el enum correcto (MySQL specific statement)
        DB::statement("ALTER TABLE funcion_asientos MODIFY COLUMN estado ENUM('DISPONIBLE', 'RESERVADO_TEMPORAL', 'VENDIDO', 'BLOQUEADO', 'OCUPADO') NOT NULL DEFAULT 'DISPONIBLE'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venta_funcion_asientos');
        Schema::table('cajas', function (Blueprint $table) {
            $table->dropForeign(['cerrado_por']);
            $table->dropColumn(['monto_esperado', 'diferencia', 'cerrado_por']);
            // El estado volvería a booleano pero es complejo revertir sin perder datos si son strings
        });
    }
};
