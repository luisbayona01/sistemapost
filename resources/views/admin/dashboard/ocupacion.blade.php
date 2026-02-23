@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">📊 Reporte de Asistencia</h1>
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

        <!-- Tabla de Funciones -->
        <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
            <div class="bg-blue-600 text-white p-4">
                <h2 class="text-xl font-bold">🎬 Asistencia por Función</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left">Película</th>
                            <th class="px-4 py-3 text-left">Sala</th>
                            <th class="px-4 py-3 text-left">Fecha/Hora</th>
                            <th class="px-4 py-3 text-right">Vendidos</th>
                            <th class="px-4 py-3 text-right">Disponibles</th>
                            <th class="px-4 py-3 text-right">Asistencia (%)</th>
                            <th class="px-4 py-3 text-right">Venta ($)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($funciones as $funcion)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <span class="font-semibold">{{ $funcion['pelicula'] }}</span>
                                </td>
                                <td class="px-4 py-3">{{ $funcion['sala'] }}</td>
                                <td class="px-4 py-3">
                                    <div class="text-sm">
                                        <div>{{ $funcion['fecha_hora'] }}</div>
                                        <div class="text-gray-500">{{ $funcion['dia_semana'] }}</div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-right font-semibold">
                                    {{ $funcion['asientos_vendidos'] }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    {{ $funcion['asientos_disponibles'] }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <span
                                        class="px-3 py-1 rounded-full text-sm font-bold
                                                        {{ $funcion['ocupacion_porcentaje'] >= 80 ? 'bg-green-100 text-green-800' : '' }}
                                                        {{ $funcion['ocupacion_porcentaje'] >= 50 && $funcion['ocupacion_porcentaje'] < 80 ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                        {{ $funcion['ocupacion_porcentaje'] < 50 ? 'bg-red-100 text-red-800' : '' }}">
                                        {{ $funcion['ocupacion_porcentaje'] }}%
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right font-bold text-green-600">
                                    ${{ number_format($funcion['ingreso_total'], 0) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                    No hay funciones en este período
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mapa de Calor (Heatmap Simplificado) -->
        @if($heatmapData->isNotEmpty())
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4">🔥 Mapa de Calor: Horarios Más Populares</h2>

                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr>
                                <th class="border p-2 bg-gray-100">Día / Hora</th>
                                @php
                                    // Obtener todos los horarios únicos
                                    $horarios = collect();
                                    foreach ($heatmapData as $dia => $horas) {
                                        $horarios = $horarios->merge($horas->keys());
                                    }
                                    $horarios = $horarios->unique()->sort()->values();
                                @endphp
                                @foreach($horarios as $hora)
                                    <th class="border p-2 bg-gray-100 text-sm">{{ $hora }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($heatmapData as $dia => $horas)
                                <tr>
                                    <td class="border p-2 font-semibold bg-gray-50">{{ $dia }}</td>
                                    @foreach($horarios as $hora)
                                        @php
                                            $data = $horas->get($hora);
                                            $ocupacion = $data['ocupacion_promedio'] ?? 0;

                                            // Determinar color según ocupación
                                            if ($ocupacion >= 80) {
                                                $color = 'bg-green-500 text-white';
                                            } elseif ($ocupacion >= 60) {
                                                $color = 'bg-green-300';
                                            } elseif ($ocupacion >= 40) {
                                                $color = 'bg-yellow-300';
                                            } elseif ($ocupacion >= 20) {
                                                $color = 'bg-orange-300';
                                            } elseif ($ocupacion > 0) {
                                                $color = 'bg-red-300';
                                            } else {
                                                $color = 'bg-gray-100';
                                            }
                                        @endphp
                                        <td class="border p-2 text-center {{ $color }}">
                                            @if($ocupacion > 0)
                                                <div class="text-sm font-bold">{{ round($ocupacion, 0) }}%</div>
                                                <div class="text-xs opacity-75">({{ $data['funciones_count'] ?? 0 }})</div>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 flex gap-6 text-sm">
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 bg-green-500 rounded"></div>
                        <span>≥80% (Excelente)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 bg-green-300 rounded"></div>
                        <span>60-79% (Bueno)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 bg-yellow-300 rounded"></div>
                        <span>40-59% (Regular)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 bg-orange-300 rounded"></div>
                        <span>20-39% (Bajo)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 bg-red-300 rounded"></div>
                        <span>
                            <20% (Muy Bajo)</span>
                    </div>
                </div>
            </div>
        @endif

    </div>
@endsection
