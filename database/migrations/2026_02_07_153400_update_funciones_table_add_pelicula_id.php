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
        Schema::table('funciones', function (Blueprint $table) {
            // Agregar la nueva columna pelicula_id
            $table->foreignId('pelicula_id')->nullable()->after('sala_id')->constrained('peliculas')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('funciones', function (Blueprint $table) {
            $table->dropForeign(['pelicula_id']);
            $table->dropColumn('pelicula_id');
        });
    }
};
