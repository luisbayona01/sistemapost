@extends('layouts.admin')

@section('content')
    <div class="px-6 py-8 bg-slate-50 min-h-screen">
        <div class="max-w-4xl mx-auto">

            @if($miCaja)
                {{-- CAJA ACTIVA --}}
                <div class="mb-8">
                    <h1 class="text-3xl font-black text-slate-900 tracking-tight flex items-center gap-3">
                        <div class="w-12 h-12 bg-emerald-500 rounded-2xl flex items-center justify-center">
                            <i class="fas fa-cash-register text-white text-lg"></i>
                        </div>
                        Mi Caja de Hoy
                    </h1>
                    <p class="text-slate-500 font-medium mt-2">
                        {{ $miCaja->nombre }} — Abierta desde las {{ $miCaja->hora_apertura }}
                    </p>
                </div>

                {{-- KPIs --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                    <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-dollar-sign text-emerald-600"></i>
                            </div>
                            <span class="text-xs font-black text-slate-400 uppercase tracking-widest">Total Vendido</span>
                        </div>
                        <span
                            class="text-3xl font-black text-emerald-600">${{ number_format((float) $miCaja->total_vendido, 0) }}</span>
                    </div>

                    <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-receipt text-blue-600"></i>
                            </div>
                            <span class="text-xs font-black text-slate-400 uppercase tracking-widest">Transacciones</span>
                        </div>
                        <span class="text-3xl font-black text-blue-600">{{ $totales['cantidad_transacciones'] ?? 0 }}</span>
                    </div>

                    <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 bg-violet-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-coins text-violet-600"></i>
                            </div>
                            <span class="text-xs font-black text-slate-400 uppercase tracking-widest">Base Inicial</span>
                        </div>
                        <span
                            class="text-3xl font-black text-violet-600">${{ number_format((float) $miCaja->monto_inicial, 0) }}</span>
                    </div>
                </div>

                {{-- Desglose Rápido --}}
                @if($totales)
                    <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm mb-8">
                        <h3 class="text-sm font-black text-slate-400 uppercase tracking-widest mb-4">Desglose en Tiempo Real</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="text-center p-4 bg-slate-50 rounded-xl">
                                <p class="text-xs font-bold text-slate-400 mb-1">Boletería</p>
                                <p class="text-lg font-black text-slate-700">
                                    ${{ number_format((float) $totales['ventas_entradas'], 0) }}</p>
                            </div>
                            <div class="text-center p-4 bg-slate-50 rounded-xl">
                                <p class="text-xs font-bold text-slate-400 mb-1">Confitería</p>
                                <p class="text-lg font-black text-slate-700">
                                    ${{ number_format((float) $totales['ventas_dulceria'], 0) }}</p>
                            </div>
                            <div class="text-center p-4 bg-emerald-50 rounded-xl">
                                <p class="text-xs font-bold text-emerald-600 mb-1">Efectivo</p>
                                <p class="text-lg font-black text-emerald-700">
                                    ${{ number_format((float) $totales['ventas_efectivo'], 0) }}</p>
                            </div>
                            <div class="text-center p-4 bg-blue-50 rounded-xl">
                                <p class="text-xs font-bold text-blue-600 mb-1">Tarjeta</p>
                                <p class="text-lg font-black text-blue-700">
                                    ${{ number_format((float) $totales['ventas_tarjeta'], 0) }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Acciones --}}
                <div class="flex flex-col md:flex-row gap-4">
                    <a href="{{ route('pos.index') }}"
                        class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white px-8 py-4 rounded-2xl font-bold text-center transition-all shadow-lg shadow-emerald-200 flex items-center justify-center gap-3">
                        <i class="fas fa-store text-lg"></i>
                        Ir al POS — Vender
                    </a>

                    <a href="{{ route('admin.cajas.mostrar-cierre-wizard', $miCaja->id) }}"
                        class="flex-1 bg-red-600 hover:bg-red-700 text-white px-8 py-4 rounded-2xl font-bold text-center transition-all shadow-lg shadow-red-200 flex items-center justify-center gap-3">
                        <i class="fas fa-lock text-lg"></i>
                        Cerrar Caja
                    </a>
                </div>

            @else
                {{-- SIN CAJA ABIERTA --}}
                <div class="text-center py-20">
                    <div class="w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-cash-register text-4xl text-slate-300"></i>
                    </div>
                    <h2 class="text-2xl font-black text-slate-700 mb-3">No hay caja abierta hoy para este usuario</h2>
                    <p class="text-slate-500 font-medium mb-8 max-w-md mx-auto">
                        Tu caja se abrirá automáticamente al ingresar al punto de venta.
                    </p>

                    <a href="{{ route('pos.index') }}"
                        class="inline-flex items-center gap-3 bg-emerald-600 hover:bg-emerald-700 text-white px-8 py-4 rounded-2xl font-bold transition-all shadow-lg shadow-emerald-200">
                        <i class="fas fa-store text-lg"></i>
                        Ir al Punto de Venta
                    </a>
                </div>
            @endif

        </div>
    </div>
@endsection
