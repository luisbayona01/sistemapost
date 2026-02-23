@extends('layouts.admin')

@section('content')
    <div class="container mx-auto px-4 py-6">

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">🍿 Reporte de Dulcería</h1>
            <a href="{{ route('admin.dashboard.index') }}" class="bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded">
                ← Volver al Dashboard
            </a>
        </div>

        <!-- Filtros -->
        <form method="GET" class="mb-6 flex gap-3 bg-white p-4 rounded-lg shadow">
            <div>
                <label class="block text-sm font-semibold mb-1">Desde:</label>
                <input type="date" name="fecha_inicio" value="{{ $fechaInicio }}" class="border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Hasta:</label>
                <input type="date" name="fecha_fin" value="{{ $fechaFin }}" class="border rounded px-3 py-2">
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded">
                    Filtrar
                </button>
            </div>
        </form>

        <!-- Total de Ventas -->
        <div class="bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg shadow p-8 mb-6">
            <p class="text-sm opacity-90 mb-2">Ventas Totales Dulcería (Período)</p>
            <p class="text-5xl font-bold">${{ number_format($totalVentas, 0) }}</p>
        </div>

        <div class="grid grid-cols-2 gap-6">

            <!-- Productos con Margen Real -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="bg-green-600 text-white p-4">
                    <h2 class="text-xl font-bold">💰 Análisis de Rentabilidad por Producto</h2>
                    <p class="text-sm opacity-90">Productos con costo calculado</p>
                </div>

                @if($productosConMargen->isEmpty())
                    <div class="p-8 text-center text-gray-500">
                        <p class="mb-2">⚠️ No hay productos con costo calculado</p>
                        <p class="text-sm">Agrega recetas a los productos para ver su margen real</p>
                    </div>
                @else
                    <table class="w-full">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-left">Producto</th>
                                <th class="px-4 py-2 text-right">Precio</th>
                                <th class="px-4 py-2 text-right">Costo</th>
                                <th class="px-4 py-2 text-right">Ganancia ($)</th>
                                <th class="px-4 py-2 text-right">Rentabilidad (%)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach($productosConMargen as $producto)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 font-semibold">{{ $producto->nombre }}</td>
                                    <td class="px-4 py-2 text-right">${{ number_format($producto->precio, 0) }}</td>
                                    <td class="px-4 py-2 text-right text-red-600">
                                        ${{ number_format($producto->costo_total_unitario, 0) }}
                                    </td>
                                    <td class="px-4 py-2 text-right font-bold text-green-600">
                                        ${{ number_format($producto->margen_absoluto, 0) }}
                                    </td>
                                    <td class="px-4 py-2 text-right">
                                        <span class="px-2 py-1 rounded-full text-sm font-bold
                                                        {{ $producto->margen_porcentual >= 35 ? 'bg-green-100 text-green-800' : '' }}
                                                        {{ $producto->margen_porcentual >= 20 && $producto->margen_porcentual < 35 ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                        {{ $producto->margen_porcentual < 20 ? 'bg-red-100 text-red-800' : '' }}">
                                            {{ number_format($producto->margen_porcentual, 1) }}%
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            <!-- Productos con Bajo Movimiento -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="bg-orange-600 text-white p-4">
                    <h2 class="text-xl font-bold">⚠️ Productos Estancados</h2>
                    <p class="text-sm opacity-90">Sin ventas en el período seleccionado</p>
                </div>

                @if($productosBajoMovimiento->isEmpty())
                    <div class="p-8 text-center text-green-600">
                        <p class="text-2xl mb-2">✅</p>
                        <p class="font-semibold">Todos los productos tienen ventas</p>
                        <p class="text-sm opacity-75">Excelente rotación de inventario</p>
                    </div>
                @else
                    <table class="w-full">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-left">Producto</th>
                                <th class="px-4 py-2 text-right">Stock</th>
                                <th class="px-4 py-2 text-right">Precio</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach($productosBajoMovimiento as $producto)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2">
                                        <div class="flex items-center gap-2">
                                            <span class="text-orange-500">⚠️</span>
                                            <span class="font-semibold">{{ $producto->nombre }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2 text-right">{{ $producto->stock_actual }}</td>
                                    <td class="px-4 py-2 text-right">${{ number_format($producto->precio, 0) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="p-4 bg-yellow-50 border-t">
                        <p class="text-sm text-yellow-800">
                            💡 <strong>Recomendación:</strong> Considera ajustar precios o promocionar estos productos
                        </p>
                    </div>
                @endif
            </div>

        </div>

        <!-- Métricas Adicionales -->
        <div class="grid grid-cols-3 gap-4 mt-6">

            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm mb-2">Productos Alta Rentabilidad</p>
                <p class="text-3xl font-bold text-green-600">
                    {{ $productosConMargen->where('margen_porcentual', '>=', 35)->count() }}
                </p>
                <p class="text-sm text-gray-500 mt-1">(≥35% de margen)</p>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm mb-2">Productos Rentabilidad Media</p>
                <p class="text-3xl font-bold text-yellow-600">
                    {{ $productosConMargen->whereBetween('margen_porcentual', [20, 34.9])->count() }}
                </p>
                <p class="text-sm text-gray-500 mt-1">(20-34% de margen)</p>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm mb-2">Productos Baja Rentabilidad</p>
                <p class="text-3xl font-bold text-red-600">
                    {{ $productosConMargen->where('margen_porcentual', '<', 20)->count() }}
                </p>
                <p class="text-sm text-gray-500 mt-1">(<20% de margen)</p>
            </div>

        </div>

    </div>
@endsection
