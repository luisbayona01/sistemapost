<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Insumo;
use App\Models\InsumoLote;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

echo "Iniciando reparación de lotes de insumos...\n";

$insumos = Insumo::all();
$count = 0;

foreach ($insumos as $insumo) {
    if ($insumo->lotes()->count() == 0) {
        // Si tiene stock pero no lotes, crear un lote base
        // Si no tiene stock, igual crear un lote si es necesario (el error decía faltan 180 pero stock 500?)
        // El seeder puso stock_actual=500.

        $cantidad = $insumo->stock_actual > 0 ? $insumo->stock_actual : 1000;

        InsumoLote::create([
            'insumo_id' => $insumo->id,
            'numero_lote' => 'LOTE-FIX-' . $insumo->id . '-' . time(),
            'cantidad_inicial' => $cantidad,
            'cantidad_actual' => $cantidad, // Full stock disponible
            'costo_unitario' => $insumo->costo_unitario,
            'fecha_vencimiento' => Carbon::now()->addYear(),
        ]);

        echo "✅ Lote creado para: {$insumo->nombre} (Cant: {$cantidad})\n";
        $count++;
    } else {
        // Verificar si los lotes tienen stock
        $stockLotes = $insumo->lotes()->sum('cantidad_actual');
        if ($stockLotes < 10) { // Si tiene muy poco stock
            InsumoLote::create([
                'insumo_id' => $insumo->id,
                'numero_lote' => 'LOTE-REFILL-' . $insumo->id . '-' . time(),
                'cantidad_inicial' => 1000,
                'cantidad_actual' => 1000,
                'costo_unitario' => $insumo->costo_unitario,
                'fecha_vencimiento' => Carbon::now()->addYear(),
            ]);
            echo "✅ Lote REFILL creado para: {$insumo->nombre}\n";
            $count++;
        }
    }
}

echo "Reparación completada. {$count} insumos afectados.\n";
