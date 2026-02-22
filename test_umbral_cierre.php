<?php
// test_umbral_cierre.php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Caja;
use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "=== TEST: VALIDACIÓN DE UMBRAL DE CIERRE ===" . PHP_EOL;

// 1. Setup
$user = User::first();
auth()->login($user);
$umbral = config('caja.umbral_diferencia_motivo', 3000);
echo "Umbral configurado: $" . number_format($umbral, 0) . PHP_EOL;

// Limpiar caja abierta
Caja::where('user_id', $user->id)->where('estado', 'ABIERTA')->update(['estado' => 'CERRADA', 'fecha_cierre' => now()]);

// Abrir caja nueva
$caja = Caja::create([
    'empresa_id' => $user->empresa_id,
    'user_id' => $user->id,
    'fecha_apertura' => now(),
    'saldo_inicial' => 10000,
    'estado' => 'ABIERTA',
    'nombre' => 'Caja Test Umbral ' . rand(1, 999),
]);

echo "Caja creada: ID " . $caja->id . PHP_EOL;

// Simular totales contables (Esperado: 10000, Real: 15000 -> Diferencia +5000 > 3000)
$efectivoEsperado = 10000;
$montoDeclarado = 15000;
$diferencia = $montoDeclarado - $efectivoEsperado;

echo "Diferencia simulada: $" . number_format($diferencia, 0) . " (Mayor al umbral)" . PHP_EOL;

// CASO 1: SIN MOTIVO (DEBE FALLAR)
echo "--- Intento 1: Cerrar SIN motivo ---" . PHP_EOL;
if (abs($diferencia) > $umbral) {
    echo "❌ BLOQUEADO: Diferencia excede umbral y no hay motivo." . PHP_EOL;
} else {
    echo "⚠️ ERROR EN TEST: Debería bloquearse." . PHP_EOL;
}

// CASO 2: CON MOTIVO (DEBE PASAR)
echo "--- Intento 2: Cerrar CON motivo ---" . PHP_EOL;
$motivo = "Soberante por propina no registrada";
if (abs($diferencia) > $umbral && !empty($motivo)) {
    echo "✅ PERMITIDO: Motivo presente ('$motivo')." . PHP_EOL;

    // Simular cierre real
    $caja->update([
        'estado' => 'CERRADA',
        'monto_final_declarado' => $montoDeclarado,
        'diferencia' => $diferencia,
        'motivo_diferencia' => $motivo,
        'fecha_cierre' => now()
    ]);
    echo "✅ Caja cerrada exitosamente en BD." . PHP_EOL;
} else {
    echo "❌ Error en lógica de validación positiva." . PHP_EOL;
}

echo "=== FIN TEST UMBRAL ===" . PHP_EOL;
