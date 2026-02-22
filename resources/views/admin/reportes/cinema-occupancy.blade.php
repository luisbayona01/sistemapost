@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-black text-slate-900 uppercase tracking-tighter">📊 Reporte de Ocupación - Cinema</h1>
            <div class="bg-indigo-100 text-indigo-700 px-4 py-2 rounded-xl font-bold text-sm uppercase">
                {{ now()->translatedFormat('F Y') }}
            </div>
        </div>

        <!-- Filtros -->
        <div class="bg-white p-6 rounded-[2rem] shadow-xl border border-slate-100 mb-8">
            <form method="GET" class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Fecha
                        Inicio</label>
                    <input type="date" name="fecha_inicio" value="{{ $fechaInicio }}"
                        class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl px-4 py-3 font-bold text-slate-700 focus:border-indigo-500 transition-all outline-none">
                </div>
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Fecha
                        Fin</label>
                    <input type="date" name="fecha_fin" value="{{ $fechaFin }}"
                        class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl px-4 py-3 font-bold text-slate-700 focus:border-indigo-500 transition-all outline-none">
                </div>
                <button type="submit"
                    class="bg-slate-900 hover:bg-black text-white px-8 py-3.5 rounded-xl font-black uppercase tracking-widest transition-all transform hover:-translate-y-1 active:scale-95 shadow-lg shadow-slate-900/20">
                    Filtrar
                </button>
            </form>
        </div>

        <!-- Tabla de funciones -->
        <div class="bg-white rounded-[2rem] shadow-2xl border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-slate-900 text-white">
                            <th class="px-6 py-5 text-left text-[10px] font-black uppercase tracking-widest">Película</th>
                            <th class="px-6 py-5 text-left text-[10px] font-black uppercase tracking-widest">Sala</th>
                            <th class="px-6 py-5 text-left text-[10px] font-black uppercase tracking-widest">Fecha/Hora</th>
                            <th class="px-6 py-5 text-right text-[10px] font-black uppercase tracking-widest">Vendidos</th>
                            <th class="px-6 py-5 text-right text-[10px] font-black uppercase tracking-widest">Disponibles
                            </th>
                            <th class="px-6 py-5 text-center text-[10px] font-black uppercase tracking-widest">Ocupación
                            </th>
                            <th class="px-6 py-5 text-right text-[10px] font-black uppercase tracking-widest">Ingreso</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($funciones as $funcion)
                            <tr class="hover:bg-slate-50/80 transition-colors group">
                                <td class="px-6 py-4 font-black text-slate-900 uppercase text-sm tracking-tight">
                                    {{ $funcion['pelicula'] }}</td>
                                <td class="px-6 py-4">
                                    <span
                                        class="bg-slate-100 text-slate-600 px-3 py-1 rounded-lg text-[10px] font-black uppercase">
                                        {{ $funcion['sala'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-slate-500 text-xs font-bold">{{ $funcion['fecha_hora'] }}</td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-indigo-600 font-black text-sm">{{ $funcion['asientos_vendidos'] }}</span>
                                </td>
                                <td class="px-6 py-4 text-right text-slate-400 font-bold text-xs">
                                    {{ $funcion['asientos_disponibles'] }}</td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex flex-col items-center gap-1">
                                        <div class="w-24 h-2 bg-slate-100 rounded-full overflow-hidden">
                                            <div class="h-full {{ $funcion['ocupacion_porcentaje'] >= 80 ? 'bg-emerald-500' : ($funcion['ocupacion_porcentaje'] >= 50 ? 'bg-amber-500' : 'bg-rose-500') }}"
                                                style="width: {{ $funcion['ocupacion_porcentaje'] }}%"></div>
                                        </div>
                                        <span
                                            class="text-[10px] font-black {{ $funcion['ocupacion_porcentaje'] >= 80 ? 'text-emerald-600' : ($funcion['ocupacion_porcentaje'] >= 50 ? 'text-amber-600' : 'text-rose-600') }}">
                                            {{ $funcion['ocupacion_porcentaje'] }}%
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span
                                        class="text-lg font-black text-slate-900">${{ number_format($funcion['ingreso_total'], 0) }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7"
                                    class="px-6 py-20 text-center text-slate-400 font-bold uppercase text-xs tracking-widest italic">
                                    No se encontraron funciones en este rango de fechas
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
