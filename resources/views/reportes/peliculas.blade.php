@extends('layouts.admin')

@section('title', 'Ventas por Película')

@section('content')
    <div class="p-6 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                <div>
                    <h1 class="text-3xl font-black text-slate-900 tracking-tight">Ventas por Película</h1>
                    <p class="text-slate-500 font-bold uppercase tracking-widest text-xs">Control Gerencial de Audiencia</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <form action="{{ route('admin.reportes.peliculas') }}" method="GET" class="flex flex-wrap items-center gap-3 bg-white p-2 rounded-2xl shadow-sm border border-slate-100">
                        <input type="date" name="fecha" value="{{ $fecha }}" class="border-0 bg-slate-50 rounded-xl px-4 py-2 text-sm font-bold text-slate-700 outline-none focus:ring-2 focus:ring-slate-900 transition-all">
                        
                        <select name="sala_id" class="border-0 bg-slate-50 rounded-xl px-4 py-2 text-sm font-bold text-slate-700 outline-none focus:ring-2 focus:ring-slate-900 transition-all">
                            <option value="">Todas las Salas</option>
                            @foreach($salas as $sala)
                                <option value="{{ $sala->id }}" {{ $salaId == $sala->id ? 'selected' : '' }}>{{ $sala->nombre }}</option>
                            @endforeach
                        </select>

                        <button type="submit" class="bg-slate-900 text-white px-6 py-2 rounded-xl text-sm font-black hover:bg-slate-800 transition-all">
                            Filtrar
                        </button>

                        <a href="{{ route('admin.reportes.peliculas.export', request()->all()) }}" class="bg-emerald-500 text-white px-6 py-2 rounded-xl text-sm font-black hover:bg-emerald-600 transition-all flex items-center gap-2">
                            <i class="fas fa-file-excel"></i> Exportar
                        </a>
                    </form>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Columna Izquierda: Hoy/Seleccionado -->
                <div class="lg:col-span-2 space-y-8">
                    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
                        <div class="p-6 border-b border-slate-50 bg-slate-50/50 flex justify-between items-center">
                            <h2 class="font-black text-slate-800 uppercase tracking-tighter flex items-center gap-2">
                                <i class="fas fa-bolt text-amber-500"></i> Desempeño Operativo
                            </h2>
                            <span class="text-xs font-black text-slate-400">FECHA: {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</span>
                        </div>
                        <div class="p-6">
                            @if($hoy->isEmpty())
                                <div class="text-center py-12">
                                    <i class="fas fa-film text-4xl text-slate-200 mb-4"></i>
                                    <p class="text-slate-400 font-bold italic">No hay ventas registradas para el criterio seleccionado</p>
                                </div>
                            @else
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left">
                                        <thead>
                                            <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">
                                                <th class="pb-3">Película</th>
                                                <th class="pb-3 text-center">Boletos</th>
                                                <th class="pb-3 text-right">Recaudación</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-50">
                                            @foreach($hoy as $p)
                                                <tr class="group hover:bg-slate-50/50 transition-colors">
                                                    <td class="py-4 font-black text-slate-900">{{ $p->pelicula }}</td>
                                                    <td class="py-4 text-center">
                                                        <span class="bg-slate-100 px-3 py-1 rounded-full text-xs font-black text-slate-600">
                                                            {{ $p->cantidad_boletos }}
                                                        </span>
                                                    </td>
                                                    <td class="py-4 text-right font-black text-emerald-600">
                                                        ${{ number_format($p->monto_pesos, 0) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Detalle por Función (Sugerido por el usuario) -->
                    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
                        <div class="p-6 border-b border-slate-50 bg-slate-50/50 flex justify-between items-center">
                            <h2 class="font-black text-slate-800 uppercase tracking-tighter flex items-center gap-2">
                                <i class="fas fa-clock text-blue-500"></i> Detalle por Función y Horario
                            </h2>
                        </div>
                        <div class="p-0">
                            <div class="overflow-x-auto">
                                <table class="w-full text-left">
                                    <thead class="bg-slate-50">
                                        <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                            <th class="p-6">Película</th>
                                            <th class="p-6">Sala</th>
                                            <th class="p-6 text-center">Hora</th>
                                            <th class="p-6 text-center">Boletos</th>
                                            <th class="p-6 text-right">Monto</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-50">
                                        @forelse($funcionesDetalle as $fd)
                                            <tr class="hover:bg-slate-50/50 transition-colors">
                                                <td class="p-6 font-bold text-slate-900">{{ $fd->pelicula }}</td>
                                                <td class="p-6">
                                                    <span class="bg-blue-50 text-blue-700 px-3 py-1 rounded-lg text-[10px] font-black uppercase">
                                                        {{ $fd->sala }}
                                                    </span>
                                                </td>
                                                <td class="p-6 text-center font-bold text-slate-600">
                                                    {{ \Carbon\Carbon::parse($fd->fecha_hora)->format('h:i A') }}
                                                </td>
                                                <td class="p-6 text-center">
                                                    <span class="font-black text-slate-900">{{ $fd->boletos }}</span>
                                                </td>
                                                <td class="p-6 text-right font-black text-emerald-600">
                                                    ${{ number_format($fd->monto, 0) }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="p-8 text-center text-slate-400 italic">No hay funciones con ventas registradas</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Columna Derecha: Participación (Sidebar) -->
                <div class="space-y-8">
                    <div class="bg-slate-900 rounded-[2rem] p-8 text-white shadow-xl relative overflow-hidden">
                        <div class="relative z-10">
                            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-6">Participación (Últimas 4 Semanas)</h3>
                            <div class="h-64 mb-6">
                                <canvas id="chartPeliculas"></canvas>
                            </div>
                            <div class="space-y-3">
                                @foreach($ultimoMes->take(5) as $m)
                                    <div class="flex justify-between items-center text-xs">
                                        <span class="text-slate-400 font-bold truncate mr-4">{{ $m->pelicula }}</span>
                                        <span class="font-black text-emerald-400">{{ number_format($m->porcentaje, 1) }}%</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="absolute -right-20 -bottom-20 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl"></div>
                    </div>
                </div>
            </div>

            <!-- Tabla Resumen Mensual (Pie de página) -->
            <div class="mt-8 bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-6 border-b border-slate-50 flex justify-between items-center">
                    <h2 class="font-black text-slate-800 uppercase tracking-tighter">Histórico Mensual Detallado</h2>
                </div>
                <div class="p-0">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-slate-50">
                                <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                    <th class="p-6">Película</th>
                                    <th class="p-6 text-center">Total Boletos</th>
                                    <th class="p-6 text-right">Total Monto</th>
                                    <th class="p-6 text-center">% Participación</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach($ultimoMes as $m)
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="p-6 font-bold text-slate-700">{{ $m->pelicula }}</td>
                                        <td class="p-6 text-center font-black text-slate-900">{{ number_format($m->boletos) }}</td>
                                        <td class="p-6 text-right font-black text-slate-900">${{ number_format($m->monto, 0) }}</td>
                                        <td class="p-6">
                                            <div class="flex items-center gap-3">
                                                <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden">
                                                    <div class="h-full bg-emerald-500 rounded-full" style="width: {{ $m->porcentaje }}%"></div>
                                                </div>
                                                <span class="text-[10px] font-black text-slate-400">{{ number_format($m->porcentaje, 1) }}%</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('chartPeliculas').getContext('2d');
            const data = {
                labels: {!! json_encode($ultimoMes->pluck('pelicula')) !!},
                datasets: [{
                    data: {!! json_encode($ultimoMes->pluck('monto')) !!},
                    backgroundColor: [
                        '#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#64748b'
                    ],
                    borderWidth: 0,
                    hoverOffset: 15
                }]
            };

            new Chart(ctx, {
                type: 'doughnut',
                data: data,
                options: {
                    cutout: '75%',
                    plugins: {
                        legend: { display: false }
                    },
                    maintainAspectRatio: false
                }
            });
        });
    </script>
@endsection
