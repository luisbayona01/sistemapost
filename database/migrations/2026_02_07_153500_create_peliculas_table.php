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
        Schema::create('peliculas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresa')->cascadeOnDelete();
            $table->string('titulo');
            $table->text('sinopsis')->nullable();
            $table->string('duracion')->nullable()->comment('En minutos o formato horas:minutos');
            $table->string('clasificacion')->nullable(); // G, PG, PG-13, R, NC-17
            $table->string('genero')->nullable();
            $table->string('afiche')->nullable(); // URL de la imagen/poster
            $table->string('trailer_url')->nullable();
            $table->foreignId('distribuidor_id')->nullable()->constrained('distribuidores')->nullOnDelete();
            $table->date('fecha_estreno')->nullable();
            $table->date('fecha_fin_exhibicion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peliculas');
    }
};
