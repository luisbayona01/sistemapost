<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('configuracion_numeracion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas');

            $table->enum('tipo', ['DE', 'FE']);
            $table->string('prefijo', 10);
            $table->integer('consecutivo_actual')->default(0);
            $table->integer('consecutivo_inicial')->default(1);
            $table->integer('consecutivo_final')->nullable()
                ->comment('Solo para FE con resolución DIAN');

            // Resolución DIAN (solo FE)
            $table->string('resolucion_numero', 50)->nullable();
            $table->date('resolucion_fecha')->nullable();
            $table->date('resolucion_fecha_inicio')->nullable();
            $table->date('resolucion_fecha_fin')->nullable();

            $table->boolean('activo')->default(true);
            $table->text('notas')->nullable();

            $table->timestamps();

            $table->unique(['empresa_id', 'tipo', 'prefijo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configuracion_numeracion');
    }
};
