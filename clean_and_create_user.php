<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Empresa;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

try {
    // 1. Limpiar usuarios (Sin transacción para evitar problemas de DDL/truncado en MySQL)
    echo "Limpiando tabla de usuarios...\n";
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    DB::table('users')->truncate();
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    // 2. Asegurar empresa base
    echo "Verificando empresa...\n";
    $empresa = Empresa::first() ?? Empresa::create([
        'nombre' => 'CinePost Demo',
        'estado' => 'activa',
        'ruc' => '1234567890',
        'moneda_id' => 1,
        'direccion' => 'Calle Falsa 123',
        'correo' => 'test@cinepost.com',
        'telefono' => '123456789'
    ]);

    // 3. Crear el nuevo usuario maestro
    echo "Creando usuario admin@gmail.com...\n";
    $user = User::create([
        'name' => 'Luis Admin',
        'email' => 'admin@gmail.com',
        'password' => Hash::make('12345678'),
        'empresa_id' => $empresa->id,
        'estado' => 1
    ]);

    // 4. Asegurar Rol y Permisos
    echo "Asignando roles...\n";
    $role = Role::firstOrCreate(['name' => 'administrador']);

    // Intentar sincronizar permisos si existen
    try {
        $permissions = Permission::all();
        if ($permissions->count() > 0) {
            $role->syncPermissions($permissions);
        }
    } catch (\Exception $ePerm) {
        echo "Aviso: No se pudieron sincronizar permisos (posiblemente tablas de Spatie vacías).\n";
    }

    $user->assignRole('administrador');

    echo "\n✅ PROCESO COMPLETADO:\n";
    echo "--------------------------\n";
    echo "Usuario ÚNICO: admin@gmail.com\n";
    echo "Clave: 12345678\n";
    echo "Rol: administrador\n";
    echo "--------------------------\n";

} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
