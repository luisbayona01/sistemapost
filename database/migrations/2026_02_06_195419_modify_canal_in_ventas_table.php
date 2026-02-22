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
        // 1. Convertir valores existentes antes del cambio de tipo
        DB::table('ventas')->where('canal', 'POS')->update(['canal' => 'confiteria']);
        DB::table('ventas')->where('canal', 'WEB')->update(['canal' => 'web']);

        // Intentar identificar ventas de cine para marcarlas como ventanilla
        DB::statement("
            UPDATE ventas 
            SET canal = 'ventanilla' 
            WHERE canal = 'confiteria' 
            AND id IN (SELECT DISTINCT venta_id FROM funcion_asientos)
        ");

        Schema::table('ventas', function (Blueprint $table) {
            $table->enum('canal', ['ventanilla', 'confiteria', 'web'])
                ->default('confiteria')
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->string('canal')->default('POS')->change();
        });
    }
};
