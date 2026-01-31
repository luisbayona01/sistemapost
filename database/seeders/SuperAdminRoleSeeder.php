<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SuperAdminRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear rol super-admin
        $rolSuperAdmin = Role::firstOrCreate(
            ['name' => 'super-admin'],
            ['guard_name' => 'web']
        );

        // Obtener todos los permisos de super-admin
        $permisosSuperAdmin = Permission::whereIn('name', [
            // Empresas SaaS
            'crear-empresa-saas',
            'editar-empresa-saas',
            'ver-empresa-saas',
            'suspender-empresa',
            'activar-empresa',
            'eliminar-empresa',
            // Suscripciones
            'ver-suscripciones-todas',
            'ver-metricas-globales',
            'ver-reportes-globales',
            // Planes SaaS
            'administrar-planes-saas',
            'crear-plan-saas',
            'editar-plan-saas',
            'eliminar-plan-saas',
        ])->get();

        $rolSuperAdmin->syncPermissions($permisosSuperAdmin);

        $this->command->info('✅ Rol super-admin creado exitosamente');
        $this->command->info('   - Permisos asignados: ' . $permisosSuperAdmin->count());
    }
}
