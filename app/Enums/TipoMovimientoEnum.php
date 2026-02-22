<?php

namespace App\Enums;

enum TipoMovimientoEnum: string
{
    case Venta = 'VENTA';
    case Retiro = 'RETIRO';
    case INGRESO = 'INGRESO';
    case EGRESO = 'EGRESO';
    case CORTESIA = 'CORTESIA';
    case BAJA = 'BAJA';
    case DEVOLUCION = 'DEVOLUCION';
}
