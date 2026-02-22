<table>
    <thead>
        <tr>
            <th colspan="2" style="font-weight: bold; font-size: 14px; text-align: center;">REPORTE DE CIERRE DE CAJA
            </th>
        </tr>
        <tr>
            <th>ID Cierre:</th>
            <td>{{ $caja->id }}</td>
        </tr>
        <tr>
            <th>Cajero:</th>
            <td>{{ $caja->user?->name ?? 'Usuario Desconocido' }}</td>
        </tr>
        <tr>
            <th>Fecha Apertura:</th>
            <td>{{ $caja->fecha_apertura }}</td>
        </tr>
        <tr>
            <th>Fecha Cierre:</th>
            <td>{{ $caja->fecha_cierre }}</td>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="2"></td>
        </tr>

        <tr>
            <th style="background-color: #cccccc;">CONCEPTO</th>
            <th style="background-color: #cccccc;">MONTO</th>
        </tr>

        <tr>
            <td>Saldo Inicial</td>
            <td>{{ $totales['monto_inicial'] ?? 0 }}</td>
        </tr>
        <tr>
            <td>(+) Total Ventas Efectivo</td>
            <td>{{ $totales['ventas_efectivo'] ?? 0 }}</td>
        </tr>
        <tr>
            <td>(+) Ingresos Manuales</td>
            <td>{{ $totales['ingresos_manuales'] ?? 0 }}</td>
        </tr>
        <tr>
            <td>(-) Egresos Manuales</td>
            <td>{{ $totales['egresos_manuales'] ?? 0 }}</td>
        </tr>
        <tr>
            <th style="font-weight: bold;">(=) EFECTIVO ESPERADO</th>
            <th style="font-weight: bold;">{{ $totales['efectivo_esperado'] ?? 0 }}</th>
        </tr>

        <tr>
            <td colspan="2"></td>
        </tr>

        <tr>
            <td colspan="2"></td>
        </tr>

        <tr>
            <th style="background-color: #cccccc;">RESULTADO ARQUEO</th>
            <th style="background-color: #cccccc;">VALOR</th>
        </tr>
        <tr>
            <td>Efectivo Declarado (Físico)</td>
            <td>{{ $caja->efectivo_declarado ?? $caja->monto_final_declarado }}</td>
        </tr>
        <tr>
            <td>Tarjeta / Datáfono Declarado</td>
            <td>{{ $caja->tarjeta_declarado ?? 0 }}</td>
        </tr>
        @if($caja->otros_declarado > 0)
            <tr>
                <td>Otros (Web/Bonos) Declarado</td>
                <td>{{ $caja->otros_declarado ?? 0 }}</td>
            </tr>
        @endif
        <tr>
            <th style="font-weight: bold;">TOTAL DECLARADO</th>
            <th style="font-weight: bold;">{{ $caja->monto_final_declarado }}</th>
        </tr>
        <tr>
            <th style="color: {{ $caja->diferencia != 0 ? 'red' : 'green' }}">DIFERENCIA CONSOLIDADA</th>
            <th style="color: {{ $caja->diferencia != 0 ? 'red' : 'green' }}">{{ $caja->diferencia }}</th>
        </tr>

        <tr>
            <td colspan="2"></td>
        </tr>

        <tr>
            <th style="background-color: #cccccc;">DETALLE VENTAS (Bruto)</th>
            <th style="background-color: #cccccc;"></th>
        </tr>
        <tr>
            <td>Tickets de Cine (Boletería)</td>
            <td>{{ number_format($totales['ventas_entradas'] ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td>Dulcería & Snacks (Confitería) - BRUTO</td>
            <td>{{ number_format($totales['ventas_dulceria'] ?? 0, 2) }}</td>
        </tr>
        <tr style="color: #666; font-style: italic;">
            <td>-- Base Gravable (Neto)</td>
            <td>{{ number_format($totales['ventas_netas_confiteria'] ?? 0, 2) }}</td>
        </tr>
        <tr style="color: #666; font-style: italic;">
            <td>-- Impuesto INC (8%)</td>
            <td>{{ number_format($totales['total_inc'] ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Total Bruto</td>
            <td style="font-weight: bold;">{{ number_format($totales['total_general'] ?? 0, 2) }}</td>
        </tr>
    </tbody>
</table>