<?php
// fix_password.php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

echo "=== FIX PASSWORD MANUAL ===" . PHP_EOL;

$targetEmail = 'admin@admin.com';
$newPassword = '12345678';

$user = User::where('email', $targetEmail)->first();

if (!$user) {
    echo "❌ No encontré al usuario '$targetEmail'." . PHP_EOL . PHP_EOL;
    echo "Usuarios disponibles:" . PHP_EOL;
    foreach (User::all() as $u) {
        echo " - ID: {$u->id} | Email: {$u->email} | Nombre: {$u->name}" . PHP_EOL;
    }
} else {
    echo "✅ Usuario encontrado: {$user->name} (ID: {$user->id})" . PHP_EOL;

    $user->password = bcrypt($newPassword);
    $user->save();

    echo "🔑 Contraseña actualizada a: '$newPassword'" . PHP_EOL;
    echo "🔒 Hash nuevo: " . substr($user->password, 0, 20) . "..." . PHP_EOL;

    // Verificar login
    if (auth()->attempt(['email' => $targetEmail, 'password' => $newPassword])) {
        echo "🚀 PRUEBA LOGIN EXITOSA: Las credenciales funcionan." . PHP_EOL;
    } else {
        echo "⚠️ PRUEBA LOGIN FALLIDA: Algo raro pasa con Auth." . PHP_EOL;
    }
}
