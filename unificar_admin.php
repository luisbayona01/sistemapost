<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

echo "=== UNIFICACIÓN DE SUPER USUARIO ===" . PHP_EOL;

// 1. Definir credenciales del Super Admin
$email = 'admin@admin.com';
$password = '12345678';

// 2. Buscar o Crear el Usuario
$superUser = User::firstOrCreate(
    ['email' => $email],
    [
        'name' => 'Super Administrador',
        'password' => bcrypt($password),
        'empresa_id' => 1,
    ]
);

// Asegurar datos correctos
$superUser->update([
    'password' => bcrypt($password),
    'empresa_id' => 1
]);

echo "✅ Usuario Principal: $email (ID: {$superUser->id})" . PHP_EOL;

// 3. Asignar TODOS los roles disponibles para máxima autoridad
// Obtenemos todos los roles de la BD para no fallar si alguno no existe
$allRoles = Role::pluck('name')->toArray();

// Aseguramos que tenga 'cajero' y 'administrador' como mínimo
$requiredRoles = ['administrador', 'cajero', 'Gerente', 'Root'];

foreach ($requiredRoles as $reqRole) {
    if (!in_array($reqRole, $allRoles)) {
        Role::create(['name' => $reqRole, 'guard_name' => 'web']);
        echo "   -> Rol creado: $reqRole" . PHP_EOL;
    }
}

// Sincronizar con TODOS los roles requeridos
$superUser->syncRoles($requiredRoles);
echo "✅ Roles asignados: " . implode(', ', $requiredRoles) . PHP_EOL;

// 4. ELIMINAR al resto de usuarios
try {
    // Evitar borrarnos a nosotros mismos
    $deleted = User::where('id', '!=', $superUser->id)->delete();
    echo "🗑️  Otros usuarios eliminados: $deleted" . PHP_EOL;
} catch (\Exception $e) {
    echo "⚠️  Nota: No se pudieron borrar algunos usuarios (posiblemente por restricciones FK de ventas pasadas)." . PHP_EOL;
    echo "    Error: " . $e->getMessage() . PHP_EOL;
}

echo PHP_EOL . "🚀 SISTEMA LIMPIO. Credenciales únicas:" . PHP_EOL;
echo "   User: $email" . PHP_EOL;
echo "   Pass: $password" . PHP_EOL;
