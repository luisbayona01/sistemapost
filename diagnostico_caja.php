<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$cajaId = 25;
$caja = \App\Models\Caja::find($cajaId);

if (!$caja) {
    echo "Caja #{$cajaId} no encontrada\n";
    exit(1);
}

echo "CAJA #{$cajaId} - DATOS GUARDADOS EN BD\n";
echo "=====================================\n";
echo "Saldo Inicial: $" . number_format($caja->saldo_inicial, 0) . "\n";
echo "Efectivo Esperado: $" . number_format($caja->monto_final_esperado, 0) . "\n";
echo "Efectivo Declarado: $" . number_format($caja->monto_final_declarado, 0) . "\n";
echo "Diferencia Efectivo: $" . number_format($caja->diferencia, 0) . "\n";
echo "\n";
echo "Tarjeta Esperada: $" . number_format($caja->tarjeta_esperada ?? 0, 0) . "\n";
echo "Tarjeta Declarada: $" . number_format($caja->tarjeta_declarada ?? 0, 0) . "\n";
echo "Diferencia Tarjeta: $" . number_format($caja->diferencia_tarjeta ?? 0, 0) . "\n";
echo "\n";

// Calcular desde ventas
$ventasEfectivo = \App\Models\Venta::where('caja_id', $caja->id)
    ->where('estado_pago', 'PAGADA')
    ->where('metodo_pago', 'EFECTIVO')
    ->sum('total');

$ventasTarjeta = \App\Models\Venta::where('caja_id', $caja->id)
    ->where('estado_pago', 'PAGADA')
    ->where('metodo_pago', 'TARJETA')
    ->sum('total');

$totalVentas = \App\Models\Venta::where('caja_id', $caja->id)
    ->where('estado_pago', 'PAGADA')
    ->sum('total');

echo "CÁLCULO REAL (desde tabla ventas):\n";
echo "===================================\n";
echo "Ventas Efectivo: $" . number_format($ventasEfectivo, 0) . "\n";
echo "Ventas Tarjeta: $" . number_format($ventasTarjeta, 0) . "\n";
echo "Total Ventas: $" . number_format($totalVentas, 0) . "\n";
echo "\n";

echo "DIAGNÓSTICO:\n";
echo "============\n";
if ($caja->tarjeta_esperada == 0 && $caja->tarjeta_declarada > 0) {
    echo "❌ ERROR: Tarjeta esperada es $0 pero declaraste $" . number_format($caja->tarjeta_declarada, 0) . "\n";
    echo "   Esto indica que NO hubo ventas con tarjeta, pero declaraste vouchers.\n";
} elseif ($caja->tarjeta_esperada > 0 && $caja->tarjeta_declarada == 0) {
    echo "❌ ERROR: Tarjeta esperada es $" . number_format($caja->tarjeta_esperada, 0) . " pero declaraste $0\n";
    echo "   Esto indica que hubo ventas con tarjeta pero no declaraste vouchers.\n";
} else {
    echo "✅ Los valores parecen correctos\n";
}
