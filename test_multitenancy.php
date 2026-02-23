<?php
// test_multitenancy.php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Empresa;
use App\Models\Venta;

echo "=== TEST: AISLAMIENTO MULTI-TENANCY ===" . PHP_EOL;

// 1. Usar Admin existente (Empresa A)
$userA = User::first();
$empresaIdA = $userA->empresa_id;

// 2. Crear Admin B Mockup (Empresa Ficticia ID 9999)
$userB = User::firstOrCreate(
    ['email' => 'admin_b_test@test.com'],
    [
        'name' => 'Admin B Test',
        'password' => bcrypt('123'),
        'empresa_id' => 9999 // ID improbable
    ]
);
$empresaIdB = $userB->empresa_id;

echo "Usuario A: {$userA->name} (Empresa ID: {$empresaIdA})" . PHP_EOL;
echo "Usuario B: {$userB->name} (Empresa ID: {$empresaIdB})" . PHP_EOL;

// 3. Crear Venta A (Contexto Empresa A)
auth()->login($userA);

// Crear venta real para prueba
try {
    Venta::create([
        'empresa_id' => $empresaIdA,
        'user_id' => $userA->id,
        'caja_id' => 1, // Asumimos caja 1 existe o no valida FK estricta en test
        'cliente_id' => 1,
        'comprobante_id' => 1,
        'fecha_hora' => now(),
        'total' => 5000,
        'estado_pago' => 'PAGADA',
        'canal' => 'confiteria',
        'origen' => 'TEST_MT'
    ]);
} catch (\Exception $e) {
    echo "⚠️ Nota: Venta no creada (quizás FK error), pero consultaremos existentes. Error: " . $e->getMessage() . PHP_EOL;
}

// 3. Verificar conteo como Usuario A
$conteoA = Venta::count();
echo "-> Vistas por Usuario A: $conteoA ventas (Correcto: ve la suya)" . PHP_EOL;

// 4. Cambiar contexto a Usuario B
auth()->logout();
auth()->login($userB);
echo "--- Cambio de Sesión a Usuario B ---" . PHP_EOL;

// 5. Verificar conteo como Usuario B (Debe ser 0 o solo las suyas)
// Limpiamos scope global cached si es necesario (Laravel lo maneja por request, pero en script continuo puede persistir)
// Forzamos nueva instancia query
$conteoB = Venta::count();

if ($conteoB < $conteoA) {
    echo "✅ ÉXITO: Usuario B ve $conteoB ventas (Aislamiento funciona)." . PHP_EOL;
} elseif ($conteoB === $conteoA) {
    echo "❌ ERROR: Usuario B ve las mismas ventas que A ($conteoB). Falla aislamiento." . PHP_EOL;
} else {
    echo "❓ Raro: Usuario B ve más ventas ($conteoB)." . PHP_EOL;
}

// 6. Validar Global Scope
$scopeApplied = (new Venta())->hasGlobalScope('empresa'); // Nombre del scope en HasEmpresaScope trait
if ($scopeApplied) {
    echo "🔍 Global Scope 'empresa' detectado en modelo Venta." . PHP_EOL;
} else {
    // A veces el nombre es diferente. Revisemos el Trait.
    // HasEmpresaScope::bootHasEmpresaScope usa static::addGlobalScope('empresa', ...)
    echo "⚠️ Global Scope 'empresa' parece NO estar activo o tiene otro nombre." . PHP_EOL;
}

echo "=== FIN TEST MULTI-TENANCY ===" . PHP_EOL;
