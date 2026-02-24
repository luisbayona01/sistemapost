<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixSpatieTeamIds extends Command
{
    protected $signature = 'spatie:fix-team-ids';
    protected $description = 'Rellenar empresa_id nulo en tablas de Spatie Permission para soportar teams';

    public function handle(): void
    {
        // 0. Limpiar filas huérfanas (usuarios que ya no existen en la tabla users)
        $orphans1 = DB::table('model_has_roles')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))->from('users')->whereColumn('users.id', 'model_has_roles.model_id');
            })
            ->where('model_type', 'App\\Models\\User')
            ->delete();
        $this->info("Filas huérfanas eliminadas de model_has_roles: {$orphans1}");

        $orphans2 = DB::table('model_has_permissions')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))->from('users')->whereColumn('users.id', 'model_has_permissions.model_id');
            })
            ->where('model_type', 'App\\Models\\User')
            ->delete();
        $this->info("Filas huérfanas eliminadas de model_has_permissions: {$orphans2}");

        // 1. Actualizar model_has_roles con empresa_id real
        DB::statement("
            UPDATE model_has_roles mr
            INNER JOIN users u ON u.id = mr.model_id AND mr.model_type = 'App\\\\Models\\\\User'
            SET mr.empresa_id = u.empresa_id
            WHERE mr.empresa_id IS NULL AND u.empresa_id IS NOT NULL
        ");
        $null1 = DB::table('model_has_roles')->whereNull('empresa_id')->count();
        $this->info("[model_has_roles] Actualizado ✅ - NULL restantes: {$null1}");

        // 2. Actualizar model_has_permissions con empresa_id real
        DB::statement("
            UPDATE model_has_permissions mp
            INNER JOIN users u ON u.id = mp.model_id AND mp.model_type = 'App\\\\Models\\\\User'
            SET mp.empresa_id = u.empresa_id
            WHERE mp.empresa_id IS NULL AND u.empresa_id IS NOT NULL
        ");
        $null2 = DB::table('model_has_permissions')->whereNull('empresa_id')->count();
        $this->info("[model_has_permissions] Actualizado ✅ - NULL restantes: {$null2}");

        // 3. Limpiar caché de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $this->info('Cache de permisos limpiado ✅');
        $this->info('DONE — Recarga el sistema.');
    }
}
