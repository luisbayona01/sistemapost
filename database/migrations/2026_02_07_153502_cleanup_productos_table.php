<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * Ahora es seguro limpiar las columnas antiguas
     */
    public function up(): void
    {
        // Eliminar producto_id de funciones
        Schema::table('funciones', function (Blueprint $table) {
            // Intentar dropear FK primero. El nombre suele ser tabla_columna_foreign
            $table->dropForeign(['producto_id']);
            $table->dropColumn('producto_id');
        });

        // Eliminar columnas de productos
        Schema::table('productos', function (Blueprint $table) {
            $table->dropForeign(['distribuidor_id']);

            $table->dropColumn([
                'sinopsis',
                'duracion',
                'clasificacion',
                'genero',
                'trailer_url',
                'distribuidor_id',
                'fecha_estreno',
                'fecha_fin_exhibicion'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('funciones', function (Blueprint $table) {
            $table->foreignId('producto_id')->nullable()->constrained()->cascadeOnDelete();
        });

        Schema::table('productos', function (Blueprint $table) {
            $table->text('sinopsis')->nullable();
            $table->foreignId('distribuidor_id')->nullable()->constrained('distribuidores');
            // ... otros campos
        });
    }
};
