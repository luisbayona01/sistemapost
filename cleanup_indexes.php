<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$indexes = [
    'ventas' => ['idx_ventas_empresa_user_fecha', 'idx_ventas_empresa_fecha', 'idx_ventas_empresa_estado', 'idx_ventas_empresa_canal'],
    'cajas' => ['idx_cajas_empresa_user_estado', 'idx_cajas_empresa_fecha'],
    'movimientos' => ['idx_movimientos_caja_tipo_fecha', 'idx_movimientos_caja_fecha'],
    'inventario' => ['idx_inventario_producto_empresa', 'idx_inventario_empresa_cantidad'],
    'kardex' => ['idx_kardex_producto_fecha', 'idx_kardex_producto_tipo'],
    'compras' => ['idx_compras_empresa_fecha', 'idx_compras_empresa_proveedor']
];

foreach ($indexes as $table => $idxs) {
    foreach ($idxs as $idx) {
        try {
            DB::statement("DROP INDEX {$idx} ON {$table}");
            echo "Dropped index {$idx} on {$table}\n";
        } catch (\Exception $e) {
            echo "Failed to drop {$idx} on {$table}: " . $e->getMessage() . "\n";
        }
    }
}
