<?php
// diagnostico_final.php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

echo "=== DIAGNOSTICO COMPLETO DE USUARIO ===" . PHP_EOL;

$targetEmail = 'admin@admin.com';
$user = User::where('email', $targetEmail)->first();

if (!$user) {
    // Verificar si está soft-deleted
    if (Schema::hasColumn('users', 'deleted_at')) {
        $userDeleted = User::withTrashed()->where('email', $targetEmail)->first();
        if ($userDeleted) {
            echo "❌ CRÍTICO: El usuario '$targetEmail' existe pero está SOFT-DELETED (Papelera)." . PHP_EOL;
            echo "   Acción recomendada: \$user->restore();" . PHP_EOL;
            $userDeleted->restore();
            echo "   ✅ Usuario RESTAURADO automáticamente." . PHP_EOL;
            $user = $userDeleted;
        } else {
            echo "❌ El usuario '$targetEmail' NO EXISTE en absoluto." . PHP_EOL;
            exit;
        }
    } else {
        echo "❌ El usuario '$targetEmail' NO EXISTE." . PHP_EOL;
        exit;
    }
}

// 1. Estado Básico
echo "✅ Usuario encontrado: {$user->name} (ID: {$user->id})" . PHP_EOL;
echo "   Estado: " . ($user->estado ?? 'N/A') . PHP_EOL;
echo "   Empresa ID: " . ($user->empresa_id ?? 'NULL') . PHP_EOL;

// 2. Roles
echo "   Roles asignados: " . implode(', ', $user->getRoleNames()->toArray()) . PHP_EOL;

// 3. Password Check
if (Hash::check('12345678', $user->password)) {
    echo "   ✅ Password '12345678': CORRECTO." . PHP_EOL;
} else {
    echo "   ❌ Password '12345678': INCORRECTO." . PHP_EOL;
    // Forzar reset si falla
    $user->password = bcrypt('12345678');
    $user->save();
    echo "   🔄 Password reseteado a '12345678'." . PHP_EOL;
}

// 4. Auth Attempt (Simulación de Middleware)
if (auth()->attempt(['email' => $targetEmail, 'password' => '12345678'])) {
    echo "🚀 LOGIN EXITOSO (Auth::attempt devuelve true)." . PHP_EOL;
} else {
    echo "⚠️ LOGIN FALLIDO (Auth::attempt devuelve false). Posible causa: estado inactivo o GlobalScope." . PHP_EOL;
}

echo "=== FIN DIAGNOSTICO ===" . PHP_EOL;
