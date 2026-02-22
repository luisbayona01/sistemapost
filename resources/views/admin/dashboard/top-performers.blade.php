@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">🏆 Ranking de Ventas</h1>
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

        <div class="grid grid-cols-2 gap-6">

            <!-- Top Películas -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="bg-purple-600 text-white p-4">
                    <h2 class="text-xl font-bold">🎬 Top 10 Películas por Recaudación</h2>
                </div>
                <table class="w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left">#</th>
                            <th class="px-4 py-2 text-left">Película</th>
                            <th class="px-4 py-2 text-right">Tickets</th>
                            <th class="px-4 py-2 text-right">Ventas ($)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($topPeliculas as $index => $pelicula)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 font-bold">{{ $index + 1 }}</td>
                                <td class="px-4 py-2">{{ $pelicula->titulo }}</td>
                                <td class="px-4 py-2 text-right">{{ $pelicula->tickets_vendidos }}</td>
                                <td class="px-4 py-2 text-right font-semibold text-green-600">
                                    ${{ number_format($pelicula->ingreso_total, 0) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                    No hay datos en este período
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Top Productos por Unidades -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="bg-orange-600 text-white p-4">
                    <h2 class="text-xl font-bold">🍿 Top 10 Dulces/Snacks (Unidades)</h2>
                </div>
                <table class="w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left">#</th>
                            <th class="px-4 py-2 text-left">Producto</th>
                            <th class="px-4 py-2 text-right">Unidades</th>
                            <th class="px-4 py-2 text-right">Ventas ($)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($topProductosUnidades as $index => $producto)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 font-bold">{{ $index + 1 }}</td>
                                <td class="px-4 py-2">{{ $producto->nombre }}</td>
                                <td class="px-4 py-2 text-right font-semibold">{{ $producto->total_vendido }}</td>
                                <td class="px-4 py-2 text-right text-green-600">
                                    ${{ number_format($producto->ingreso_total, 0) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                    No hay datos en este período
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Top Productos por Ingreso -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="bg-green-600 text-white p-4">
                    <h2 class="text-xl font-bold">💰 Top 10 Dulces/Snacks (Venta Total)</h2>
                </div>
                <table class="w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left">#</th>
                            <th class="px-4 py-2 text-left">Producto</th>
                            <th class="px-4 py-2 text-right">Unidades</th>
                            <th class="px-4 py-2 text-right">Ventas ($)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($topProductosIngreso as $index => $producto)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 font-bold">{{ $index + 1 }}</td>
                                <td class="px-4 py-2">{{ $producto->nombre }}</td>
                                <td class="px-4 py-2 text-right">{{ $producto->total_vendido }}</td>
                                <td class="px-4 py-2 text-right font-semibold text-green-600">
                                    ${{ number_format($producto->ingreso_total, 0) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                    No hay datos en este período
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Horarios Rentables -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="bg-blue-600 text-white p-4">
                    <h2 class="text-xl font-bold">🕐 Horarios con Mayor Venta</h2>
                </div>
                <table class="w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left">Horario</th>
                            <th class="px-4 py-2 text-right">Funciones</th>
                            <th class="px-4 py-2 text-right">Tickets</th>
                            <th class="px-4 py-2 text-right">Ventas ($)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($horariosRentables as $horario)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 font-bold">{{ str_pad($horario->hora, 2, '0', STR_PAD_LEFT) }}:00</td>
                                <td class="px-4 py-2 text-right">{{ $horario->total_funciones }}</td>
                                <td class="px-4 py-2 text-right">{{ $horario->tickets_vendidos }}</td>
                                <td class="px-4 py-2 text-right font-semibold text-green-600">
                                    ${{ number_format($horario->ingreso_total, 0) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                    No hay datos en este período
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>

    </div>
@endsection
