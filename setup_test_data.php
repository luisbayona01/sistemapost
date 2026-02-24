<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Empresa;
use App\Models\User;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

echo "--- Iniciando Setup Empresa 2 ---\n";

// 1. Empresa 2
$empresa2 = Empresa::updateOrCreate(['id' => 2], [
    'nombre' => 'Empresa Prueba 2',
    'nit' => '900999999-9',
    'slug' => 'prueba2'
]);
echo "Empresa 2: OK\n";

// 2. Usuario en empresa 2
$user2 = User::updateOrCreate(['email' => 'prueba2@test.com'], [
    'name' => 'Usuario Prueba 2',
    'password' => Hash::make('password'),
    'empresa_id' => 2
]);
echo "Usuario Empresa 2: OK\n";

// 3. Producto en empresa 2
$producto2 = Producto::updateOrCreate(['codigo' => 'PROD-B2-TEST'], [
    'empresa_id' => 2,
    'nombre' => 'Producto Empresa 2',
    'precio' => 5000,
    'stock_actual' => 100,
    'estado' => 1,
    'presentacione_id' => 1,
    'marca_id' => 1,
    'categoria_id' => 1
]);
echo "Producto Empresa 2 (ID: {$producto2->id}): OK\n";

// 4. Venta en empresa 2
$venta2 = Venta::updateOrCreate(['id' => 888], [
    'empresa_id' => 2,
    'user_id' => $user2->id,
    'total' => 10000,
    'estado_pago' => 'PAGADA'
]);
echo "Venta Empresa 2 (ID: {$venta2->id}): OK\n";

echo "--- SETUP COMPLETADO ---\n";
