<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'Courier', monospace;
            font-size: 10pt;
            line-height: 1.2;
            margin: 0;
            padding: 10px;
        }

        .center {
            text-align: center;
        }

        .bold {
            font-weight: bold;
        }

        .border-top {
            border-top: 1px dashed #000;
            padding-top: 5px;
            margin-top: 5px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .right {
            text-align: right;
        }

        .qr-placeholder {
            width: 100px;
            height: 100px;
            border: 1px solid #ddd;
            margin: 10px auto;
        }

        .small {
            font-size: 8pt;
        }
    </style>
</head>

<body>
    <div class="center">
        <span class="bold">{{ $doc->empresa->razon_social }}</span><br>
        NIT: {{ $doc->empresa->nit }}<br>
        {{ $doc->empresa->direccion }}<br>
        Tel: {{ $doc->empresa->telefono }}<br>
    </div>

    <div class="border-top center">
        <span class="bold">
            {{ $doc->tipo_documento === 'FE' ? 'FACTURA ELECTRÓNICA DE VENTA' : 'DOCUMENTO EQUIVALENTE POS' }}
        </span><br>
        No: {{ $doc->numero_completo }}<br>
        Fecha: {{ $doc->created_at->format('d/m/Y H:i') }}
    </div>

    <div class="border-top">
        CLIENTE: {{ $doc->cliente_nombre }}<br>
        DOC: {{ $doc->cliente_documento }}<br>
    </div>

    <div class="border-top">
        <table class="table">
            <thead>
                <tr>
                    <th align="left">DESCRIPCIÓN</th>
                    <th align="right">CANT</th>
                    <th align="right">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach($doc->lineas as $linea)
                    <tr>
                        <td>{{ $linea->descripcion }}</td>
                        <td align="right">{{ number_format($linea->cantidad, 0) }}</td>
                        <td align="right">${{ number_format($linea->total_linea, 0) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="border-top">
        <table class="table">
            <tr>
                <td align="left">SUBTOTAL:</td>
                <td align="right">${{ number_format($doc->subtotal, 0) }}</td>
            </tr>
            @if($doc->impuesto_inc > 0)
                <tr>
                    <td align="left">INC 8%:</td>
                    <td align="right">${{ number_format($doc->impuesto_inc, 0) }}</td>
                </tr>
            @endif
            <tr class="bold">
                <td align="left">TOTAL:</td>
                <td align="right">${{ number_format($doc->total, 0) }}</td>
            </tr>
        </table>
    </div>

    <div class="border-top center small">
        CUFE/CUDE: {{ $doc->cufe ?? 'PENDIENTE DE ENVÍO' }}<br>
        <div class="qr-placeholder">
            @if($doc->qr_code)
                {{-- Aquí iría el QR real --}}
                [QR CODE]
            @else
                [SIN QR]
            @endif
        </div>
        Representación gráfica de factura electrónica.<br>
        Software: Antigravity POS v1.0
    </div>

    @if($doc->tipo_documento === 'FE')
        <div class="border-top small center">
            Resolución DIAN No. {{ $doc->empresa->resolucion_dian ?? '1876xxxx' }}<br>
            Vigente desde: {{ $doc->empresa->resolucion_desde ?? '2024-01-01' }} al
            {{ $doc->empresa->resolucion_hasta ?? '2025-01-01' }}
        </div>
    @endif
</body>

</html>