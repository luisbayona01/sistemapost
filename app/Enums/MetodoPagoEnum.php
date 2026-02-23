<?php

namespace App\Enums;

enum MetodoPagoEnum: string
{
    case EFECTIVO = 'EFECTIVO';
    case TARJETA = 'TARJETA';
    case TRANSFERENCIA = 'TRANSFERENCIA';
    case QR = 'QR';
    case MIXTO = 'MIXTO';
    case STRIPE = 'STRIPE';
}
