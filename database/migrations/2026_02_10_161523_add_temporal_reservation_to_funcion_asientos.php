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
        Schema::table('funcion_asientos', function (Blueprint $table) {
            DB::statement("ALTER TABLE funcion_asientos MODIFY COLUMN estado ENUM('DISPONIBLE', 'RESERVADO_TEMPORAL', 'VENDIDO', 'BLOQUEADO', 'OCUPADO') NOT NULL DEFAULT 'DISPONIBLE'");
            $table->timestamp('reservado_hasta')->nullable()->after('estado');
            $table->foreignId('reservado_por')->nullable()->constrained('users')->after('reservado_hasta');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('funcion_asientos', function (Blueprint $table) {
            $table->dropForeign(['reservado_por']);
            $table->dropColumn(['reservado_hasta', 'reservado_por']);
            DB::statement("ALTER TABLE funcion_asientos MODIFY COLUMN estado ENUM('DISPONIBLE', 'VENDIDO', 'BLOQUEADO', 'OCUPADO') NOT NULL DEFAULT 'DISPONIBLE'");
        });
    }
};
