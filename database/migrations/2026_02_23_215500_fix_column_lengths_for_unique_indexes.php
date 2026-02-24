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
        Schema::table('users', function (Blueprint $table) {
            $table->string('email', 190)->change();
            $table->string('username', 190)->change();
        });

        Schema::table('productos', function (Blueprint $table) {
            $table->string('codigo', 190)->change();
        });

        Schema::table('clientes', function (Blueprint $table) {
            $table->string('persona_id', 190)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No es estrictamente necesario volver a 255, pero para consistencia:
        Schema::table('users', function (Blueprint $table) {
            $table->string('email', 255)->change();
            $table->string('username', 255)->change();
        });
    }
};
