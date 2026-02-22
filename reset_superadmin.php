<?php
// reset_superadmin.php
// Script de MANTENIMIENTO PROFUNDO para login

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\File;

echo "=== MANTENIMIENTO PROFUNDO DE LOGIN ===" . PHP_EOL;

// 1. Limpiar sesiones de disco
$sessionPath = storage_path('framework/sessions');
echo "Limpiando sesiones en: $sessionPath ..." . PHP_EOL;

if (!File::exists($sessionPath)) {
    File::makeDirectory($sessionPath, 0755, true);
    echo "✅ Directorio creado." . PHP_EOL;
}

$files = File::files($sessionPath);
$count = 0;
foreach ($files as $file) {
    if ($file->getFilename() !== '.gitignore') {
        File::delete($file);
        $count++;
    }
}
echo "✅ Eliminados $count archivos de sesión viejos." . PHP_EOL;

// 2. Verificar permisos de escritura (Crear archivo de check)
$checkFile = $sessionPath . '/_write_test.txt';
try {
    file_put_contents($checkFile, 'OK');
    unlink($checkFile);
    echo "✅ Permisos de escritura de sesión: CORRECTOS." . PHP_EOL;
} catch (\Exception $e) {
    echo "❌ ERROR CRÍTICO: No puedo escribir en la carpeta de sesiones. " . $e->getMessage() . PHP_EOL;
}

// 3. Restaurar Super Admin
$email = 'admin@admin.com';
$pass = '12345678';

$user = User::where('email', $email)->first();

if (!$user) {
    // Buscar si existe con otro email pero nombre parecido, o crear de cero
    $user = User::firstOrCreate(
        ['email' => $email],
        ['name' => 'Super Administrador', 'password' => bcrypt($pass), 'empresa_id' => 1]
    );
    echo "✅ Usuario CREADO: $email" . PHP_EOL;
} else {
    // Actualizar password forzosamente
    $user->password = bcrypt($pass);
    $user->save();
    echo "✅ Usuario ACTUALIZADO: $email" . PHP_EOL;
}

// 4. Asignar roles (asegurar)
try {
    $user->syncRoles(['Root', 'administrador', 'cajero', 'Gerente']);
    echo "✅ Roles sincronizados." . PHP_EOL;
} catch (\Exception $e) {
    echo "⚠️ Nota: Spatie roles no disponible o error: " . $e->getMessage() . PHP_EOL;
}

echo PHP_EOL . "🚀 LIQUIDACIÓN TOTAL COMPLETADA." . PHP_EOL;
echo "1. Cierra tu navegador completamente." . PHP_EOL;
echo "2. Abre una ventana de INCÓGNITO." . PHP_EOL;
echo "3. Logueate con: $email / $pass" . PHP_EOL;
