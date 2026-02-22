<?php
// simular_flujo_ux_first.php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\{Caja, User, Venta};
use Illuminate\Http\Request;
use App\Http\Middleware\EnsureCajaAbierta;

echo "=== SIMULACIÓN FLUX UX-FIRST ===" . PHP_EOL;

// 1. Limpiar escenario (Cerrar cajas de usuario prueba si existen)
$user = User::first(); // Agarrar cualquier usuario para la prueba
if (!$user)
    die("No hay usuarios en la BD");
auth()->login($user);
echo "Usuario logueado: " . $user->name . PHP_EOL;

// Forzar cierre de cajas abiertas previas para probar auto-apertura
Caja::where('user_id', $user->id)->where('estado', 'ABIERTA')->update(['estado' => 'CERRADA', 'fecha_cierre' => now()]);
echo "1. Cajas previas cerradas forzosamente." . PHP_EOL;

// 2. Simular entrada al POS (Middleware)
echo "2. Ejecutando Middleware EnsureCajaAbierta..." . PHP_EOL;

$request = Request::create('/pos', 'GET');
$middleware = new EnsureCajaAbierta();

$middleware->handle($request, function ($req) {
    // Esto simula que el request pasó el middleware
    echo "   -> Middleware pasó el control al siguiente paso." . PHP_EOL;

    // Verificar si inyectó la caja
    if ($req->caja_activa) {
        echo "   ✅ ÉXITO: Caja inyectada en request." . PHP_EOL;
        echo "   📦 ID Caja: " . $req->caja_activa->id . PHP_EOL;
        echo "   💰 Saldo Inicial: " . $req->caja_activa->saldo_inicial . PHP_EOL;
        echo "   🔓 Estado: " . $req->caja_activa->estado . PHP_EOL;
    } else {
        echo "   ❌ ERROR: No se inyectó caja activa." . PHP_EOL;
    }
    return new Illuminate\Http\Response();
});

// 3. Verificar persistencia en BD
$cajaNueva = Caja::where('user_id', $user->id)->where('estado', 'ABIERTA')->latest()->first();
if ($cajaNueva) {
    echo "3. Verificación BD: La caja " . $cajaNueva->id . " existe y está ABIERTA." . PHP_EOL;

    // 4. Simular Venta usando esta cajá
    echo "4. Simulando Venta ligada a Caja " . $cajaNueva->id . "..." . PHP_EOL;
    try {
        $venta = Venta::create([
            'empresa_id' => $user->empresa_id,
            'user_id' => $user->id,
            'caja_id' => $cajaNueva->id, // Aquí es donde CashierController usaría $cajaAbierta->id
            'cliente_id' => 1,
            'comprobante_id' => 1,
            'fecha_hora' => now(),
            'total' => 50000,
            'estado_pago' => 'PAGADA',
            'canal' => 'confiteria',
            'tipo_venta' => 'FISICA',
            'origen' => 'TEST_SCRIPT'
        ]);
        echo "   ✅ Venta creada con ID: " . $venta->id . " ligada a Caja: " . $venta->caja_id . PHP_EOL;
    } catch (\Exception $e) {
        echo "   ❌ Error al crear venta: " . $e->getMessage() . PHP_EOL;
    }

} else {
    echo "❌ ERROR FATAL: No se creó la caja en BD." . PHP_EOL;
}

echo "=== FIN SIMULACIÓN ===" . PHP_EOL;
