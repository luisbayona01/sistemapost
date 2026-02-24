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
        // 1. PRODUCTOS: codigo -> (codigo, empresa_id)
        Schema::table('productos', function (Blueprint $table) {
            $table->dropUnique('productos_codigo_unique');
            $table->unique(['codigo', 'empresa_id']);
        });

        // 2. USERS: email, username, empleado_id -> incluir empresa_id
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_email_unique');
            $table->unique(['email', 'empresa_id']);

            $table->dropUnique('users_username_unique');
            $table->unique(['username', 'empresa_id']);

            $table->dropUnique('users_empleado_id_unique');
            $table->unique(['empleado_id', 'empresa_id']);
        });

        // 3. CLIENTES: persona_id -> (persona_id, empresa_id)
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropUnique('clientes_persona_id_unique');
            $table->unique(['persona_id', 'empresa_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropUnique(['codigo', 'empresa_id']);
            $table->unique('codigo');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['email', 'empresa_id']);
            $table->unique('email');

            $table->dropUnique(['username', 'empresa_id']);
            $table->unique('username');

            $table->dropUnique(['empleado_id', 'empresa_id']);
            $table->unique('empleado_id');
        });

        Schema::table('clientes', function (Blueprint $table) {
            $table->dropUnique(['persona_id', 'empresa_id']);
            $table->unique('persona_id');
        });
    }
};
