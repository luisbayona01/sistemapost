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
        Schema::create('salas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresa')->cascadeOnDelete();
            $table->string('nombre');
            $table->json('configuracion_json');
            $table->integer('capacidad');
            $table->timestamps();
        });

        Schema::create('funciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresa')->cascadeOnDelete();
            $table->foreignId('sala_id')->constrained()->cascadeOnDelete();
            $table->foreignId('producto_id')->constrained()->cascadeOnDelete();
            $table->dateTime('fecha_hora');
            $table->decimal('precio', 15, 2);
            $table->timestamps();
        });

        Schema::create('funcion_asientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('funcion_id')->constrained()->cascadeOnDelete();
            $table->string('codigo_asiento'); // Ej: A1, B5
            $table->enum('estado', ['disponible', 'bloqueado', 'vendido'])->default('disponible');
            $table->timestamp('bloqueado_hasta')->nullable();
            $table->string('session_id')->nullable(); // Para identificar quién bloqueó
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('funcion_asientos');
        Schema::dropIfExists('funciones');
        Schema::dropIfExists('salas');
    }
};
