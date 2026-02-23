<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Empresa;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

try {
    echo "--- INICIANDO RESET TOTAL DE USUARIOS ---\n";

    // 1. Limpiar usuarios por completo
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    DB::table('users')->truncate();
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    echo "✔ Tabla 'users' vaciada.\n";

    // 2. Obtener o crear empresa
    $empresa = Empresa::first() ?? Empresa::create([
        'nombre' => 'Cinema Paraíso',
        'estado' => 'activa',
        'ruc' => '1234567890',
        'moneda_id' => 1,
    ]);
    echo "✔ Empresa vinculada: {$empresa->nombre} (ID: {$empresa->id})\n";

    // 3. Crear el nuevo usuario MAESTRO con los campos correctos
    // IMPORTANTE: El controlador de login usa el campo 'username'
    $user = User::create([
        'name' => 'Administrador Sistema',
        'username' => 'admin',         // <--- ESTE ES EL QUE USA EL LOGIN
        'email' => 'admin@gmail.com',
        'password' => Hash::make('12345678'),
        'empresa_id' => $empresa->id,
        'estado' => 1 // Activo
    ]);
    echo "✔ Usuario creado: 'admin' / 'admin@gmail.com'.\n";

    // 4. Asignar rol de administrador
    $role = Role::firstOrCreate(['name' => 'administrador']);
    $user->assignRole($role);
    echo "✔ Rol 'administrador' asignado.\n";

    echo "\n--- PROCESO FINALIZADO CON ÉXITO ---\n";
    echo "POR FAVOR USA ESTOS DATOS EXACTOS EN EL FORMULARIO:\n";
    echo "Usuario (Username): admin\n";
    echo "Contraseña: 12345678\n";
    echo "------------------------------------\n";

} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
