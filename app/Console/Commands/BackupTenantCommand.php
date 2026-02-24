<?php

namespace App\Console\Commands;

use App\Models\Empresa;
use Illuminate\Console\Command;

class BackupTenantCommand extends Command
{
    protected $signature = 'backup:tenant {empresa_id?}';
    protected $description = 'Backup de un tenant específico o todos';

    public function handle()
    {
        $empresaId = $this->argument('empresa_id');

        if ($empresaId) {
            $empresa = Empresa::findOrFail($empresaId);
            config(['backup.backup.name' => "tenant_{$empresa->slug}"]);
            $this->info("🔄 Respaldando tenant: {$empresa->nombre}");
            // Forzar prefijo con empresa_id
            $this->call('backup:run', ['--only-db' => true]);
        } else {
            // Backup global (Super Admin)
            $this->info("🔄 Respaldando todos los tenants (Backup Global)");
            $this->call('backup:run');
        }
    }
}
