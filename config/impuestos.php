<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuración de Impuestos (Normativa Colombiana DIAN 2026)
    |--------------------------------------------------------------------------
    |
    | inc_confiteria: Impuesto Nacional al Consumo para confitería (8%)
    | aplicar_inc: Switch maestro para activar/desactivar el cálculo.
    |
    */

    'inc_confiteria' => 8,

    'aplicar_inc' => true,

    'regla_redondeo' => 0, // 0 decimales para el total final (Estándar POS Colombia)
];
