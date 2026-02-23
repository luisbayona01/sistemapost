<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <style>
        @page {
            margin: 0;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            width: 72mm;
            margin: 0;
            padding: 5mm;
            font-size: 11pt;
            line-height: 1.2;
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
            margin: 5px 0;
        }

        .header {
            margin-bottom: 5px;
        }

        .header h1 {
            font-size: 14pt;
            margin: 0;
        }

        .info {
            font-size: 9pt;
            margin-bottom: 5px;
        }

        .tabla {
            width: 100%;
            border-collapse: collapse;
        }

        .tabla th {
            border-bottom: 1px dashed #000;
            font-size: 9pt;
        }

        .tabla td {
            padding: 2px 0;
            font-size: 10pt;
            vertical-align: top;
        }

        .total-box {
            margin-top: 10px;
            font-size: 12pt;
        }

        .section-label {
            background-color: #334155;
            color: #fff;
            text-align: center;
            padding: 3px 5px;
            font-weight: bold;
            font-size: 9pt;
            margin: 5px 0 3px 0;
        }

        .cortesia-msg {
            background-color: #000;
            color: #fff;
            text-align: center;
            padding: 5px;
            font-weight: bold;
            margin: 5px 0;
        }

        .footer {
            font-size: 8pt;
            margin-top: 10px;
        }
    </style>
</head>

