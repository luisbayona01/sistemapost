<?php

/**
 * Script de Reset para Prueba de Cierre
 * Ejecutar: php reset_cierre.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔄 RESET DE DATOS PARA PRUEBA DE CIERRE\n";
echo "========================================\n\n";

try {
    // Paso 1: Contar registros actuales
    $ventasHoy = \App\Models\Venta::whereDate('fecha_hora', today())->count();
    $cajasHoy = \App\Models\Caja::whereDate('fecha_apertura', today())->count();
    $movimientosHoy = \App\Models\Movimiento::whereDate('created_at', today())->count();

    echo "📊 Registros encontrados hoy:\n";
    echo "   - Ventas: {$ventasHoy}\n";
    echo "   - Cajas: {$cajasHoy}\n";
    echo "   - Movimientos: {$movimientosHoy}\n\n";

    if ($ventasHoy === 0 && $cajasHoy === 0 && $movimientosHoy === 0) {
        echo "✅ No hay datos que eliminar. Sistema ya está limpio.\n";
        exit(0);
    }

    echo "🗑️  Eliminando datos...\n";

    // Paso 2: Eliminar detalles de ventas
    DB::table('producto_venta')
        ->whereIn('venta_id', function ($query) {
            $query->select('id')
                ->from('ventas')
                ->whereDate('fecha_hora', today());
        })
        ->delete();
    echo "   ✓ Detalles de ventas eliminados\n";

    // Paso 3: Eliminar ventas
    \App\Models\Venta::whereDate('fecha_hora', today())->delete();
    echo "   ✓ Ventas eliminadas\n";

    // Paso 4: Eliminar cajas
    \App\Models\Caja::whereDate('fecha_apertura', today())->delete();
    echo "   ✓ Cajas eliminadas\n";

    // Paso 5: Eliminar movimientos
    \App\Models\Movimiento::whereDate('created_at', today())->delete();
    echo "   ✓ Movimientos eliminados\n";

    // Verificación final
    echo "\n📋 VERIFICACIÓN FINAL:\n";
    echo "   - Ventas hoy: " . \App\Models\Venta::whereDate('fecha_hora', today())->count() . "\n";
    echo "   - Cajas hoy: " . \App\Models\Caja::whereDate('fecha_apertura', today())->count() . "\n";
    echo "   - Movimientos hoy: " . \App\Models\Movimiento::whereDate('created_at', today())->count() . "\n";

    echo "\n✅ Reset completado exitosamente.\n";
    echo "\n📘 Siguiente paso: Sigue el manual MANUAL_PRUEBA_CIERRE.md\n";
    echo "   Valores de prueba sugeridos:\n";
    echo "   - Base inicial: \$50,000\n";
    echo "   - Venta efectivo: \$100,000\n";
    echo "   - Venta tarjeta: \$50,000\n\n";

} catch (\Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "   Archivo: " . $e->getFile() . "\n";
    echo "   Línea: " . $e->getLine() . "\n";
    exit(1);
}
