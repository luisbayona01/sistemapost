<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Add Cinema metadata to productos table
        // We assume Movies are stored in the 'productos' table
        Schema::table('productos', function (Blueprint $table) {
            $table->string('trailer_url', 500)->nullable();
            $table->string('duracion')->nullable(); // e.g., "120 min"
            $table->string('clasificacion')->nullable(); // e.g., "PG-13", "+18"
            $table->enum('genero', ['Accion', 'Comedia', 'Drama', 'Terror', 'Infantil', 'Documental', 'Ciencia Ficcion', 'Otros'])->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn(['trailer_url', 'duracion', 'clasificacion', 'genero']);
        });
    }
};
