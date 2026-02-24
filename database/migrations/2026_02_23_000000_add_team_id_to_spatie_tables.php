<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $fk = config('permission.column_names.team_foreign_key', 'empresa_id');

        // ── 1. ROLES: Agregar empresa_id + índice único corto ─────────────────────
        if (!Schema::hasColumn('roles', $fk)) {
            Schema::table('roles', function (Blueprint $table) use ($fk) {
                $table->unsignedBigInteger($fk)->nullable()->after('id');
            });
        }

        $hasRoleIndex = count(DB::select("SHOW INDEX FROM `roles` WHERE Key_name = 'roles_team_unique'")) > 0;
        if (!$hasRoleIndex) {
            // Usar prefijos para evitar el error "Specified key was too long" de MySQL InnoDB
            DB::statement("ALTER TABLE `roles` ADD UNIQUE KEY `roles_team_unique` (`name`(100), `guard_name`(50), `{$fk}`)");
        }

        // ── 2. MODEL_HAS_ROLES: Solo agregar la columna, sin tocar PK ─────────────
        // NOTA: No modificamos la PK porque las filas existentes tienen empresa_id = NULL
        // y MySQL bloquea PKs nullable. Spatie soporta este modo "teams sin team en PK"
        // si la columna existe en la tabla (la usa como filtro extra en sus queries).
        if (!Schema::hasColumn('model_has_roles', $fk)) {
            Schema::table('model_has_roles', function (Blueprint $table) use ($fk) {
                $table->unsignedBigInteger($fk)->nullable()->default(null)->after('model_type');
                $table->index($fk, 'model_has_roles_empresa_id_index');
            });
        }

        // ── 3. MODEL_HAS_PERMISSIONS: Mismo enfoque ───────────────────────────────
        if (!Schema::hasColumn('model_has_permissions', $fk)) {
            Schema::table('model_has_permissions', function (Blueprint $table) use ($fk) {
                $table->unsignedBigInteger($fk)->nullable()->default(null)->after('model_type');
                $table->index($fk, 'model_has_permissions_empresa_id_index');
            });
        }

        // ── 4. Actualizar filas existentes con el empresa_id del owner del usuario ─
        // Para las asignaciones ya existentes, ponemos empresa_id = empresa del usuario
        DB::statement("
            UPDATE model_has_roles mr
            INNER JOIN users u ON u.id = mr.model_id AND mr.model_type = 'App\\\\Models\\\\User'
            SET mr.{$fk} = u.empresa_id
            WHERE mr.{$fk} IS NULL
        ");

        DB::statement("
            UPDATE model_has_permissions mp
            INNER JOIN users u ON u.id = mp.model_id AND mp.model_type = 'App\\\\Models\\\\User'
            SET mp.{$fk} = u.empresa_id
            WHERE mp.{$fk} IS NULL
        ");
    }

    public function down(): void
    {
        // No-op: no revertir en producción
    }
};
