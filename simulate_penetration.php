<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

// 1. Forzar Venta 888 vía DB sólida
DB::table('ventas')->insertOrIgnore([
    'id' => 888,
    'empresa_id' => 2,
    'user_id' => 6, // Un user cualquiera que sí exista
    'total' => 10000,
    'estado_pago' => 'PAGADA',
    'created_at' => now(),
    'updated_at' => now()
]);

// 2. Mock Session: Usuario de Empresa 1
$user1 = User::where('empresa_id', 1)->first();
Auth::login($user1);
echo "Logueado como: {$user1->name} (Empresa ID: {$user1->empresa_id})\n";

// --- TEST PRODUCTO ---
echo "\nTest Acceso Producto ID 87 (Empresa 2):\n";
try {
    $p = Producto::findOrFail(87);
    echo "❌ ERROR: Producto accesible!\n";
} catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
    echo "✅ OK: Producto no encontrado (BLOQUEADO)\n";
}

// --- TEST VENTA ---
echo "\nTest Acceso Venta ID 888 (Empresa 2):\n";
try {
    $v = Venta::findOrFail(888);
    echo "❌ ERROR: Venta accesible!\n";
} catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
    echo "✅ OK: Venta no encontrada (BLOQUEADO)\n";
}

echo "\n--- VERIFICACIÓN FINALIZADA ---\n";
