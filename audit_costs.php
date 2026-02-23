<?php

use App\Models\Producto;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== VERIFICACIÓN DE COSTOS Y MARGENES ===" . PHP_EOL . PHP_EOL;

$productos = Producto::where('es_venta_retail', true)->get();

foreach ($productos as $p) {
    // Calcular costo si no está actualizado (aunque el seeder lo debió hacer)
    // $p->calcularRentabilidad(); 

    $costo = $p->costo_total_unitario ?? 0;
    $precio = $p->precio;
    $margen = $precio > 0 ? (($precio - $costo) / $precio * 100) : 0;

    $simboloAlerta = ($margen < 50) ? '⚠️' : '✅';

    echo "Product: {$p->nombre}" . PHP_EOL;
    echo "  Precio Venta: $" . number_format($precio, 0) . PHP_EOL;
    echo "  Costo Total : $" . number_format($costo, 0) . PHP_EOL;
    echo "  Margen      : " . number_format($margen, 1) . "% {$simboloAlerta}" . PHP_EOL;

    // Verificar si tiene receta
    $recetaCount = $p->insumos()->count();
    if ($recetaCount == 0) {
        echo "  ❌ ALERTA: No tiene insumos asociados (Receta vacía)" . PHP_EOL;
    } else {
        echo "  Insumos     : {$recetaCount} ingredientes" . PHP_EOL;
    }
    echo "----------------------------------------" . PHP_EOL;
}

echo PHP_EOL . "=== VERIFICACIÓN DE KARDEX INICIAL ===" . PHP_EOL;
// Verificar si hay registros de apertura para estos productos
$kardexCount = \App\Models\Kardex::whereIn('producto_id', $productos->pluck('id'))
    ->where('tipo_transaccion', \App\Enums\TipoTransaccionEnum::Apertura)
    ->count();

if ($kardexCount == $productos->count()) {
    echo "✅ Todos los productos retail tienen registro de APERTURA en Kardex." . PHP_EOL;
} else {
    echo "❌ ALERTA: Faltan registros de apertura. Esperados: {$productos->count()}, Encontrados: {$kardexCount}" . PHP_EOL;
}
