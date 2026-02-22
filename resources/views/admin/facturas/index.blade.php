@extends('layouts.admin')
@section('content')
    <div class="container mx-auto px-4 py-6">

        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900">📦 Historial de Compras</h1>
                <p class="text-gray-500 mt-1">Consulta y gestiona las facturas de tus proveedores.</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.facturas.reporte-mensual') }}"
                    class="bg-indigo-50 text-indigo-600 border-2 border-indigo-100 hover:bg-indigo-100 px-6 py-3 rounded-2xl transition font-black flex items-center gap-2">
                    📊 Ver Reporte Mensual
                </a>
                <a href="{{ route('admin.facturas.crear') }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-2xl shadow-lg shadow-blue-200 transition font-black flex items-center gap-2">
                    + Nueva Factura
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded">
                {{ session('success') }}
            </div>
        @endif

        <!-- Resumen del mes -->
        <div class="bg-indigo-600 text-white rounded-lg p-6 mb-6 shadow-lg">
            <p class="text-sm opacity-90 uppercase tracking-wider font-semibold">Total Pagado -
                {{ now()->locale('es')->isoFormat('MMMM YYYY') }}
            </p>
            <p class="text-4xl font-bold">${{ number_format($totalMes, 0) }}</p>
        </div>

        <!-- Tabla de facturas -->
        <div class="bg-white rounded-xl shadow-xl overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-900 text-white">
                    <tr>
                        <th class="px-4 py-4 text-left">Factura</th>
                        <th class="px-4 py-4 text-left">Fecha</th>
                        <th class="px-4 py-4 text-left">Proveedor</th>
                        <th class="px-4 py-4 text-left">Detalles de Factura</th>
                        <th class="px-4 py-4 text-right">Total Pagado</th>
                        <th class="px-4 py-4 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($facturas as $factura)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-4">
                                <span class="font-bold text-indigo-600">#{{ $factura->numero_factura ?? $factura->id }}</span>
                            </td>
                            <td class="px-4 py-4 text-gray-600">{{ $factura->fecha_compra->format('d/m/Y') }}</td>
                            <td class="px-4 py-4 font-semibold">{{ $factura->proveedor->persona->razon_social ?? 'N/A' }}</td>
                            <td class="px-4 py-4">
                                @forelse($factura->movimientos as $mov)
                                    <div class="text-xs mb-1 border-b border-gray-50 last:border-0">
                                        <span class="font-bold">{{ $mov->producto->nombre }}</span>: 
                                        {{ number_format($mov->cantidad, 0) }} un. 
                                        (${{ number_format($mov->costo_unitario, 0) }} c/u)
                                    </div>
                                @empty
                                    <span class="text-red-500 text-xs">Sin detalle</span>
                                @endforelse
                            </td>
                            <td class="px-4 py-4 text-right">
                                <div class="text-right">
                                    <p class="font-bold text-lg text-gray-900">${{ number_format($factura->total_pagado, 0) }}
                                    </p>
                                    @if($factura->impuesto_valor > 0)
                                        <p class="text-xs text-green-600">Impuesto:
                                            ${{ number_format($factura->impuesto_valor, 0) }}</p>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-4 text-center space-x-2">
                                <a href="{{ route('admin.facturas.editar', $factura->id) }}"
                                    class="inline-flex items-center bg-amber-100 hover:bg-amber-200 text-amber-800 px-3 py-1 rounded font-bold text-sm transition">
                                    ✏️ Editar
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-gray-400 italic">
                                No se han registrado facturas recientemente.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $facturas->links() }}</div>
    </div>
@endsection