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
        Schema::table('productos', function (Blueprint $table) {
            $table->boolean('es_preventa')->default(false)->after('estado_pelicula');
            $table->string('slug')->nullable()->after('nombre');
        });

        Schema::table('funciones', function (Blueprint $table) {
            $table->boolean('activo')->default(true)->after('precio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn(['es_preventa', 'slug']);
        });

        Schema::table('funciones', function (Blueprint $table) {
            $table->dropColumn('activo');
        });
    }
};
