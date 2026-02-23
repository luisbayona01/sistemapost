<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->foreignId('distribuidor_id')->nullable()->after('presentacione_id')->constrained('distribuidores')->nullOnDelete();
            $table->enum('estado_pelicula', ['cartelera', 'proximamente', 'archivada'])->nullable()->after('genero');
            $table->date('fecha_estreno')->nullable()->after('estado_pelicula');
            $table->date('fecha_fin_exhibicion')->nullable()->after('fecha_estreno');
            $table->text('sinopsis')->nullable()->after('descripcion');
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropForeign(['distribuidor_id']);
            $table->dropColumn(['distribuidor_id', 'estado_pelicula', 'fecha_estreno', 'fecha_fin_exhibicion', 'sinopsis']);
        });
    }
};
