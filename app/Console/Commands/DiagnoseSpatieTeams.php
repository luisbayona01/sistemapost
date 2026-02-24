<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DiagnoseSpatieTeams extends Command
{
    protected $signature = 'spatie:diagnose';
    protected $description = 'Ver usuarios con model_has_roles.empresa_id = NULL';

    public function handle(): void
    {
        // Filas NULL de model_has_roles
        $nullRows = DB::table('model_has_roles as mr')
            ->leftJoin('users as u', 'u.id', '=', 'mr.model_id')
            ->leftJoin('roles as r', 'r.id', '=', 'mr.role_id')
            ->whereNull('mr.empresa_id')
            ->where('mr.model_type', 'App\Models\User')
            ->select('mr.model_id', 'u.name as user_name', 'u.empresa_id as user_empresa', 'r.name as role_name')
            ->get();

        if ($nullRows->isEmpty()) {
            $this->info('✅ No hay filas con empresa_id NULL en model_has_roles');
            return;
        }

        $this->warn("⚠️ {$nullRows->count()} filas con empresa_id NULL en model_has_roles:");
        $this->table(
            ['user_id', 'user_name', 'user.empresa_id', 'role_name'],
            $nullRows->map(fn($r) => [
                $r->model_id,
                $r->user_name ?? 'N/A (sin user)',
                $r->user_empresa ?? 'NULL',
                $r->role_name ?? 'N/A',
            ])->toArray()
        );

        // También mostrar todos los usuarios y sus empresa_id
        $this->line('');
        $this->info('Todos los usuarios en la BD:');
        $users = DB::table('users')->select('id', 'name', 'empresa_id')->get();
        $this->table(['id', 'name', 'empresa_id'], $users->map(fn($u) => [$u->id, $u->name, $u->empresa_id ?? 'NULL'])->toArray());
    }
}