<body>

    {{-- ===================================================== --}}
    {{-- TICKET #1: TICKET DE INGRESO CINE (solo si hay boletos) --}}
    {{-- Este es el ticket que el cliente lleva a la sala --}}
    {{-- ===================================================== --}}
    @if($tickets->count() > 0)
        <div class="ticket-container">
            <div class="header text-center">
                <div class="section-label">🎬 TICKET DE INGRESO - CINE</div>
                <h1>{{ $empresa->nombre }}</h1>
                <div class="info">NIT: {{ $empresa->ruc }} | {{ $empresa->direccion }}</div>
            </div>

            <div class="divider"></div>

            <div class="info">
                <b>{{ $venta->comprobante->nombre ?? 'TICKET' }}:</b> {{ $venta->numero_comprobante }}<br>
                <b>FECHA:</b> {{ date('d/m/Y H:i', strtotime($venta->fecha_hora)) }}<br>
                <b>CAJERO:</b> {{ $venta->user->name }}<br>
                <b>CLIENTE:</b> {{ $venta->cliente->persona->razon_social ?? 'P. GENERAL' }}
            </div>

            <div class="divider"></div>

            <table class="tabla">
                <thead>
                    <tr>
                        <th align="left">CANT</th>
                        <th align="left">FUNCIÓN / ASIENTOS</th>
                        <th align="right">VALOR</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tickets as $t)
                        <tr>
                            <td>{{ $t->pivot->cantidad }}</td>
                            <td>
                                {{ $t->nombre }}<br>
                                <small><b>ASIENTOS: {{ $asientos }}</b></small>
                            </td>
                            <td align="right">{{ number_format($t->pivot->precio_venta * $t->pivot->cantidad, 0) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="divider"></div>

            <div class="total-box">
                <table width="100%">
                    <tr class="bold">
                        <td align="left">TOTAL CINE (EXENTO):</td>
                        <td align="right">${{ number_format((float) ($venta->subtotal_cine ?? 0), 0) }}</td>
                    </tr>
                </table>
            </div>

            @if($venta->metodo_pago === 'CORTESIA')
                <div class="cortesia-msg">*** CORTESÍA - VALOR $0 ***</div>
            @endif

            <div class="footer text-center">
                CONSERVE ESTE TICKET PARA SU INGRESO<br>
                {{ date('H:i:s') }}
            </div>
        </div>
    @endif

    {{-- Salto de página si hay AMBOS tickets de cine Y confitería --}}
    @if($tickets->count() > 0 && $snacks->count() > 0)
        <div style="page-break-after: always;"></div>
    @endif

    {{-- ===================================================== --}}
    {{-- TICKET #2: RECIBO DE PAGO UNIFICADO --}}
    {{-- Muestra TODO lo que se cobró (cine + snacks) --}}
    {{-- para que el cliente entienda el total --}}
    {{-- ===================================================== --}}
    @if($snacks->count() > 0 || $tickets->count() > 0)
        <div class="ticket-container">
            <div class="header text-center">
                <div class="section-label">🧾 RECIBO DE PAGO</div>
                <h1>{{ $empresa->nombre }}</h1>
                <div class="info">NIT: {{ $empresa->ruc }} | {{ $empresa->direccion }}</div>
            </div>

            <div class="divider"></div>

            <div class="info">
                <b>{{ $venta->comprobante->nombre ?? 'TICKET' }}:</b> {{ $venta->numero_comprobante }}<br>
                <b>FECHA:</b> {{ date('d/m/Y H:i', strtotime($venta->fecha_hora)) }}<br>
                <b>CAJERO:</b> {{ $venta->user->name }}<br>
                <b>CLIENTE:</b> {{ $venta->cliente->persona->razon_social ?? 'P. GENERAL' }}
            </div>

            <div class="divider"></div>

            <table class="tabla">
                <thead>
                    <tr>
                        <th align="left">CANT</th>
                        <th align="left">DESCRIPCIÓN</th>
                        <th align="right">TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Entradas de cine --}}
                    @if($tickets->count() > 0)
                        @foreach($tickets as $t)
                            <tr>
                                <td>{{ $t->pivot->cantidad }}</td>
                                <td>
                                    Entrada: {{ $t->nombre }}<br>
                                    <small>Asientos: {{ $asientos }}</small>
                                </td>
                                <td align="right">{{ number_format($t->pivot->precio_venta * $t->pivot->cantidad, 0) }}</td>
                            </tr>
                        @endforeach
                    @endif

                    {{-- Separador visual si hay ambos --}}
                    @if($tickets->count() > 0 && $snacks->count() > 0)
                        <tr>
                            <td colspan="3">
                                <div class="divider"></div>
                            </td>
                        </tr>
                    @endif

                    {{-- Productos de confitería --}}
                    @if($snacks->count() > 0)
                        @foreach($snacks as $s)
                            <tr>
                                <td>{{ $s->pivot->cantidad }}</td>
                                <td>{{ $s->nombre }}</td>
                                <td align="right">{{ number_format($s->pivot->precio_venta * $s->pivot->cantidad, 0) }}</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>

            <div class="divider"></div>

            <div class="total-box">
                <table width="100%">
                    @php
                        $subtotalCine = (float) ($venta->subtotal_cine ?? 0);
                        $subtotalConf = (float) ($venta->subtotal_confiteria ?? 0);
                        $incTotal = (float) ($venta->inc_total ?? 0);
                        $totalFinal = (float) ($venta->total_final ?? $venta->total ?? 0);
                    @endphp

                    {{-- Subtotal cine si hay entradas --}}
                    @if($tickets->count() > 0)
                        <tr>
                            <td align="left">Subtotal Entradas (Exento):</td>
                            <td align="right">${{ number_format($subtotalCine, 0) }}</td>
                        </tr>
                    @endif

                    {{-- Subtotal confitería si hay snacks --}}
                    @if($snacks->count() > 0)
                        <tr>
                            <td align="left">Subtotal Dulcería (Neto):</td>
                            <td align="right">${{ number_format($subtotalConf - $incTotal, 0) }}</td>
                        </tr>
                        <tr>
                            <td align="left">INC 8% Incluido:</td>
                            <td align="right">${{ number_format($incTotal, 0) }}</td>
                        </tr>
                    @endif

                    {{-- Método de pago --}}
                    @if($venta->metodo_pago !== 'CORTESIA')
                        <tr>
                            <td align="left">PAGO: {{ $venta->metodo_pago }}</td>
                            <td align="right">RECIBIDO: ${{ number_format((float) ($venta->monto_recibido ?? 0), 0) }}</td>
                        </tr>
                    @endif

                    <tr class="bold" style="font-size: 14pt; border-top: 1px solid #000;">
                        <td align="left">TOTAL A PAGAR:</td>
                        <td align="right">${{ number_format($totalFinal, 0) }}</td>
                    </tr>
                </table>
            </div>

            @if($venta->metodo_pago === 'CORTESIA')
                <div class="cortesia-msg">*** CORTESÍA - VALOR $0 ***</div>
            @endif

            <div class="footer text-center">
                ¡GRACIAS POR SU VISITA!<br>
                {{ date('H:i:s') }}
            </div>
        </div>
    @endif

    <script>
        window.onload = function () {
            window.print();
        }
    </script>
</body>

</html>