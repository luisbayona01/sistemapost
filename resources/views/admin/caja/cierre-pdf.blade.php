<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Cierre de Caja #{{ $caja->id }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .report-title {
            font-size: 14px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .meta-info {
            width: 100%;
            margin-bottom: 20px;
        }

        .meta-info td {
            padding: 3px 0;
        }

        .section-title {
            background-color: #eee;
            padding: 5px;
            font-weight: bold;
            margin-bottom: 10px;
            border-bottom: 1px solid #ccc;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .table th,
        .table td {
            padding: 5px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .font-bold {
            font-weight: bold;
        }

        .summary-box {
            border: 1px solid #000;
            padding: 10px;
            margin-top: 20px;
        }

        .signature-area {
            margin-top: 50px;
            width: 100%;
        }

        .signature-line {
            width: 40%;
            border-top: 1px solid #000;
            padding-top: 5px;
            text-align: center;
        }

        .text-red-600 {
            color: #dc2626;
        }
    </style>
</head>

<body>

    <div class="header">
        @if(file_exists(public_path('assets/img/icon.png')))
            <img src="{{ public_path('assets/img/icon.png') }}" alt="Logo" style="max-height: 60px; margin-bottom: 10px;">
        @endif
        <div class="company-name">{{ auth()->user()->empresa->nombre ?? 'SISTEMAPOST' }}</div>
        <div class="report-title">Reporte de Cierre de Caja</div>
        <div>ID Cierre: #{{ $caja->id }}</div>
    </div>

    <table class="meta-info">
        <tr>
            <td width="15%"><strong>Cajero:</strong></td>
            <td width="35%">{{ $caja->user?->name ?? 'Usuario Desconocido' }}</td>
            <td width="15%"><strong>Apertura:</strong></td>
            <td width="35%">{{ $caja->fecha_apertura?->format('d/m/Y H:i') ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td width="15%"><strong>Estado:</strong></td>
            <td width="35%">{{ $caja->estado }}</td>
            <td width="15%"><strong>Cierre:</strong></td>
            <td width="35%">{{ $caja->fecha_cierre ? $caja->fecha_cierre->format('d/m/Y H:i') : 'N/A' }}</td>
        </tr>
    </table>

    <div style="text-align: center; margin-bottom: 20px;">
        @if($caja->cierre_version > 1)
            <div style="color: #dc2626; border: 1px dashed #dc2626; padding: 5px; font-weight: bold;">
                ⚠️ VERSIÓN CORREGIDA #{{ $caja->cierre_version }}
                @if($caja->reabierto_at)
                    <br><span style="font-size: 10px; font-weight: normal; color: #000;">
                        (Reabierto el {{ $caja->reabierto_at }})<br>
                        Motivo: {{ $caja->motivo_reapertura }}
                    </span>
                @endif
            </div>
        @endif
    </div>

    <div class="section-title">RESUMEN DE EFECTIVO</div>
    <table class="table">
        <tr>
            <td>(+) Saldo Inicial / Base</td>
            <td class="text-right">${{ number_format($totales['monto_inicial'] ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td>(+) Ingresos Efectivo (Ventas)</td>
            <td class="text-right">${{ number_format($totales['ventas_efectivo'] ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td>(+) Ingresos Manuales</td>
            <td class="text-right">${{ number_format($totales['ingresos_manuales'] ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td>(-) Gastos / Egresos</td>
            <td class="text-right text-red-600">-${{ number_format($totales['egresos_manuales'] ?? 0, 2) }}</td>
        </tr>
        <tr style="background-color: #f0f0f0; font-weight: bold;">
            <td>(=) EFECTIVO ESPERADO EN CAJA</td>
            <td class="text-right">${{ number_format($totales['efectivo_esperado'] ?? 0, 2) }}</td>
        </tr>
    </table>

    <div class="summary-box">
        <table width="100%">
            <tr style="background-color: #f0f0f0;">
                <td colspan="2"><strong>ARQUEO DE EFECTIVO</strong></td>
            </tr>
            <tr>
                <td><strong>Monto Declarado (Recuento físico):</strong></td>
                <td class="text-right">
                    <strong>${{ number_format((float) ($caja->monto_final_declarado ?? 0), 2) }}</strong>
                </td>
            </tr>
            <tr>
                <td><strong>Diferencia / Faltante / Sobrante:</strong></td>
                <td class="text-right {{ ($caja->diferencia ?? 0) != 0 ? 'text-red-600' : '' }}">
                    <strong>{{ ($caja->diferencia ?? 0) >= 0 ? '+' : '' }}{{ number_format((float) ($caja->diferencia ?? 0), 2) }}</strong>
                </td>
            </tr>
        </table>
    </div>

    <div class="summary-box" style="margin-top: 10px;">
        <table width="100%">
            <tr style="background-color: #e3f2fd;">
                <td colspan="2"><strong>ARQUEO DE TARJETA/DATÁFONO</strong></td>
            </tr>
            <tr>
                <td><strong>Vouchers Declarados:</strong></td>
                <td class="text-right"><strong>${{ number_format($caja->tarjeta_declarada ?? 0, 2) }}</strong></td>
            </tr>
            <tr>
                <td><strong>Ventas con Tarjeta (Sistema):</strong></td>
                <td class="text-right">${{ number_format($caja->tarjeta_esperada ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Diferencia:</strong></td>
                <td class="text-right {{ ($caja->diferencia_tarjeta ?? 0) != 0 ? 'text-red-600' : '' }}">
                    <strong>{{ ($caja->diferencia_tarjeta ?? 0) >= 0 ? '+' : '' }}{{ number_format($caja->diferencia_tarjeta ?? 0, 2) }}</strong>
                </td>
            </tr>
        </table>
        @if($caja->notas_cierre)
            <div style="margin-top: 10px; border-top: 1px dashed #ccc; padding-top: 5px;">
                <strong>Nota:</strong> {{ $caja->notas_cierre }}
            </div>
        @endif
        @if($caja->motivo_diferencia)
            <div style="margin-top: 10px; border-top: 1px solid #dc2626; padding-top: 5px; color: #dc2626;">
                <strong>Motivo Diferencia:</strong> {{ $caja->motivo_diferencia }}
            </div>
        @endif
    </div>

    <div class="section-title" style="margin-top: 20px;">DETALLE DE VENTAS</div>
    <table class="table">
        <tr style="background-color: #ddd;">
            <th>Concepto</th>
            <th class="text-right">Monto</th>
        </tr>
        <tr>
            <td>Tickets de Cine (Boletería)</td>
            <td class="text-right">${{ number_format($totales['ventas_entradas'] ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td>Dulcería & Snacks (Confitería) - BRUTO</td>
            <td class="text-right">${{ number_format($totales['ventas_dulceria'] ?? 0, 2) }}</td>
        </tr>
        <tr style="font-size: 10px; color: #555;">
            <td style="padding-left: 20px;">-- Base Gravable (Neto)</td>
            <td class="text-right">${{ number_format($totales['ventas_netas_confiteria'] ?? 0, 2) }}</td>
        </tr>
        <tr style="font-size: 10px; color: #555;">
            <td style="padding-left: 20px;">-- Impuesto INC (8%)</td>
            <td class="text-right">${{ number_format($totales['total_inc'] ?? 0, 2) }}</td>
        </tr>
        <tr style="font-weight: bold; font-size: 13px;">
            <td>TOTAL VENTAS BRUTO</td>
            <td class="text-right">${{ number_format($totales['total_general'] ?? 0, 2) }}</td>
        </tr>
    </table>

    <div class="section-title">MEDIOS DE PAGO</div>
    <table class="table">
        <tr>
            <td>Efectivo</td>
            <td class="text-right">${{ number_format($caja->efectivo_declarado ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td>Tarjeta / Datáfono</td>
            <td class="text-right">${{ number_format($caja->tarjeta_declarado ?? 0, 2) }}</td>
        </tr>
        @if($caja->otros_declarado > 0)
            <tr>
                <td>Otros (Web / Bonos)</td>
                <td class="text-right">${{ number_format($caja->otros_declarado ?? 0, 2) }}</td>
            </tr>
        @endif
        <tr style="font-weight: bold;">
            <td>TOTAL DECLARADO</td>
            <td class="text-right">${{ number_format($caja->monto_final_declarado ?? 0, 2) }}</td>
        </tr>
    </table>

    <table class="signature-area">
        <tr>
            <td class="signature-line">
                Firma Cajero<br>
                {{ $caja->user?->name ?? 'Usuario Desconocido' }}
            </td>
            <td width="20%"></td>
            <td class="signature-line">
                Firma Supervisor / Auditor<br>
                Empresa: {{ auth()->user()->empresa->nombre ?? 'Admin' }}
            </td>
        </tr>
    </table>

    <div style="text-align: center; font-size: 10px; color: #999; margin-top: 30px;">
        Generado automáticamente por SISTEMAPOST el {{ date('d/m/Y H:i:s') }}
        <br>
        Documento de Control Interno - No válido como comprobante fiscal
    </div>

</body>

</html>