@extends('layouts.admin')

@section('content')
    <div class="container mx-auto px-4 py-6 max-w-5xl">

        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-lg p-8" id="reporte-imprimible">
            <div class="text-center mb-6 border-b pb-4">
                <h1 class="text-3xl font-bold">📊 REPORTE DE CIERRE</h1>
                <p class="text-gray-600">Caja #{{ $caja->id }}</p>
                <p class="text-sm text-gray-500">
                    {{ $caja->fecha_cierre ? $caja->fecha_cierre->format('d/m/Y H:i') : 'En proceso' }}
                </p>
            </div>

            <!-- Información General -->
            <div class="grid grid-cols-2 gap-4 mb-6 bg-gray-50 p-4 rounded">
                <div>
                    <p class="text-sm text-gray-600">Cajero Apertura:</p>
                    <p class="font-semibold">{{ $caja->user->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Cajero Cierre:</p>
                    <p class="font-semibold">{{ $caja->cerradoPor->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Fecha Apertura:</p>
                    <p class="font-semibold">{{ $caja->fecha_apertura?->format('d/m/Y H:i') ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Fecha Cierre:</p>
                    <p class="font-semibold">
                        {{ $caja->fecha_cierre ? $caja->fecha_cierre->format('d/m/Y H:i') : 'Abierta' }}
                    </p>
                </div>
            </div>

            <!-- Resumen Financiero -->
            <div class="mb-6">
                <h3 class="text-xl font-bold mb-3 border-b pb-2">💰 Resumen Financiero</h3>
                <table class="w-full">
                    <tr class="border-b">
                        <td class="py-2 font-semibold">Monto Inicial:</td>
                        <td class="py-2 text-right">${{ number_format((float) ($caja->monto_inicial ?? 0), 2) }}</td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-2">🎟️ Ventas Boletería:</td>
                        <td class="py-2 text-right">${{ number_format($totales['ventas_boleteria'], 2) }}</td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-2">🍿 Ventas Confitería (Bruto):</td>
                        <td class="py-2 text-right">${{ number_format($totales['ventas_confiteria'], 2) }}</td>
                    </tr>
                    <tr class="border-b italic text-gray-500">
                        <td class="py-1 pl-4">-- Base Gravable (Neto):</td>
                        <td class="py-1 text-right">${{ number_format($totales['ventas_netas_confiteria'], 2) }}</td>
                    </tr>
                    <tr class="border-b italic text-gray-500">
                        <td class="py-1 pl-4">-- Impuesto INC (8%):</td>
                        <td class="py-1 text-right">${{ number_format($totales['total_inc'], 2) }}</td>
                    </tr>
                    <tr class="border-b bg-blue-50">
                        <td class="py-2 font-bold">TOTAL VENTAS:</td>
                        <td class="py-2 text-right font-bold text-blue-700">
                            ${{ number_format($totales['total_ventas'], 2) }}</td>
                    </tr>
                </table>
            </div>

            <!-- Desglose por Método de Pago REVISADO -->
            <div class="mb-6">
                <h3 class="text-xl font-bold mb-3 border-b pb-2">📂 Arqueo de Caja (Balance General)</h3>
                <table class="w-full">
                    <tr class="border-b bg-gray-50">
                        <td class="py-2 px-4">💰 Base en Caja (Monto Inicial):</td>
                        <td class="py-2 px-4 text-right font-medium">${{ number_format($caja->monto_inicial ?? 0, 2) }}</td>
                    </tr>
                    <tr class="border-b">
                        <td class="py-2 px-4">🎟️ Total Ventas + Ingresos (Sistema):</td>
                        <td class="py-2 px-4 text-right font-medium">
                            ${{ number_format($totales['total_ventas'] + ($totales['ingresos_manuales'] ?? 0) - ($totales['egresos_manuales'] ?? 0), 2) }}
                        </td>
                    </tr>
                    <tr class="border-b bg-slate-100 font-bold">
                        <td class="py-2 px-4 text-lg">EFECTIVO + TARJETAS (ESPERADO TOTAL):</td>
                        <td class="py-2 px-4 text-right text-lg">
                            ${{ number_format($totales['monto_final_esperado_total'], 2) }}</td>
                    </tr>
                    <tr class="border-b bg-emerald-50">
                        <td class="py-3 px-4 font-black text-emerald-800 text-lg">TOTAL CONTADO Y DECLARADO:</td>
                        <td class="py-3 px-4 text-right font-black text-emerald-800 text-2xl">
                            ${{ number_format($caja->monto_final_declarado ?? 0, 2) }}
                        </td>
                    </tr>

                    <tr class="bg-gray-200 border-t-4 border-slate-300">
                        <td class="py-4 px-4 font-black text-xl">DIFERENCIA FINAL:</td>
                        <td class="py-4 px-4 text-right font-black text-3xl 
                                    {{ abs($caja->diferencia ?? 0) < 1 ? 'text-emerald-700' : '' }}
                                    {{ ($caja->diferencia ?? 0) > 1 ? 'text-blue-700' : '' }}
                                    {{ ($caja->diferencia ?? 0) < -1 ? 'text-rose-700' : '' }}">
                            ${{ number_format(abs($caja->diferencia ?? 0), 2) }}
                            <div class="text-xs uppercase tracking-widest mt-1">
                                @if(abs($caja->diferencia ?? 0) < 1)
                                    (CUADRE PERFECTO ✔️)
                                @elseif(($caja->diferencia ?? 0) > 0)
                                    (SOBRANTE)
                                @else
                                    (FALTANTE)
                                @endif
                            </div>
                        </td>
                    </tr>
                </table>

                <!-- Desglose Informativo (Opcional para auditoría) -->
                <div class="mt-6 border-t pt-4">
                    <details class="cursor-pointer group">
                        <summary
                            class="text-xs font-bold text-slate-400 uppercase tracking-widest hover:text-slate-600 transition-colors">
                            🔍 Ver detalles informativos de medios de pago
                        </summary>
                        <div class="mt-4 grid grid-cols-2 gap-8 text-sm bg-slate-50 p-4 rounded-xl border border-slate-100">
                            <div>
                                <p class="font-bold text-slate-500 mb-2 uppercase text-[10px]">Efectivo:</p>
                                <div class="flex justify-between"><span>Esperado:</span>
                                    <span>${{ number_format($totales['efectivo_esperado'], 2) }}</span></div>
                                <div class="flex justify-between border-b pb-1"><span>Declarado:</span>
                                    <span>${{ number_format($caja->efectivo_declarado ?? 0, 2) }}</span></div>
                            </div>
                            <div>
                                <p class="font-bold text-slate-500 mb-2 uppercase text-[10px]">Tarjetas:</p>
                                <div class="flex justify-between"><span>Esperado:</span>
                                    <span>${{ number_format($totales['ventas_tarjeta'], 2) }}</span></div>
                                <div class="flex justify-between border-b pb-1"><span>Declarado:</span>
                                    <span>${{ number_format($caja->tarjeta_declarado ?? 0, 2) }}</span></div>
                            </div>
                        </div>
                    </details>
                </div>
            </div>
            <p class="text-[10px] text-slate-400 mt-2 italic">* La diferencia total es el resultado de comparar la suma
                de todo lo declarado contra la suma de todo lo esperado por el sistema.</p>
        </div>

        <!-- Notas -->
        @if($caja->notas_cierre)
            <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-400 p-4">
                <p class="font-semibold mb-1">📝 Notas de Cierre:</p>
                <p class="text-gray-700">{{ $caja->notas_cierre }}</p>
            </div>
        @endif

    </div>

    <!-- Botones de Acción (NO se imprimen) -->
    <div class="flex gap-3 mt-6 no-print">
        <button onclick="window.print()"
            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-semibold">
            🖨️ Imprimir Reporte
        </button>

        <a href="{{ route('admin.caja.descargar-pdf', $caja->id) }}"
            class="flex-1 bg-red-600 hover:bg-red-700 text-white py-3 rounded-lg font-semibold text-center">
            📄 Descargar PDF
        </a>

        <a href="{{ route('admin.caja.descargar-excel', $caja->id) }}"
            class="flex-1 bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg font-semibold text-center">
            📊 Descargar Excel
        </a>

        <a href="{{ route('admin.caja.index') }}"
            class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 py-3 rounded-lg font-semibold text-center">
            Volver
        </a>
    </div>
    </div>

    <!-- CSS para impresión -->
    <style>
        @media print {

            /* Ocultar elementos que no deben imprimirse */
            .no-print {
                display: none !important;
            }

            /* Ajustar márgenes de página */
            @page {
                margin: 1.5cm;
            }

            /* Asegurar que el contenido no se corte */
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }

            /* Evitar saltos de página en tablas */
            table {
                page-break-inside: avoid;
            }

            /* Mantener colores de fondo en impresión */
            .bg-blue-50,
            .bg-green-50,
            .bg-yellow-50,
            .bg-gray-50,
            .bg-gray-100,
            .bg-gray-200 {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
@endsection