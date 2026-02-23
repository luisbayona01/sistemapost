@extends('layouts.admin')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <h1 class="text-3xl font-black text-slate-900 mb-6 uppercase tracking-tight">💰 Gestión de Cajas</h1>

        <!-- Cajas Abiertas (Listado Principal) -->
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 mb-8 overflow-hidden">
            <div class="bg-slate-900 text-white p-6 flex justify-between items-center">
                <h2 class="text-xl font-black uppercase tracking-tighter">📂 Cajas Activas y Recientes</h2>
                <span class="text-xs font-bold text-slate-400">Control de Arqueo</span>
            </div>

            <div class="p-8">
                @php
                    $abiertas = $cajas->where('estado', 'ABIERTA');
                @endphp

                @if($abiertas->count() == 0)
                    <div class="text-center py-16 bg-slate-50 rounded-[2rem] border-2 border-dashed border-slate-200">
                        <p class="text-6xl mb-6">📭</p>
                        <p class="text-xl font-black text-slate-400 uppercase tracking-widest">No hay cajas abiertas</p>
                        <p class="text-sm text-slate-400 mt-2 font-bold">Las cajas se abren automáticamente con la primera venta
                            del día.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach($abiertas as $caja)
                            <div
                                class="bg-white border-2 border-slate-100 rounded-[2rem] p-8 hover:border-emerald-500 transition-all shadow-sm hover:shadow-xl group">
                                <div class="flex justify-between items-start mb-6">
                                    <div>
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Cajero
                                            Responsable</p>
                                        <p class="text-2xl font-black text-slate-900 tracking-tight">
                                            {{ $caja->user->name ?? 'N/A' }}</p>
                                    </div>
                                    <span
                                        class="bg-emerald-500 text-white px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest shadow-lg shadow-emerald-500/20 animate-pulse">
                                        ABIERTA
                                    </span>
                                </div>

                                <div class="space-y-3 mb-8">
                                    <div class="flex justify-between items-center py-2 border-b border-slate-50">
                                        <span class="text-xs font-bold text-slate-500 uppercase">Caja ID</span>
                                        <span class="text-sm font-black text-slate-900">#{{ $caja->id }}</span>
                                    </div>
                                    <div class="flex justify-between items-center py-2 border-b border-slate-50">
                                        <span class="text-xs font-bold text-slate-500 uppercase">Apertura</span>
                                        <span
                                            class="text-sm font-black text-slate-900">{{ $caja->fecha_apertura?->format('d/m/Y H:i') ?? 'N/A' }}</span>
                                    </div>
                                    <div class="flex justify-between items-center py-2 border-b border-slate-50">
                                        <span class="text-xs font-bold text-slate-500 uppercase">Base inicial</span>
                                        <span
                                            class="text-sm font-black text-slate-900">${{ number_format($caja->monto_inicial, 0) }}</span>
                                    </div>

                                    <div class="flex justify-between items-center pt-4">
                                        <span class="text-xs font-black text-slate-900 uppercase">Ventas Sistema</span>
                                        <span
                                            class="text-xl font-black text-emerald-600">${{ number_format($caja->ventas_sum_total ?? 0, 0) }}</span>
                                    </div>
                                </div>

                                <a href="{{ route('admin.caja.cerrar-simple', $caja->id) }}"
                                    class="block w-full bg-slate-900 hover:bg-slate-800 text-white text-center py-4 rounded-2xl font-black uppercase tracking-widest text-xs transition-all shadow-lg shadow-slate-900/10 active:scale-95">
                                    🔒 Realizar Arqueo y Cerrar
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Historial de Cajas Cerradas -->
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
            <div class="bg-slate-50 p-6 border-b border-slate-100">
                <h2 class="text-sm font-black text-slate-900 uppercase tracking-widest">📋 Historial de Cierres Recientes
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50/50">
                        <tr>
                            <th class="px-8 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                ID</th>
                            <th class="px-8 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                Cajero</th>
                            <th class="px-8 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                Cierre</th>
                            <th
                                class="px-8 py-4 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                Venta Sistema</th>
                            <th
                                class="px-8 py-4 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                Diferencia</th>
                            <th
                                class="px-8 py-4 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($cajas->where('estado', 'CERRADA') as $caja)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-8 py-4 font-black text-slate-900">#{{ $caja->id }}</td>
                                <td class="px-8 py-4">
                                    <span class="font-bold text-slate-700">{{ $caja->user->name ?? 'N/A' }}</span>
                                </td>
                                <td class="px-8 py-4">
                                    <span
                                        class="text-xs font-bold text-slate-500">{{ $caja->fecha_cierre?->format('d/m/Y H:i') ?? 'N/A' }}</span>
                                </td>
                                <td class="px-8 py-4 text-right font-black text-slate-900">
                                    ${{ number_format($caja->monto_final_esperado ?? 0, 0) }}
                                </td>
                                <td class="px-8 py-4 text-right">
                                    <span class="text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-tighter
                                                {{ abs($caja->diferencia ?? 0) < 1 ? 'bg-emerald-100 text-emerald-700' : '' }}
                                                {{ ($caja->diferencia ?? 0) > 1 ? 'bg-blue-100 text-blue-700' : '' }}
                                                {{ ($caja->diferencia ?? 0) < -1 ? 'bg-rose-100 text-rose-700' : '' }}">
                                        ${{ number_format(abs($caja->diferencia ?? 0), 0) }}
                                        @if(abs($caja->diferencia ?? 0) >= 1)
                                            ({{ ($caja->diferencia ?? 0) > 0 ? 'Sobrante' : 'Faltante' }})
                                        @else
                                            (CUADRADO)
                                        @endif
                                    </span>
                                </td>
                                <td class="px-8 py-4 text-center">
                                    <a href="{{ route('admin.caja.reporte-cierre', $caja->id) }}"
                                        class="text-slate-900 hover:text-emerald-600 font-black text-[10px] uppercase tracking-widest py-2 px-4 bg-slate-100 rounded-xl transition-all">
                                        Ver Reporte
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-8 py-6 bg-slate-50/50 border-t border-slate-100">
                {{ $cajas->links() }}
            </div>
        </div>
    </div>
@endsection