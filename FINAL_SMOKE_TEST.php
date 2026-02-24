<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\Empresa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

function logResult($message, $success = true)
{
    $status = $success ? "[PASS]" : "[FAIL]";
    echo "{$status} {$message}\n";
}

echo "=== CINEMA POS: FINAL SMOKE TEST (PHASE 6 CLOSE) ===\n\n";

// 1. Verificar existencia de múltiples empresas
$empresasCount = Empresa::count();
echo "Empresas detectadas: {$empresasCount}\n";
if ($empresasCount < 2) {
    echo "⚠️  Solo hay una empresa. Creando Empresa 2 para el test...\n";
    $e2 = Empresa::updateOrCreate(['nombre' => 'Test Empresa 2'], [
        'nombre' => 'Test Empresa 2',
        'dominio' => 'test2.localhost',
        'correo' => 'test2@cinema.com',
        'estado' => 1
    ]);
} else {
    $e2 = Empresa::where('nombre', 'Test Empresa 2')->first() ?? Empresa::where('id', '>', 1)->first();
}

$e1 = Empresa::find(1);

// 2. Test de Aislamiento de Datos (Empresa 1)
echo "\n--- TEST DE AISLAMIENTO: EMPRESA 1 ---\n";
$user1 = User::where('empresa_id', 1)->first();
if (!$user1) {
    die("Error: No hay usuarios en Empresa 1 para el test.\n");
}

Auth::login($user1);
// Inyectar contexto de tenant manualmente para el test (lo que haría el middleware)
app()->instance('currentTenant', $e1);
setPermissionsTeamId($e1->id);

$productosE1Count = Producto::count();
$productosE2Count = Producto::withoutGlobalScopes()->where('empresa_id', $e2->id)->count();
$ventasE2Count = Venta::withoutGlobalScopes()->where('empresa_id', $e2->id)->count();

logResult("Usuario {$user1->email} (E1) logueado.");
logResult("Productos visibles para E1: {$productosE1Count}");
$leakDetected = Producto::where('empresa_id', $e2->id)->exists();
logResult("¿Fuga detectada hacia productos de E2?: " . ($leakDetected ? "SÍ (ERROR)" : "NO (OK)"), !$leakDetected);

// 3. Test de Aislamiento de Datos (Empresa 2)
echo "\n--- TEST DE AISLAMIENTO: EMPRESA 2 ---\n";
$user2 = User::where('empresa_id', $e2->id)->first();
if (!$user2) {
    // Crear usuario temporal
    $user2 = User::updateOrCreate(['email' => 'smoke-test-e2@gmail.com'], [
        'name' => 'Test User E2',
        'password' => bcrypt('password'),
        'empresa_id' => $e2->id,
        'username' => 'smoketeste2'
    ]);
}

Auth::login($user2);
app()->instance('currentTenant', $e2);
setPermissionsTeamId($e2->id);

$productosE2Visibles = Producto::count();
logResult("Usuario {$user2->email} (E2) logueado.");
logResult("Productos visibles para E2: {$productosE2Visibles}");
$leakDetected = Producto::where('empresa_id', $e1->id)->exists();
logResult("¿Fuga detectada hacia productos de E1?: " . ($leakDetected ? "SÍ (ERROR)" : "NO (OK)"), !$leakDetected);

// 4. Test de Recursión (Intento de acceso masivo)
echo "\n--- TEST DE RECURSIÓN Y RENDIMIENTO ---\n";
try {
    $start = microtime(true);
    for ($i = 0; $i < 50; $i++) {
        $count = Producto::count();
        $u = Auth::user();
    }
    $end = microtime(true);
    logResult("Ejecutadas 50 consultas con scope activo en " . round($end - $start, 4) . "s sin recursión infinita.");
} catch (\Throwable $e) {
    logResult("ERROR DE RECURSIÓN: " . $e->getMessage(), false);
}

// 5. Test de Índices Únicos (Colisión permitida entre tenants)
echo "\n--- TEST DE COLISIÓN DE ÍNDICES (TENANT INDEPENDENCE) ---\n";
$dupCode = "TEST-DUP-123";
try {
    DB::beginTransaction();

    // Crear en E1
    Producto::withoutGlobalScopes()->updateOrCreate(
        ['codigo' => $dupCode, 'empresa_id' => $e1->id],
        ['nombre' => 'Prod E1', 'categoria_id' => 1, 'marca_id' => 1, 'presentacione_id' => 1, 'precio' => 100]
    );

    // Intentar crear en E2 con el MISMO código
    $p2 = Producto::withoutGlobalScopes()->updateOrCreate(
        ['codigo' => $dupCode, 'empresa_id' => $e2->id],
        ['nombre' => 'Prod E2', 'categoria_id' => 1, 'marca_id' => 1, 'presentacione_id' => 1, 'precio' => 200]
    );

    logResult("Independencia de índices: Se permitió crear código '{$dupCode}' en ambas empresas sin error de duplicado.");
    DB::rollback();
} catch (\Throwable $e) {
    logResult("ERROR DE COLISIÓN: El sistema no permitió duplicidad de claves entre tenants: " . $e->getMessage(), false);
    DB::rollback();
}

echo "\n============================================\n";
echo "RESULTADO FINAL: SI TODO ESTÁ EN [PASS], FASE 6 CERRADA.\n";
echo "============================================\n";
