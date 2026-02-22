@extends('layouts.admin')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8">

            <!-- HEADER & FILTERS -->
            <div class="flex flex-col md:flex-row justify-between items-center mb-10 gap-6">
                <div>
                    <h1 class="text-4xl font-black text-gray-900">📊 Reporte de Compras</h1>
                    <p class="text-gray-500 font-medium">Resumen detallado del mes seleccionado.</p>
                </div>

                <form action="{{ route('admin.facturas.reporte-mensual') }}" method="GET"
                    class="flex items-center gap-4 bg-gray-50 p-4 rounded-2xl border border-gray-100">
                    <div>
                        <select name="mes" class="border-none bg-transparent font-bold text-gray-700 focus:ring-0">
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}" {{ $m == $mes ? 'selected' : '' }}>
                                    {{ Carbon\Carbon::create(2000, $m, 1)->locale('es')->monthName }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <select name="anio" class="border-none bg-transparent font-bold text-gray-700 focus:ring-0">
                            @foreach(range(date('Y'), date('Y') - 5) as $y)
                                <option value="{{ $y }}" {{ $y == $anio ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit"
                        class="bg-blue-600 text-white px-6 py-2 rounded-xl font-bold hover:bg-blue-700 transition">
                        Filtrar
                    </button>
                </form>
            </div>

            <!-- KPI CARDS -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-12">
                <div class="bg-indigo-50 p-6 rounded-3xl border-2 border-indigo-100">
                    <p class="text-indigo-600 text-xs font-black uppercase tracking-wider mb-2">Conteo Facturas</p>
                    <p class="text-4xl font-black text-indigo-900">{{ $totales['conteo'] }}</p>
                </div>
                <div class="bg-emerald-50 p-6 rounded-3xl border-2 border-emerald-100">
                    <p class="text-emerald-600 text-xs font-black uppercase tracking-wider mb-2">Total Neto</p>
                    <p class="text-4xl font-black text-emerald-900"> ${{ number_format($totales['neto'], 0) }} </p>
                </div>
                <div class="bg-amber-50 p-6 rounded-3xl border-2 border-amber-100">
                    <p class="text-amber-600 text-xs font-black uppercase tracking-wider mb-2">Total IVA</p>
                    <p class="text-4xl font-black text-amber-900"> ${{ number_format($totales['iva'], 0) }} </p>
                </div>
                <div class="bg-gray-900 p-6 rounded-3xl border-2 border-gray-800 text-white">
                    <p class="text-gray-400 text-xs font-black uppercase tracking-wider mb-2">Total Pagado</p>
                    <p class="text-4xl font-black text-white"> ${{ number_format($totales['total'], 0) }} </p>
                </div>
            </div>

            <!-- TABLE -->
            <div class="overflow-x-auto rounded-3xl border border-gray-100">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-gray-400 uppercase text-[10px] font-black tracking-widest">
                        <tr>
                            <th class="px-6 py-5">Fecha</th>
                            <th class="px-6 py-5">Factura #</th>
                            <th class="px-6 py-5">Proveedor</th>
                            <th class="px-6 py-5">Neto</th>
                            <th class="px-6 py-5">IVA</th>
                            <th class="px-6 py-5">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($facturas as $factura)
                            <tr class="hover:bg-blue-50/30 transition">
                                <td class="px-6 py-4 font-medium text-gray-600">{{ $factura->fecha_compra->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 font-bold text-gray-900">{{ $factura->numero_factura ?? 'N/A' }}</td>
                                <td class="px-6 py-4">
                                    <span
                                        class="bg-blue-100 text-blue-700 text-[10px] font-black px-2 py-1 rounded-md uppercase">
                                        {{ $factura->proveedor->persona->razon_social }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-mono text-gray-500">
                                    ${{ number_format($factura->subtotal_calculado, 0) }}</td>
                                <td class="px-6 py-4 font-mono text-gray-500">${{ number_format($factura->impuesto_valor, 0) }}
                                </td>
                                <td class="px-6 py-4 font-mono font-black text-gray-900">
                                    ${{ number_format($factura->total_pagado, 0) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-20 text-center text-gray-400 font-medium">
                                    No se encontraron facturas para este período.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-10 flex justify-end">
                <button onclick="window.print()"
                    class="bg-gray-100 text-gray-600 px-8 py-3 rounded-2xl font-bold hover:bg-gray-200 transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2-2v4a2 2 0 012 2v3m6 0h1.5m-1.5 0v3m-3.5-3.5h.01M9 16h.01">
                        </path>
                    </svg>
                    Imprimir Reporte
                </button>
            </div>
        </div>
    </div>
@endsection