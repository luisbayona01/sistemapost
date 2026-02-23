@extends('layouts.app')

@section('title', 'Control Ejecutivo')

@section('content')
    <div class="min-h-screen bg-slate-50 pb-20">
        <!-- Header -->
        <div class="bg-slate-900 text-white px-6 pt-10 pb-16 rounded-b-[3rem] shadow-xl">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold">Control Ejecutivo</h1>
                    <p class="text-slate-400 text-sm">Resumen Operativo</p>
                </div>
                <div class="bg-slate-800 p-3 rounded-2xl border border-slate-700">
                    <i class="fas fa-chart-line text-emerald-400 text-xl"></i>
                </div>
            </div>

            <div class="flex items-center gap-3 overflow-x-auto pb-2 no-scrollbar">
                <div
                    class="bg-emerald-500/10 border border-emerald-500/20 px-4 py-2 rounded-xl flex items-center gap-2 whitespace-nowrap">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-xs font-bold text-emerald-400 uppercase tracking-wider">Día Operativo:
                        {{ $stats['fecha_operativa'] }}</span>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="px-6 -mt-8 space-y-4">
            <!-- Main Total Card -->
            <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-slate-100 relative overflow-hidden">
                <div class="absolute top-0 right-0 p-6 opacity-10">
                    <i class="fas fa-wallet text-6xl"></i>
                </div>
                <p class="text-slate-500 text-sm font-semibold mb-1 uppercase tracking-wider">Total Recaudado</p>
                <h2 class="text-4xl font-black text-slate-900 leading-none">
                    ${{ number_format($stats['total_ventas'], 0, ',', '.') }}
                </h2>
                <div class="mt-4 flex items-center gap-2 text-emerald-600">
                    <i class="fas fa-shopping-cart text-xs"></i>
                    <span class="text-xs font-bold uppercase tracking-tighter">{{ $stats['conteo_ventas'] }} Ventas
                        operadas</span>
                </div>
            </div>

            <!-- Secondary Grid -->
            <div class="grid grid-cols-2 gap-4">
                <!-- Efectivo -->
                <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-slate-100">
                    <div
                        class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center mb-4 border border-emerald-100">
                        <i class="fas fa-money-bill-wave text-emerald-600"></i>
                    </div>
                    <p class="text-slate-500 text-[10px] font-bold uppercase tracking-widest mb-1">Efectivo</p>
                    <h3 class="text-xl font-bold text-slate-900">
                        ${{ number_format($stats['total_efectivo'], 0, ',', '.') }}
                    </h3>
                </div>

                <!-- Tarjetas -->
                <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-slate-100">
                    <div
                        class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center mb-4 border border-blue-100">
                        <i class="fas fa-credit-card text-blue-600"></i>
                    </div>
                    <p class="text-slate-500 text-[10px] font-bold uppercase tracking-widest mb-1">Tarjetas</p>
                    <h3 class="text-xl font-bold text-slate-900">
                        ${{ number_format($stats['total_tarjetas'], 0, ',', '.') }}
                    </h3>
                </div>
            </div>

            <!-- Impuestos & Others -->
            <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-slate-100">
                <h4 class="text-slate-900 font-bold mb-4 flex items-center gap-2">
                    <i class="fas fa-file-invoice text-slate-400"></i>
                    Detalle Fiscal (INC)
                </h4>

                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-500">Impuesto al Consumo (8%)</span>
                        <span
                            class="text-sm font-black text-slate-900">${{ number_format($stats['inc_recaudado'], 0, ',', '.') }}</span>
                    </div>
                    <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                        <div class="bg-slate-900 h-full w-full opacity-20"></div>
                    </div>
                </div>
            </div>

            <!-- Canal Distribution -->
            <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-slate-100">
                <h4 class="text-slate-900 font-bold mb-4 flex items-center gap-2">
                    <i class="fas fa-layer-group text-slate-400"></i>
                    Distribución por Canal
                </h4>

                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                            <span class="text-sm text-slate-600">Confitería</span>
                        </div>
                        <span
                            class="text-sm font-bold text-slate-900">${{ number_format($stats['por_canal']['confiteria'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                            <span class="text-sm text-slate-600">Cine (Taquilla)</span>
                        </div>
                        <span
                            class="text-sm font-bold text-slate-900">${{ number_format($stats['por_canal']['cine'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            <span class="text-sm text-slate-600">Ventas Mixtas</span>
                        </div>
                        <span
                            class="text-sm font-bold text-slate-900">${{ number_format($stats['por_canal']['mixta'], 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Breakdown por Película (FIX 5) -->
            @if(isset($breakdownPeliculas) && $breakdownPeliculas->count() > 0)
                <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-slate-100">
                    <h4 class="text-slate-900 font-bold mb-4 flex items-center gap-2">
                        <i class="fas fa-ticket-alt text-slate-400"></i>
                        Boletos por Película / Función
                    </h4>
                    <div class="space-y-3">
                        @foreach($breakdownPeliculas as $peli)
                            @php
                                $ocup = $peli->capacidad_sala > 0
                                    ? round(($peli->boletos_vendidos / $peli->capacidad_sala) * 100)
                                    : 0;
                                $color = $ocup >= 80 ? 'bg-emerald-500' : ($ocup >= 50 ? 'bg-amber-400' : 'bg-slate-300');
                            @endphp
                            <div class="border border-slate-100 rounded-2xl p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <p class="font-black text-slate-900 text-xs uppercase leading-tight">{{ $peli->titulo }}</p>
                                        <p class="text-[9px] text-slate-400 font-bold uppercase mt-0.5">
                                            {{ $peli->sala }} · {{ \Carbon\Carbon::parse($peli->fecha_hora)->format('H:i') }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xs font-black text-slate-900">
                                            ${{ number_format($peli->total_recaudado ?? 0, 0, ',', '.') }}</p>
                                        <p class="text-[9px] font-bold text-emerald-600">{{ $peli->boletos_vendidos }} boletos</p>
                                    </div>
                                </div>
                                <!-- Barra de ocupación -->
                                <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                                    <div class="{{ $color }} h-full rounded-full transition-all" style="width: {{ $ocup }}%"></div>
                                </div>
                                <p class="text-[8px] text-slate-400 mt-1 text-right font-bold">{{ $ocup }}% ocupación</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Date Filter -->
            <form action="{{ route('executive.dashboard') }}" method="GET" class="pt-4">
                <div class="bg-slate-900 p-2 rounded-3xl flex gap-1">
                    <input type="date" name="fecha" value="{{ $fechaFiltro }}"
                        class="bg-transparent text-white border-none focus:ring-0 text-sm flex-1 ml-4 py-3">
                    <button type="submit" class="bg-emerald-500 text-white px-6 py-3 rounded-2xl font-bold text-sm">
                        Consultar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
@endsection