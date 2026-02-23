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
        Schema::create('precios_entradas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresa')->cascadeOnDelete();
            $table->string('nombre'); // General, Niños, Adulto Mayor
            $table->decimal('precio', 15, 2);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // Relacionar precios con funciones (Many to Many o cada función tiene sus precios)
        Schema::create('funcion_precio', function (Blueprint $table) {
            $table->id();
            $table->foreignId('funcion_id')->constrained('funciones')->cascadeOnDelete();
            $table->foreignId('precio_entrada_id')->constrained('precios_entradas')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('funcion_precio');
        Schema::dropIfExists('precios_entradas');
    }
};
