<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Base Inicial Default (Auto-apertura POS)
    |--------------------------------------------------------------------------
    | Monto con el que se abre la caja automáticamente cuando el cajero
    | ingresa al POS. Valor 0 = sin fondo de cambio.
    | El admin puede abrir manualmente con otro monto desde el panel.
    */
    'base_inicial_default' => env('CAJA_BASE_INICIAL', 0),

    /*
    |--------------------------------------------------------------------------
    | Umbral de Diferencia para Motivo Obligatorio
    |--------------------------------------------------------------------------
    |
    | Define el monto máximo de diferencia permitido en el cierre de caja
    | sin requerir un motivo explicativo. Si la diferencia (efectivo o tarjeta)
    | supera este valor, el sistema exigirá una justificación.
    |
    */
    'umbral_diferencia_motivo' => env('CAJA_UMBRAL_DIFERENCIA', 3000),

    /*
    |--------------------------------------------------------------------------
    | Días Máximos para Reapertura Administrativa
    |--------------------------------------------------------------------------
    |
    | Número máximo de días hacia atrás que se permite reabrir un cierre
    | para corrección administrativa.
    |
    */
    'dias_max_reapertura' => env('CAJA_DIAS_MAX_REAPERTURA', 7),
];
