<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuración Fiscal Colombia (DIAN)
    |--------------------------------------------------------------------------
    |
    | Aquí se definen los parámetros para la emisión de documentos fiscales.
    |
    */

    'proveedor_activo' => env('FISCAL_PROVIDER', 'alegra'), // alegra, siigo, mock

    'uvt_valor' => env('FISCAL_UVT_VALOR', 47065), // Valor UVT 2024

    'limite_pos_uvt' => 5, // Límite de 5 UVT para Documento Equivalente POS

    'emision_automatica' => env('FISCAL_EMISION_AUTOMATICA', true),

    'providers' => [
        'alegra' => [
            'username' => env('ALEGRA_USER'),
            'token' => env('ALEGRA_TOKEN'),
            'endpoint' => env('ALEGRA_ENDPOINT', 'https://api.alegra.com/api/v1/'),
        ],
    ],
];
