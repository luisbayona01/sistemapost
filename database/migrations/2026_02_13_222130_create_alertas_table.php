<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('alertas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->enum('tipo', ['CRITICA', 'ADVERTENCIA', 'INFO']);
            $table->enum('categoria', ['INVENTARIO', 'OCUPACION', 'CAJA', 'PRECIO', 'GENERAL']);
            $table->string('titulo', 255);
            $table->text('mensaje');
            $table->json('datos')->nullable()->comment('Datos adicionales en JSON');
            $table->boolean('vista')->default(false);
            $table->boolean('resuelta')->default(false);
            $table->timestamp('resuelta_at')->nullable();
            $table->foreignId('resuelta_por')->nullable()->constrained('users');
            $table->timestamps();

            $table->index(['empresa_id', 'vista', 'resuelta']);
            $table->index(['tipo', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alertas');
    }
};
