<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleStructureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Roles Definition
        $roles = [
            'Root' => [
                'description' => 'Super Usuario - Acceso Total e Inmutable',
                'permissions' => Permission::all()->pluck('name')->toArray() // All permissions
            ],
            'Gerente' => [
                'description' => 'Gestión de precios, reportes y autorizaciones',
                'permissions' => [
                    'ver-registro-actividad',
                    'ver-kardex',
                    'ver-compra',
                    'crear-compra',
                    'mostrar-compra',
                    'ver-inventario',
                    'crear-inventario',
                    'ver-producto',
                    'crear-producto',
                    'editar-producto',
                    'ver-proveedore',
                    'crear-proveedore',
                    'editar-proveedore',
                    'ver-venta',
                    'crear-venta',
                    'mostrar-venta',
                    'ver-cliente',
                    'crear-cliente',
                    'editar-cliente',
                    'ver-reportes-globales', // Assuming this exists or similar
                    'ver-caja',
                    'aperturar-caja',
                    'cerrar-caja',
                    'crear-movimiento',
                    'ver-movimiento'
                ]
            ],
            'Operador' => [
                'description' => 'Ventas, ingreso de stock y consulta',
                'permissions' => [
                    'ver-venta',
                    'crear-venta',
                    'mostrar-venta',
                    'ver-inventario',
                    'ver-caja',
                    'aperturar-caja',
                    'cerrar-caja',
                    'ver-cliente',
                    'crear-cliente'
                ]
            ]
        ];

        // 2. Create Roles and Assign Permissions
        foreach ($roles as $roleName => $roleData) {
            $role = Role::firstOrCreate(['name' => $roleName]);

            // Sync permissions (careful not to revoke existing if running update, 
            // but for strict enforcement sync is better)

            // Filter permissions that actually exist in DB to avoid errors
            $validPermissions = Permission::whereIn('name', $roleData['permissions'])->pluck('name')->toArray();
            $role->syncPermissions($validPermissions);

            $this->command->info("Rol actualizado: $roleName con " . count($validPermissions) . " permisos.");
        }

        // 3. Create Root User if not exists
        $rootUser = User::firstOrCreate(
            ['email' => 'root@system.com'],
            [
                'name' => 'System Root',
                'password' => bcrypt('RootPassword123!'), // Strong default
                'empresa_id' => 1 // Assuming default company
            ]
        );
        $rootUser->assignRole('Root');
        $this->command->info("Usuario Root verificado (root@system.com)");

    }
}
