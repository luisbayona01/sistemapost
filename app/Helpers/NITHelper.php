<?php

namespace App\Helpers;

class NITHelper
{
    /**
     * Validar NIT con dígito de verificación
     */
    public static function validar(string $nit): bool
    {
        // Limpiar
        $nit = preg_replace('/[^0-9]/', '', $nit);

        if (strlen($nit) < 2)
            return false;

        $dv = substr($nit, -1);
        $numero = substr($nit, 0, -1);

        return self::calcularDV($numero) == $dv;
    }

    /**
     * Calcular dígito de verificación según DIAN
     */
    public static function calcularDV(string $nit): int
    {
        $primos = [3, 7, 13, 17, 19, 23, 29, 37, 41, 43, 47, 53, 59, 67, 71];
        $suma = 0;
        $len = strlen($nit);

        for ($i = 0; $i < $len; $i++) {
            $suma += (int) $nit[$len - 1 - $i] * $primos[$i];
        }

        $residuo = $suma % 11;

        if ($residuo > 1) {
            return 11 - $residuo;
        }

        return $residuo;
    }

    /**
     * Formatear NIT (Ej: 900123456-3)
     */
    public static function formatear(string $nit): string
    {
        $nit = preg_replace('/[^0-9]/', '', $nit);
        if (strlen($nit) < 2)
            return $nit;

        $dv = substr($nit, -1);
        $numero = substr($nit, 0, -1);

        return $numero . '-' . $dv;
    }
}
