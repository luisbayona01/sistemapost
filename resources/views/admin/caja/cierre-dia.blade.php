@extends('layouts.admin')

@section('content')
    <div class="px-6 py-8 bg-slate-50 min-h-screen">
        <div class="max-w-4xl mx-auto">

            <div class="mb-10 text-center">
                <a href="{{ route('admin.cajas.index') }}"
                    class="text-slate-400 hover:text-slate-600 font-bold flex items-center gap-2 mb-6 transition-all justify-center">
                    <i class="fas fa-arrow-left text-xs"></i>
                    Volver a cajas
                </a>
                <div
                    class="w-20 h-20 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner">
                    <i class="fas fa-check-double text-3xl"></i>
                </div>
                <h1 class="text-4xl font-black text-slate-900 tracking-tight mb-2">Consolidado del Día</h1>
                <p class="text-slate-500 font-bold uppercase tracking-widest text-sm italic">{{ now()->format('d/m/Y') }}
                </p>
            </div>

            {{-- ⚠️ CAJAS ABIERTAS EN CURSO — Panel unificado --}}
            @if($cajasAbiertas > 0)
                <div class="bg-amber-50 border-2 border-amber-200 rounded-3xl p-6 mb-8">
                    <div class="flex items-center gap-4 mb-4">
                        <div
                            class="w-12 h-12 bg-amber-100 text-amber-600 rounded-2xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-xl"></i>
                        </div>
                        <div>
                            <p class="text-amber-900 font-black uppercase text-sm">{{ $cajasAbiertas }} Caja(s) Sin Cerrar</p>
                            <p class="text-amber-700 text-xs">Debes cerrarlas para completar el arqueo del día. El consolidado
                                solo incluye cajas cerradas.</p>
                        </div>
                    </div>

                    {{-- Lista de cajas abiertas con acción directa --}}
                    @if(isset($cajasAbiertasData) && $cajasAbiertasData->count() > 0)
                        <div class="space-y-2">
                            @foreach($cajasAbiertasData as $cajaAb)
                                <div class="bg-white rounded-2xl px-4 py-3 flex items-center justify-between border border-amber-100">
                                    <div>
                                        <p class="font-black text-slate-800 text-sm">{{ $cajaAb->nombre ?? 'Caja #' . $cajaAb->id }}</p>
                                        <p class="text-[10px] text-slate-400 uppercase font-bold">
                                            {{ optional($cajaAb->user)->name }} —
                                            Abierta: {{ $cajaAb->fecha_apertura->format('H:i') }} —
                                            Ventas: ${{ number_format($cajaAb->ventas_sum_total ?? 0, 2) }}
                                        </p>
                                    </div>
                                    <a href="{{ route('admin.cajas.mostrar-cierre-wizard', $cajaAb->id) }}"
                                        class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all whitespace-nowrap">
                                        <i class="fas fa-lock mr-1"></i> Cerrar
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif


            @if(($consolidado['cajas_cerradas_count'] ?? 0) > 0 || ($consolidado['total_general'] ?? 0) > 0)

                    <!-- Cards de Totales -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100">
                            <p class="text-slate-400 text-xs font-black uppercase tracking-widest mb-4">Total Entradas (Cine)</p>
                            <p class="text-4xl font-black text-slate-900">
                                ${{ number_format($consolidado['total_entradas'] ?? 0, 2) }}</p>
                        </div>
                        <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 relative overflow-hidden">
                            <p class="text-slate-400 text-xs font-black uppercase tracking-widest mb-4">Total Dulcería (Bruto)</p>
                            <p class="text-4xl font-black text-orange-600 mb-2">
                                ${{ number_format($consolidado['total_dulceria'] ?? 0, 2) }}</p>
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">
                                Neto: ${{ number_format($consolidado['total_neto_dulceria'] ?? 0, 2) }} |
                                INC (8%): ${{ number_format($consolidado['total_inc'] ?? 0, 2) }}
                            </div>
                        </div>
                    </div>

                    <!-- Desglose por Método de Pago -->
                    <div class="bg-slate-900 rounded-[2.5rem] p-10 text-white shadow-2xl relative overflow-hidden">
                        <div class="relative z-10">
                            <h2 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-8">Arqueo Consolidado
                                (Cajas Cerradas)</h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                                <!-- Efectivo -->
                                <div class="bg-slate-800 rounded-2xl p-6">
                                    <p class="text-slate-500 text-xs font-bold uppercase mb-4 flex items-center gap-2">
                                        <i class="fas fa-money-bill-wave"></i>
                                        Efectivo
                                    </p>
                                    <p class="text-3xl font-black text-white mb-2">
                                        ${{ number_format($consolidado['total_efectivo'] ?? 0, 2) }}</p>
                                    <div class="flex items-center gap-2 text-sm">
                                        <span class="text-slate-400">Diferencia:</span>
                                        <span
                                            class="{{ ($consolidado['diferencia_efectivo'] ?? 0) < 0 ? 'text-rose-400' : 'text-emerald-400' }} font-black">
                                            {{ ($consolidado['diferencia_efectivo'] ?? 0) >= 0 ? '+' : '' }}${{ number_format($consolidado['diferencia_efectivo'] ?? 0, 2) }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Tarjeta -->
                                <div class="bg-slate-800 rounded-2xl p-6">
                                    <p class="text-slate-500 text-xs font-bold uppercase mb-4 flex items-center gap-2">
                                        <i class="fas fa-credit-card"></i>
                                        Tarjeta/Datáfono
                                    </p>
                                    <p class="text-3xl font-black text-blue-400 mb-2">
                                        ${{ number_format($consolidado['total_tarjeta'] ?? 0, 2) }}</p>
                                    <div class="flex items-center gap-2 text-sm">
                                        <span class="text-slate-400">Diferencia:</span>
                                        <span
                                            class="{{ ($consolidado['diferencia_tarjeta'] ?? 0) < 0 ? 'text-rose-400' : 'text-emerald-400' }} font-black">
                                            {{ ($consolidado['diferencia_tarjeta'] ?? 0) >= 0 ? '+' : '' }}${{ number_format($consolidado['diferencia_tarjeta'] ?? 0, 2) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="mt-12 pt-8 border-t border-slate-800 flex flex-col md:flex-row justify-between items-center gap-6">
                                <div>
                                    <p class="text-slate-500 text-xs font-bold uppercase mb-1">Total Gran Recaudado</p>
                                    <p class="text-5xl font-black text-emerald-400 tracking-tighter">
                                        ${{ number_format($consolidado['total_general'] ?? 0, 2) }}</p>
                                </div>

                                <div class="flex gap-4">
                                    <button onclick="window.print()"
                                        class="bg-slate-800 hover:bg-slate-700 text-white px-8 py-4 rounded-2xl font-black transition-all border border-slate-700">
                                        <i class="fas fa-print mr-2"></i> IMPRIMIR
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Decoración -->
                        <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-emerald-500/10 rounded-full blur-3xl"></div>
                    </div>

                    <!-- DETALLE DE TRANSACCIONES (Auditabilidad) -->
                    <div class="mt-12 bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                        <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                            <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest">Detalle de Ventas</h3>
                            <span
                                class="bg-slate-100 text-slate-600 text-[10px] font-black px-3 py-1 rounded-full">{{ $ventasHoy->count() }}
                                Transacciones</span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 border-b border-slate-100">
                                        <th class="p-4 text-[10px] font-black text-slate-400 uppercase">Hora</th>
                                        <th class="p-4 text-[10px] font-black text-slate-400 uppercase">Cliente</th>
                                        <th class="p-4 text-[10px] font-black text-slate-400 uppercase">Detalle</th>
                                        <th class="p-4 text-[10px] font-black text-slate-400 uppercase text-right">Total</th>
                                        <th class="p-4"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ventasHoy as $v)
                                        <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                                            <td class="p-4 text-xs font-bold text-slate-600">{{ $v->fecha_hora->format('H:i') }}</td>
                                            <td class="p-4 text-xs">
                                                <p class="font-black text-slate-900">
                                                    {{ $v->cliente->persona->razon_social ?? 'Público General' }}
                                                </p>
                                                <p class="text-[9px] text-slate-400 font-bold uppercase">{{ $v->metodo_pago }}</p>
                                            </td>
                                            <td class="p-4 text-[10px]">
                                                @if($v->subtotal_confiteria > 0)
                                                    <span
                                                        class="inline-block bg-orange-100 text-orange-700 px-2 py-0.5 rounded-md font-bold mr-1">DULCERÍA</span>
                                                @endif
                                                @if($v->asientosCinema->count() > 0)
                                                    <span
                                                        class="inline-block bg-blue-100 text-blue-700 px-2 py-0.5 rounded-md font-bold">CINE
                                                        ({{ $v->asientosCinema->count() }})</span>
                                                @endif
                                            </td>
                                            <td class="p-4 text-sm font-black text-slate-900 text-right">
                                                ${{ number_format($v->total, 2) }}</td>
                                            <td class="p-4 text-right">
                                                <a href="{{ route('ventas.show', $v->id) }}"
                                                    class="text-slate-400 hover:text-emerald-500 transition-colors">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Footer Informativo -->
                    <div class="text-center pt-8">
                        <p class="text-slate-400 text-xs font-medium italic">
                            Este reporte consolida las ventas de todas las cajas <strong>cerradas</strong> durante la
                            jornada.<br>
                            Generado automáticamente por el Sistema de Auditoría el {{ date('d/m/Y H:i:s') }}
                        </p>
                    </div>
                </div>
            @else
            {{-- SIN CAJAS CERRADAS --}}
            <div class="text-center py-16 bg-white rounded-3xl border border-slate-100 shadow-sm">
                <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-inbox text-4xl text-slate-300"></i>
                </div>
                <h2 class="text-2xl font-black text-slate-700 mb-3">No hay cajas cerradas hoy</h2>
                <p class="text-slate-500 font-medium max-w-md mx-auto">
                    El consolidado del día se genera automáticamente cuando los cajeros cierran sus cajas.
                    @if($cajasAbiertas > 0)
                        <br><br>
                        <span class="text-amber-600 font-bold">Hay {{ $cajasAbiertas }} caja(s) aún operando.</span>
                    @endif
                </p>
            </div>
        @endif
    </div>
    </div>

    <style>
        @media print {

            aside,
            nav,
            header,
            .bg-slate-50 {
                background: white !important;
            }

            .shadow-sm,
            .shadow-xl,
            .shadow-2xl {
                box-shadow: none !important;
                border: 1px solid #eee !important;
            }

            .bg-slate-900 {
                background: #000 !important;
                color: white !important;
            }

            button,
            a {
                display: none !important;
            }

            .bg-amber-50 {
                display: none !important;
            }
        }
    </style>
@endsection