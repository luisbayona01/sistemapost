<?php

namespace App\Helpers;

class DocumentoHelper
{
    /**
     * Validar cédula de ciudadanía colombiana
     * 
     * @param string $cedula
     * @return bool
     */
    public static function validarCedula(string $cedula): bool
    {
        // Limpiar
        $cedula = preg_replace('/[^0-9]/', '', $cedula);

        // Debe tener entre 6 y 10 dígitos
        $longitud = strlen($cedula);
        if ($longitud < 6 || $longitud > 10) {
            return false;
        }

        // No puede ser todo ceros
        if ($cedula === str_repeat('0', $longitud)) {
            return false;
        }

        return true;
    }

    /**
     * Validar cualquier tipo de documento
     * 
     * @param string $tipo CC|NIT|CE|TI|PPN
     * @param string $numero
     * @return bool
     */
    public static function validarDocumento(string $tipo, string $numero): bool
    {
        switch ($tipo) {
            case 'NIT':
                return NITHelper::validar($numero);

            case 'CC':
                return self::validarCedula($numero);

            case 'CE':
            case 'TI':
                // Similar a CC
                return self::validarCedula($numero);

            case 'PPN':
                // Pasaporte: alfanumérico, 6-20 caracteres
                return strlen($numero) >= 6 && strlen($numero) <= 20;

            default:
                return false;
        }
    }

    /**
     * Formatear documento según tipo
     * 
     * @param string $tipo
     * @param string $numero
     * @return string
     */
    public static function formatear(string $tipo, string $numero): string
    {
        if ($tipo === 'NIT') {
            return NITHelper::formatear($numero);
        }

        // Para otros tipos, solo limpiar
        return preg_replace('/[^0-9A-Za-z]/', '', $numero);
    }
}
