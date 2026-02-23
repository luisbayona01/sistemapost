@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8 max-w-4xl">
        <div class="bg-white rounded-[2.5rem] shadow-2xl border border-slate-100 overflow-hidden">
            <!-- Header -->
            <div class="bg-slate-900 p-10 text-center relative overflow-hidden">
                <div class="absolute top-0 right-0 p-8 opacity-10">
                    <i class="fas fa-cash-register text-8xl text-white"></i>
                </div>
                <h1 class="text-4xl font-black text-white mb-2 uppercase tracking-tighter">Reporte de Cierre de Caja</h1>
                <p class="text-slate-400 font-bold uppercase tracking-widest text-xs">Terminal de Venta #{{ $caja->id }}</p>
            </div>

            <div class="p-10">
                <!-- Info Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
                    <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Información de
                            Apertura</p>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-bold text-slate-600">Fecha:</span>
                            <span
                                class="text-sm font-black text-slate-900">{{ $caja->fecha_apertura?->format('d/m/Y H:i') ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-bold text-slate-600">Cajero:</span>
                            <span class="text-sm font-black text-indigo-600 uppercase">{{ $caja->user->name }}</span>
                        </div>
                    </div>
                    <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Información de
                            Cierre</p>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-bold text-slate-600">Fecha:</span>
                            <span
                                class="text-sm font-black text-slate-900">{{ $caja->fecha_cierre?->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-bold text-slate-600">Estado:</span>
                            <span
                                class="bg-slate-900 text-white px-3 py-1 rounded-lg text-[10px] font-black uppercase">CERRADA</span>
                        </div>
                    </div>
                </div>

                <!-- Bloque 1: Resumen de Ventas por Canal -->
                <div class="mb-10">
                    <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] mb-6 flex items-center gap-2">
                        <i class="fas fa-chart-line text-slate-300"></i> Consolidado por Canal
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach(['boleteria' => 'Boletería', 'confiteria' => 'Confitería', 'mixta' => 'Mixta'] as $key => $label)
                            <div class="bg-slate-50 p-6 rounded-[2rem] border border-slate-100">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">{{ $label }}</p>
                                <p class="text-2xl font-black text-slate-900">
                                    ${{ number_format($resumenVentas[$key]['total'] ?? 0, 0) }}</p>
                                <div class="mt-4 space-y-1">
                                    @if(isset($resumenVentas[$key]['metodos']))
                                        @foreach($resumenVentas[$key]['metodos'] as $metodo => $subtotal)
                                            <div class="flex justify-between text-[10px] uppercase font-bold text-slate-500">
                                                <span>{{ $metodo }}:</span>
                                                <span>${{ number_format($subtotal, 0) }}</span>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Bloque 2: Devoluciones del Día (CRÍTICO) -->
                <div class="mb-10 bg-rose-50 border border-rose-100 rounded-[2.5rem] p-8">
                    <h3 class="text-xs font-black text-rose-400 uppercase tracking-[0.2em] mb-6 flex items-center gap-2">
                        <i class="fas fa-undo"></i> Devoluciones del Día
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                        <div class="bg-white/50 p-4 rounded-3xl">
                            <span class="block text-[9px] font-black text-rose-400 uppercase">Cine</span>
                            <span
                                class="text-lg font-black text-rose-600">-${{ number_format($resumenDevoluciones['boleteria'], 0) }}</span>
                        </div>
                        <div class="bg-white/50 p-4 rounded-3xl">
                            <span class="block text-[9px] font-black text-rose-400 uppercase">Dulcería</span>
                            <span
                                class="text-lg font-black text-rose-600">-${{ number_format($resumenDevoluciones['confiteria'], 0) }}</span>
                        </div>
                        <div class="bg-white/50 p-4 rounded-3xl">
                            <span class="block text-[9px] font-black text-rose-400 uppercase">Mixta</span>
                            <span
                                class="text-lg font-black text-rose-600">-${{ number_format($resumenDevoluciones['mixta'], 0) }}</span>
                        </div>
                        <div class="bg-slate-900 p-4 rounded-3xl">
                            <span class="block text-[9px] font-black text-slate-400 uppercase">Total Devuelto</span>
                            <span
                                class="text-lg font-black text-white">${{ number_format($resumenDevoluciones['total'], 0) }}</span>
                        </div>
                    </div>
                    <p class="text-[10px] italic text-rose-500 font-bold">Nota: Estos montos han sido restados de las ventas
                        brutas y reintegrados a inventario (si aplica).</p>
                </div>

                <!-- Bloque 3: Diferencia y Cuadre -->
                <div class="space-y-4 mb-10">
                    <div class="flex justify-between items-center p-6 rounded-3xl bg-slate-50 border border-slate-100">
                        <div class="flex flex-col">
                            <span class="text-sm font-black text-slate-900 uppercase">Total Bruto del Día:</span>
                            <span class="text-[9px] text-slate-400 font-bold uppercase">(Ventas Brutas -
                                Devoluciones)</span>
                        </div>
                        <span
                            class="text-2xl font-black text-slate-900">${{ number_format($resumenVentas['total_bruto'] - $resumenDevoluciones['total'], 0) }}</span>
                    </div>

                    <div
                        class="p-10 rounded-[2.5rem] text-center mb-6
                                    {{ $caja->diferencia == 0 ? 'bg-emerald-50 border-2 border-emerald-500 text-emerald-700 shadow-lg shadow-emerald-500/10' : '' }}
                                    {{ $caja->diferencia > 0 ? 'bg-blue-50 border-2 border-blue-500 text-blue-700 shadow-lg shadow-blue-500/10' : '' }}
                                    {{ $caja->diferencia < 0 ? 'bg-rose-50 border-2 border-rose-500 text-rose-700 shadow-lg shadow-rose-500/10' : '' }}">
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] mb-2">Resumen de Caja (Efectivo)</p>
                        <div class="flex items-center justify-center gap-6 mb-4">
                            <div class="text-left">
                                <span class="block text-[9px] font-bold uppercase opacity-60">Esperado</span>
                                <span class="text-2xl font-black">${{ number_format($caja->monto_esperado, 0) }}</span>
                            </div>
                            <div class="h-10 w-px bg-current opacity-20"></div>
                            <div class="text-left">
                                <span class="block text-[9px] font-bold uppercase opacity-60">Contado</span>
                                <span class="text-2xl font-black">${{ number_format($caja->saldo_final, 0) }}</span>
                            </div>
                        </div>
                        <p class="text-5xl font-black mb-2">${{ number_format(abs($caja->diferencia), 0) }}</p>
                        <div class="flex items-center justify-center gap-2 font-black uppercase tracking-widest text-sm">
                            @if($caja->diferencia == 0)
                                <i class="fas fa-check-circle"></i> Caja Cuadrada
                            @elseif($caja->diferencia > 0)
                                <i class="fas fa-plus-circle"></i> Sobrante en Caja
                            @else
                                <i class="fas fa-minus-circle"></i> Faltante en Caja
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-wrap gap-4">
                    <button onclick="window.print()"
                        class="flex-1 min-w-[200px] bg-slate-900 hover:bg-black text-white py-6 rounded-3xl font-black uppercase tracking-widest transition-all transform hover:-translate-y-1 shadow-2xl shadow-slate-900/20 flex items-center justify-center gap-3">
                        <i class="fas fa-print"></i> Imprimir Reporte Diario
                    </button>
                    <a href="{{ route('pos.index') }}"
                        class="flex-1 min-w-[200px] bg-slate-50 hover:bg-slate-100 text-slate-600 py-6 rounded-3xl font-black uppercase tracking-widest transition-all text-center">
                        Continuar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {

            .layouts-admin-navbar,
            .layouts-admin-sidebar,
            button,
            a {
                display: none !important;
            }

            body {
                background: white !important;
            }

            .container {
                max-width: 100% !important;
                padding: 0 !important;
            }

            .shadow-2xl {
                box-shadow: none !important;
            }

            .border {
                border: 1px solid #eee !important;
            }
        }
    </style>
@endsection
