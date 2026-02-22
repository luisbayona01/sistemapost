<?php

namespace App\Services;

class MoneyService
{
    /**
     * Redondea un valor al peso colombiano más cercano (sin centavos).
     * Usa el redondeo contable estándar (PHP_ROUND_HALF_EVEN).
     */
    public static function roundToPeso($value): float
    {
        return round((float) $value, 0, PHP_ROUND_HALF_EVEN);
    }

    /**
     * Formatea un valor para visualización en pesos colombianos (COP).
     * Sin centavos y con separador de miles.
     */
    public static function formatCOP($value): string
    {
        return '$' . number_format(self::roundToPeso($value), 0, ',', '.');
    }

    /**
     * Calcula el total de una venta aplicando el redondeo obligatorio.
     */
    public static function totalVentaRedondeado($subtotal, $impuestos, $tarifas = 0): float
    {
        $bruto = (float) $subtotal + (float) $impuestos + (float) $tarifas;
        return self::roundToPeso($bruto);
    }
}
