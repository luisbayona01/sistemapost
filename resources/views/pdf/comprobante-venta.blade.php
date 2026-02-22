<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprobante de venta</title>
</head>
<style>
    body {
        font-family: 'Courier New', Courier, monospace;
        margin: 0;
        padding: 0;
        color: #000;
        width: 100%;
    }

    .ticket {
        width: 100%;
        max-width: 280px; /* Optimizado para 80mm */
        margin: 0;
        padding: 5px;
    }

    .header {
        text-align: center;
        margin-bottom: 10px;
        border-bottom: 1px dashed #000;
        padding-bottom: 5px;
    }

    .header h1 {
        font-size: 14px;
        margin: 2px 0;
        text-transform: uppercase;
    }

    .header div {
        font-size: 9px;
        margin-bottom: 1px;
    }

    .type-badge {
        font-weight: bold;
        font-size: 10px;
        text-decoration: underline;
        margin-bottom: 5px;
    }

    .datos {
        margin-bottom: 10px;
        font-size: 10px;
        line-height: 1.2;
    }

    .tabla {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 10px;
    }

    .tabla th,
    .tabla td {
        text-align: left;
        padding: 3px 0;
        font-size: 10px;
        vertical-align: top;
    }

    .tabla th {
        border-bottom: 1px solid #000;
        font-weight: bold;
    }

    .totales {
        font-size: 11px;
        text-align: right;
        border-top: 1px dashed #000;
        padding-top: 5px;
    }

    .total-grande {
        font-size: 16px;
        font-weight: bold;
        margin-top: 5px;
    }

    .footer {
        text-align: center;
        font-size: 9px;
        margin-top: 15px;
        border-top: 1px dashed #000;
        padding-top: 5px;
    }

    .page-break {
        page-break-after: always;
    }
</style>

<body>

    @if($tickets->count() > 0)
        <div class="ticket">
            <div class="header">
                <div class="type-badge">TICKET CINE</div>
                <h1>{{ $empresa->nombre ?? 'N/A' }}</h1>
                <div>RUC N°: {{ $empresa->ruc ?? 'N/A' }}</div>
                <div>{{ strtoupper($empresa->direccion ?? 'N/A') }}</div>
                <div><strong>{{ strtoupper($venta->comprobante->nombre ?? 'Comprobante') }} #{{ $venta->numero_comprobante ?? $venta->id }}</strong></div>
            </div>

            <div class="datos">
                <div><strong>Cliente:</strong> {{ strtoupper($venta->cliente->persona->razon_social ?? 'Público General') }}
                </div>
                <div><strong>Fecha:</strong> {{ $venta->fecha_hora ? date("d/m/Y H:i", strtotime($venta->fecha_hora)) : 'N/A' }}</div>
            </div>

            <table class="tabla">
                <thead>
                    <tr>
                        <th>Cant.</th>
                        <th>Descripción (Asiento)</th>
                        <th style="text-align: right;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tickets as $ticket)
                        <tr>
                            <td>{{ $ticket->pivot->cantidad ?? 0 }}</td>
                            <td>
                                {{ $ticket->nombre ?? 'Ticket General' }} 
                                @if(!empty($asientos))
                                    <br><small><strong>Asientos: {{$asientos}}</strong></small>
                                @endif
                            </td>
                            <td style="text-align: right;">
                                {{ number_format(($ticket->pivot->cantidad ?? 0) * ($ticket->pivot->precio_venta ?? 0), 0) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="totales">
                <div>SUBTOTAL CINE:
                    {{ number_format($tickets->sum(fn($t) => ($t->pivot->cantidad ?? 0) * ($t->pivot->precio_venta ?? 0)), 0) }}</div>
                @if($snacks->count() == 0)
                    <div class="total-grande">TOTAL: ${{ number_format($venta->total, 0) }}</div>
                    <div style="font-size: 9px; margin-top: 5px;">PAGO: {{ $venta->metodo_pago }}</div>
                @endif
            </div>

            <div class="footer">
                CONSERVE SU TICKET PARA EL INGRESO A SALA
            </div>
        </div>

        @if($snacks->count() > 0)
            <div class="page-break"></div>
        @endif
    @endif

    @if($snacks->count() > 0)
        <div class="ticket">
            <div class="header">
                <div class="type-badge">CONFITERÍA / RETAIL</div>
                <h1>{{ $empresa->nombre ?? 'N/A' }}</h1>
                <div>RUC N°: {{ $empresa->ruc ?? 'N/A' }}</div>
                <div>{{ strtoupper($empresa->direccion ?? 'N/A') }}</div>
                <div><strong>{{ strtoupper($venta->comprobante->nombre ?? 'Comprobante') }} #{{ $venta->numero_comprobante ?? $venta->id }}</strong></div>
            </div>

            <div class="datos">
                <div><strong>Cliente:</strong> {{ strtoupper($venta->cliente->persona->razon_social ?? 'Público General') }}
                </div>
                <div><strong>Fecha:</strong> {{ $venta->fecha_hora ? date("d/m/Y H:i", strtotime($venta->fecha_hora)) : 'N/A' }}</div>
            </div>

            <table class="tabla">
                <thead>
                    <tr>
                        <th>Cant.</th>
                        <th>Descripción</th>
                        <th style="text-align: right;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($snacks as $snack)
                        <tr>
                            <td>{{ $snack->pivot->cantidad ?? 0 }}</td>
                            <td>{{ $snack->nombre ?? 'Producto' }}</td>
                            <td style="text-align: right;">
                                {{ number_format(($snack->pivot->cantidad ?? 0) * ($snack->pivot->precio_venta ?? 0), 0) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="totales">
                <div>SUBTOTAL SNACKS:
                    {{ number_format($snacks->sum(fn($s) => ($s->pivot->cantidad ?? 0) * ($s->pivot->precio_venta ?? 0)), 0) }}</div>
                <div class="total-grande">TOTAL: ${{ number_format($venta->total, 0) }}</div>
                <div style="font-size: 9px; margin-top: 5px;">PAGO: {{ $venta->metodo_pago }}</div>
            </div>

            <div class="footer">
                NO SE ACEPTAN CAMBIOS NI DEVOLUCIONES
            </div>
        </div>
    @endif

</body>

</html>
