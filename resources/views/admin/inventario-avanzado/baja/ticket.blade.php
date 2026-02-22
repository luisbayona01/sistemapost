<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Ticket de Baja #{{$salida->id}}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            max-width: 300px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .details {
            margin-bottom: 10px;
        }

        .footer {
            text-align: center;
            border-top: 1px dashed #000;
            padding-top: 10px;
            margin-top: 10px;
            font-size: 10px;
        }

        .bold {
            font-weight: bold;
        }

        .row {
            display: flex;
            justify-content: space-between;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body onload="window.print()">
    <div class="no-print" style="margin-bottom: 20px; text-align: center;">
        <a href="{{ route('inventario-avanzado.baja.create') }}"
            style="background: #333; color: #fff; text-decoration: none; padding: 10px 20px; border-radius: 5px;">Volver</a>
    </div>

    <div class="header">
        <h3 style="margin: 0;">MOVIMIENTO DE INVENTARIO</h3>
        <p style="margin: 5px 0;">TICKET DE CONTROL INTERNO</p>
        <p class="bold">{{ strtoupper($salida->tipo) }}</p>
        <p>Fecha: {{ $salida->created_at->format('d/m/Y H:i') }}</p>
        <p>Folio: REF-{{ str_pad($salida->id, 6, '0', STR_PAD_LEFT) }}</p>
    </div>

    <div class="details">
        <p><span class="bold">Responsable:</span> {{ $salida->user->name }}</p>
        <p><span class="bold">Insumo:</span> {{ $salida->insumo->nombre }}</p>
        <hr style="border: 0; border-top: 1px dashed #000;">
        <div class="row">
            <span class="bold">CANTIDAD:</span>
            <span>{{ number_format($salida->cantidad, 2) }} {{ $salida->insumo->unidad_medida }}</span>
        </div>
        <div class="row">
            <span class="bold">COSTO EST.:</span>
            <span>${{ number_format($salida->costo_estimado, 2) }}</span>
        </div>
        <hr style="border: 0; border-top: 1px dashed #000;">
        <p><span class="bold">MOTIVO:</span></p>
        <p style="font-style: italic;">{{ $salida->motivo }}</p>
    </div>

    <div class="footer">
        <p>__________________________</p>
        <p>Firma de Autorización</p>
        <p>Generado por SistemaPOS</p>
    </div>
</body>

</html>
