<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Ticket Cinema - {{ $venta->numero_comprobante }}</title>
    <style>
        @page {
            margin: 0;
        }

        body {
            font-family: 'Courier', monospace;
            font-size: 12px;
            color: #000;
            margin: 0;
            padding: 15px;
            width: 80mm;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }

        .header {
            margin-bottom: 15px;
        }

        .header h1 {
            font-size: 18px;
            margin: 0;
        }

        .ticket-info {
            margin-bottom: 10px;
            font-size: 11px;
        }

        .seat-badge {
            background: #000;
            color: #fff;
            padding: 5px 10px;
            font-size: 20px;
            display: inline-block;
            margin: 10px 0;
        }

        .footer {
            margin-top: 20px;
            font-size: 10px;
        }

        .qr-section {
            margin: 15px 0;
        }
    </style>
</head>

<body>
    <div class="header text-center">
        <h1>{{ $empresa->nombre }}</h1>
        <p>{{ $empresa->direccion }}<br>NIT: {{ $empresa->ruc }}</p>
    </div>

    <div class="divider"></div>

    <div class="text-center">
        <h2 style="margin: 5px 0;">TICKET DE ENTRADA</h2>
        <p class="bold" style="font-size: 14px; margin: 5px 0;">{{ $venta->productos->first()->nombre ?? 'PELÍCULA' }}
        </p>
    </div>

    <div class="divider"></div>

    <div class="ticket-info">
        @php $asiento = $venta->asientosCinema->first(); @endphp
        <p><strong>FECHA:</strong> {{ $asiento->funcion->fecha_hora->format('d/m/Y') }}</p>
        <p><strong>HORA :</strong> {{ $asiento->funcion->fecha_hora->format('H:i A') }}</p>
        <p><strong>SALA :</strong> {{ $asiento->funcion->sala->nombre }}</p>
    </div>

    <div class="text-center">
        <div class="seat-badge">
            ASIENTOS: {{ $venta->asientos_concatenados }}
        </div>
    </div>

    <div class="divider"></div>

    <table width="100%" style="font-size: 11px;">
        <tr>
            <td>SUBTOTAL:</td>
            <td class="text-right">${{ number_format((float) $venta->subtotal, 0) }}</td>
        </tr>
        <tr>
            <td>IVA (19%):</td>
            <td class="text-right">${{ number_format((float) $venta->impuesto, 0) }}</td>
        </tr>
        @if($venta->monto_tarifa > 0)
            <tr>
                <td>TARIFA ADMIN:</td>
                <td class="text-right">${{ number_format((float) $venta->monto_tarifa, 0) }}</td>
            </tr>
        @endif
        <tr class="bold" style="font-size: 14px;">
            <td>TOTAL:</td>
            <td class="text-right">${{ number_format((float) $venta->total, 0) }}</td>
        </tr>
    </table>

    <div class="divider"></div>

    <div class="qr-section text-center">
        @php
            $qrData = "VENTA:" . $venta->id . "|ASIENTOS:" . $venta->asientos_concatenados . "|SALA:" . $asiento->funcion->sala_id;
            $qrUrl = "https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=" . urlencode($qrData) . "&choe=UTF-8";
        @endphp
        <img src="{{ $qrUrl }}" width="120" height="120">
        <p style="font-size: 9px; margin-top: 5px;">{{ $venta->numero_comprobante }}</p>
    </div>

    <div class="footer text-center">
        <p>¡Gracias por su visita!<br>Disfrute la función.</p>
        <p>{{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>

</html>
