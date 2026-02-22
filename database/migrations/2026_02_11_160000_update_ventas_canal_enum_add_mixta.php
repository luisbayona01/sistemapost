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
        // Agregar 'mixta' al enum de la columna canal
        DB::statement("ALTER TABLE ventas MODIFY COLUMN canal ENUM('ventanilla', 'confiteria', 'web', 'mixta') NOT NULL DEFAULT 'confiteria'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir cambios (cuidado si hay datos 'mixta', podrían truncarse o fallar)
        // En un rollback real, deberíamos decidir qué hacer con los 'mixta'.
        // Por ahora, simplemente revertimos la definición del enum si es posible.

        // Opción segura: Convertir 'mixta' a 'confiteria' antes de revertir
        DB::table('ventas')->where('canal', 'mixta')->update(['canal' => 'confiteria']);

        DB::statement("ALTER TABLE ventas MODIFY COLUMN canal ENUM('ventanilla', 'confiteria', 'web') NOT NULL DEFAULT 'confiteria'");
    }
};
