@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <h1 class="text-3xl font-bold mb-6">🍿 Reporte de Confitería</h1>

        <form method="GET" class="mb-6 flex gap-3">
            <input type="date" name="fecha_inicio" value="{{ $fechaInicio }}" class="border rounded px-3 py-2">
            <input type="date" name="fecha_fin" value="{{ $fechaFin }}" class="border rounded px-3 py-2">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Filtrar</button>
        </form>

        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-bold mb-4">Ventas Totales</h2>
            <p class="text-4xl font-bold text-green-600">${{ number_format($totalVentas, 0) }}</p>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <h2 class="text-xl font-bold p-6 border-b">Top 10 Productos Vendidos</h2>
            <table class="w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left">Producto</th>
                        <th class="px-4 py-3 text-right">Cantidad Vendida</th>
                        <th class="px-4 py-3 text-right">Ingreso Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($topProductos as $producto)
                        <tr>
                            <td class="px-4 py-3 font-semibold">{{ $producto->nombre }}</td>
                            <td class="px-4 py-3 text-right">{{ $producto->total_vendido }}</td>
                            <td class="px-4 py-3 text-right font-bold">${{ number_format($producto->ingreso_total, 0) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
