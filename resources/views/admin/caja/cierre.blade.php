@extends('layouts.admin')

@section('content')
    <div class="px-6 py-8 bg-slate-50 min-h-screen">
        <div class="max-w-4xl mx-auto">

            <div class="mb-8">
                <a href="{{ route('admin.cajas.index') }}"
                    class="text-slate-400 hover:text-slate-600 font-bold flex items-center gap-2 mb-4 transition-all">
                    <i class="fas fa-arrow-left text-xs"></i>
                    Volver a cajas
                </a>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight">Cierre de Terminal</h1>
                <p class="text-slate-500 font-medium">Terminal activa: <span
                        class="text-slate-900">{{ $caja->nombre }}</span></p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- Resumen Automático (NO EDITABLE) -->
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                        <h2 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-6">Resumen del Turno</h2>

                        <div class="space-y-4">
                            <div class="flex justify-between items-center group">
                                <span class="text-slate-500 font-medium text-sm">Ventas Entradas (Cine)</span>
                                <span
                                    class="text-slate-900 font-black">${{ number_format($totales['ventas_entradas'], 0) }}</span>
                            </div>
                            <div class="flex justify-between items-center group">
                                <span class="text-slate-500 font-medium text-sm">Ventas Dulcería</span>
                                <span
                                    class="text-slate-900 font-black">${{ number_format($totales['ventas_dulceria'], 0) }}</span>
                            </div>
                            <hr class="border-slate-50">
                            <div class="flex justify-between items-center group">
                                <span class="text-slate-500 font-medium text-sm">Ventas con Tarjeta</span>
                                <span
                                    class="text-blue-600 font-black">${{ number_format($totales['ventas_tarjeta'], 0) }}</span>
                            </div>
                            <div class="flex justify-between items-center group pt-4 border-t border-slate-50">
                                <span class="text-slate-900 font-black text-base">Efectivo Esperado</span>
                                <span
                                    class="text-emerald-600 font-black text-xl">${{ number_format($totales['efectivo_esperado'], 0) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-900 p-6 rounded-3xl shadow-xl">
                        <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-1">Cajero Responsable</p>
                        <p class="text-white text-lg font-black">{{ $caja->user?->name ?? 'Usuario Desconocido' }}</p>
                        <p class="text-slate-500 text-xs font-medium">Abierta el
                            {{ $caja->fecha_apertura?->format('d/m H:i') ?? 'N/A' }}
                        </p>
                    </div>
                </div>

                <!-- Ingreso Manual (OBLIGATORIO) -->
                <div class="lg:col-span-2">
                    <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 h-full">
                        <h2 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-6 px-1">Validación de
                            Efectivo</h2>

                        <form action="{{ route('admin.cajas.cerrar', $caja->id) }}" method="POST" id="formCierre">
                            @csrf
                            <div class="space-y-8">

                                <!-- Monto Declarado -->
                                <div>
                                    <label class="block text-sm font-black text-slate-900 mb-3">Efectivo Contado en Caja
                                        (Obligatorio)</label>
                                    <div class="relative">
                                        <span
                                            class="absolute left-6 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-2xl">$</span>
                                        <input type="number" name="monto_declarado" step="0.01" required
                                            placeholder="Ingresa el monto total contado..."
                                            class="w-full bg-slate-50 border-2 border-slate-100 focus:border-emerald-500 focus:ring-0 rounded-3xl py-6 pl-12 pr-6 text-3xl font-black transition-all">
                                    </div>
                                    <p class="mt-3 text-slate-400 text-xs font-medium">Incluye el fondo inicial del cambio
                                        en tu conteo final.</p>
                                </div>

                                <!-- Tarjeta/Datáfono Declarado -->
                                <div>
                                    <label class="block text-sm font-black text-slate-900 mb-3">
                                        <i class="fas fa-credit-card text-blue-500 mr-2"></i>
                                        Total Vouchers Datáfono (Opcional)
                                    </label>
                                    <div class="relative">
                                        <span
                                            class="absolute left-6 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-2xl">$</span>
                                        <input type="number" name="tarjeta_declarada" step="0.01"
                                            placeholder="Total de vouchers contados..."
                                            value="{{ number_format($totales['ventas_tarjeta'], 0, '.', '') }}"
                                            class="w-full bg-blue-50 border-2 border-blue-100 focus:border-blue-500 focus:ring-0 rounded-3xl py-6 pl-12 pr-6 text-3xl font-black transition-all">
                                    </div>
                                    <p class="mt-3 text-blue-500 text-xs font-medium">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Sistema espera: ${{ number_format($totales['ventas_tarjeta'], 0) }}.
                                        Verifica que coincida con tus vouchers físicos.
                                    </p>
                                </div>

                                <!-- Observaciones -->
                                <div>
                                    <label class="block text-sm font-black text-slate-900 mb-3">Observaciones /
                                        Novedades</label>
                                    <textarea name="notas" rows="4"
                                        placeholder="Reporta cualquier descuadre, billetes falsos o novedades del turno..."
                                        class="w-full bg-slate-50 border-2 border-slate-100 focus:border-blue-500 focus:ring-0 rounded-3xl p-6 font-medium text-slate-700 transition-all"></textarea>
                                </div>

                                <div class="flex flex-col sm:flex-row gap-4 pt-4">
                                    <button type="submit"
                                        class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white py-5 rounded-2xl font-black shadow-lg shadow-emerald-100 transition-all flex items-center justify-center gap-3">
                                        <i class="fas fa-check-circle text-xl"></i>
                                        CONFIRMAR CIERRE
                                    </button>

                                    <a href="{{ route('admin.cajas.index') }}"
                                        class="px-8 bg-slate-100 hover:bg-slate-200 text-slate-600 py-5 rounded-2xl font-bold transition-all text-center">
                                        Cancelar
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        document.getElementById('formCierre').addEventListener('submit', function (e) {
            if (!confirm('¿Estás seguro de que deseas cerrar esta caja? Esta acción enviará los informes de auditoría y no se puede deshacer.')) {
                e.preventDefault();
            }
        });
    </script>
@endsection
