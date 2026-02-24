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
        Schema::table('empresa', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('nombre')->comment('Identificador de subdominio (ej: cine-central)');
            $table->string('dominio')->nullable()->unique()->after('slug')->comment('Dominio personalizado opcional (ej: cinecentral.com)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empresa', function (Blueprint $table) {
            $table->dropColumn(['slug', 'dominio']);
        });
    }
};
