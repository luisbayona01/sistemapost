<?php
// reset_superadmin_username.php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "=== FIX USERNAME LOGIN ===" . PHP_EOL;

$email = 'admin@admin.com';
$user = User::where('email', $email)->first();

if ($user) {
    // IMPORTANTE: Asignar username igual al email, porque el login controller valida 'username'
    // Y el usuario en el frontend escribe su email en el campo que se llama 'username' o 'email'.
    // Para no fallar, ponemos el email en el campo username.

    $user->username = $email;
    $user->password = bcrypt('12345678');
    $user->save();

    echo "✅ Usuario actualizado." . PHP_EOL;
    echo "   Email:    $email" . PHP_EOL;
    echo "   Username: $user->username" . PHP_EOL; // Esto es lo que busca Auth::attempt
    echo "   Password: 12345678" . PHP_EOL;
} else {
    echo "❌ Usuario no encontrado. Creando..." . PHP_EOL;
    $user = User::create([
        'name' => 'Super Administrador',
        'email' => $email,
        'username' => $email, // CLAVE DEL ÉXITO
        'password' => bcrypt('12345678'),
        'empresa_id' => 1,
        'estado' => 1
    ]);
    echo "✅ Usuario CREADO con username correcto." . PHP_EOL;
}

// Verificar auth manual
if (auth()->attempt(['username' => $email, 'password' => '12345678'])) {
    echo "🚀 PRUEBA DE LOGIN CONTROLLER EXITOSA (usando 'username')." . PHP_EOL;
} else {
    echo "⚠️ PRUEBA FALLIDA. Algo más pasa." . PHP_EOL;
}
