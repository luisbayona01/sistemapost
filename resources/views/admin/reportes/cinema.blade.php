@extends('layouts.app')

@section('title', 'Reportes de Cine')

@section('content')
    <div class="px-6 py-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Reportes de Cine</h1>
                <p class="text-slate-600 mt-1">Análisis de ventas, ocupación y rendimiento de películas.</p>
            </div>
            <div class="flex gap-3">
                <button onclick="window.print()"
                    class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 text-slate-700 font-semibold rounded-lg hover:bg-slate-50 transition-colors shadow-sm">
                    <i class="fas fa-print mr-2"></i> Imprimir
                </button>
                <button
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">
                    <i class="fas fa-download mr-2"></i> Exportar Excel
                </button>
            </div>
        </div>

        <!-- Filtros -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 mb-8">
            <form action="{{ route('admin.reportes.cinema') }}" method="GET"
                class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Fecha Desde</label>
                    <input type="date" name="start_date"
                        class="w-full px-4 py-2 rounded-xl border border-slate-200 outline-none focus:border-indigo-500 transition-all font-semibold"
                        value="{{ $startDate }}">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Fecha Hasta</label>
                    <input type="date" name="end_date"
                        class="w-full px-4 py-2 rounded-xl border border-slate-200 outline-none focus:border-indigo-500 transition-all font-semibold"
                        value="{{ $endDate }}">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Película</label>
                    <select name="pelicula_id" onchange="this.form.submit()"
                        class="w-full px-4 py-2 rounded-xl border border-slate-200 outline-none focus:border-indigo-500 transition-all font-semibold appearance-none bg-white">
                        <option value="">Todas las películas</option>
                        @foreach($peliculas as $peli)
                            <option value="{{ $peli->id }}" {{ $peliculaId == $peli->id ? 'selected' : '' }}>
                                {{ $peli->titulo }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Función
                        (Opcional)</label>
                    <select name="funcion_id"
                        class="w-full px-4 py-2 rounded-xl border border-slate-200 outline-none focus:border-indigo-500 transition-all font-semibold appearance-none bg-white">
                        <option value="">Todas las funciones</option>
                        @foreach($funcionesList as $f)
                            <option value="{{ $f->id }}" {{ $funcionId == $f->id ? 'selected' : '' }}>
                                {{ $f->fecha_hora->format('d/m H:i') }} - {{ $f->sala->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit"
                    class="bg-slate-900 text-white px-6 py-2 rounded-xl font-bold hover:bg-slate-800 transition-all h-[42px]">
                    <i class="fas fa-search mr-2"></i> Filtrar
                </button>
            </form>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center text-xl">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                </div>
                <h3 class="text-slate-500 text-sm font-semibold uppercase tracking-wider">Ingresos Taquilla</h3>
                <p class="text-2xl font-bold text-slate-900 mt-1">${{ number_format($ingresosTickets, 2) }}</p>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center text-xl">
                        <i class="fas fa-ticket-alt"></i>
                    </div>
                </div>
                <h3 class="text-slate-500 text-sm font-semibold uppercase tracking-wider">Boletos Vendidos</h3>
                <p class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($boletosVendidos) }}</p>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="w-12 h-12 bg-purple-100 text-purple-600 rounded-xl flex items-center justify-center text-xl">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <h3 class="text-slate-500 text-sm font-semibold uppercase tracking-wider">Ocupación Promedio</h3>
                <p class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($ocupacionPromedio, 1) }}%</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Películas más taquilleras -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-bold text-slate-800">Top Películas</h3>
                    <i class="fas fa-award text-amber-400"></i>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @forelse($topMovies as $movie)
                            <div
                                class="flex justify-between items-center border-b border-slate-50 pb-2 last:border-0 last:pb-0">
                                <div>
                                    <p class="font-bold text-slate-800">{{ $movie->nombre }}</p>
                                    <p class="text-xs text-slate-500">{{ $movie->tickets_vendidos }} entradas</p>
                                </div>
                                <span
                                    class="font-mono text-emerald-600 font-bold">${{ number_format($movie->total_ventas, 2) }}</span>
                            </div>
                        @empty
                            <p class="text-center text-slate-400 py-4 italic text-sm">Sin datos registrados.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Ocupación por Sala -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-bold text-slate-800">Ocupación por Sala</h3>
                    <i class="fas fa-chart-pie text-blue-400"></i>
                </div>
                <div class="p-6">
                    <div class="space-y-6">
                        <div class="flex flex-col gap-4">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-semibold text-slate-700">Sala 1</span>
                                <span class="text-xs font-bold text-slate-500">0%</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2">
                                <div class="bg-blue-500 h-2 rounded-full" style="width: 0%"></div>
                            </div>

                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-semibold text-slate-700">Sala 2</span>
                                <span class="text-xs font-bold text-slate-500">0%</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2">
                                <div class="bg-emerald-500 h-2 rounded-full" style="width: 0%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
