@extends('layouts.admin')

@section('content')
    <div class="px-6 py-8 bg-slate-50 min-h-screen" x-data="cierreWizard()">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <a href="{{ route('admin.cajas.index') }}"
                    class="text-slate-400 hover:text-slate-600 font-bold flex items-center gap-2 mb-4 transition-all">
                    <i class="fas fa-arrow-left text-xs"></i>
                    Volver a cajas
                </a>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight">Cierre de Caja Guiado</h1>
                <p class="text-slate-500 font-medium">Caja: <span
                        class="text-slate-900 font-bold">{{ $caja->nombre }}</span> | Cajero: <span
                        class="text-slate-900">{{ $caja->user?->name ?? 'Usuario Desconocido' }}</span></p>
            </div>

            <!-- Steps Indicator -->
            <div class="mb-8">
                <div class="flex items-center justify-between relative">
                    <div class="absolute left-0 top-1/2 -translate-y-1/2 w-full h-1 bg-slate-200 -z-10"></div>
                    <template x-for="i in 4">
                        <div class="flex flex-col items-center gap-2 bg-slate-50 px-2">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all duration-300"
                                :class="step >= i ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-500'">
                                <span x-text="i"></span>
                            </div>
                            <span class="text-xs font-bold uppercase"
                                :class="step >= i ? 'text-emerald-700' : 'text-slate-400'"
                                x-text="['Inicio', 'Conteo', 'Revisión', 'Confirmar'][i-1]"></span>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Wizard Content -->
            <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-slate-100 relative">

                <!-- Loading Overlay -->
                <div x-show="loading"
                    class="absolute inset-0 bg-white/80 z-50 flex items-center justify-center backdrop-blur-sm"
                    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                    <div class="flex flex-col items-center">
                        <i class="fas fa-circle-notch fa-spin text-4xl text-emerald-600 mb-4"></i>
                        <p class="text-slate-600 font-bold animate-pulse">Procesando cierre...</p>
                    </div>
                </div>

                <form id="wizardForm" action="{{ route('admin.cajas.cerrar', $caja->id) }}" method="POST"
                    @submit.prevent="submitForm">
                    @csrf
                    <input type="hidden" name="monto_declarado" :value="totalDeclarado">
                    <input type="hidden" name="efectivo_declarado" :value="efectivoDeclarado">
                    <input type="hidden" name="tarjeta_declarada" :value="tarjetaDeclarada">
                    <input type="hidden" name="otros_declarado" :value="otrosDeclarado">

                    <!-- Step 1: Resumen Preliminar -->
                    <div x-show="step === 1" x-transition:enter="transition ease-out duration-300 transform"
                        x-transition:enter-start="opacity-0 translate-x-12"
                        x-transition:enter-end="opacity-100 translate-x-0">
                        <div class="p-8">
                            <div class="text-center mb-8">
                                <h2 class="text-2xl font-black text-slate-800 mb-2">Resumen de Turno</h2>
                                <p class="text-slate-500">Revisa los totales esperados por el sistema.</p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                                <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200">
                                    <span
                                        class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Ventas
                                        Totales</span>
                                    <span
                                        class="text-3xl font-black text-slate-900">${{ number_format($totales['total_general'] ?? 0, 0) }}</span>
                                    <div class="mt-4 space-y-2 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-slate-500">Cine (Boletería):</span>
                                            <span
                                                class="font-bold">${{ number_format($totales['ventas_entradas'] ?? 0, 0) }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-slate-500">Confitería:</span>
                                            <span
                                                class="font-bold">${{ number_format($totales['ventas_dulceria'] ?? 0, 0) }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-emerald-50 p-6 rounded-2xl border border-emerald-100">
                                    <span
                                        class="block text-xs font-bold text-emerald-600 uppercase tracking-widest mb-2">Efectivo
                                        Esperado</span>
                                    <span
                                        class="text-3xl font-black text-emerald-700">${{ number_format($totales['efectivo_esperado'] ?? 0, 0) }}</span>
                                    <p class="text-xs text-emerald-600 mt-2">Base + Ventas Efectivo + Ingresos - Egresos</p>
                                </div>

                                <div class="bg-blue-50 p-6 rounded-2xl border border-blue-100">
                                    <span
                                        class="block text-xs font-bold text-blue-600 uppercase tracking-widest mb-2">Tarjeta
                                        Esperada</span>
                                    <span
                                        class="text-3xl font-black text-blue-700">${{ number_format($totales['ventas_tarjeta'] ?? 0, 0) }}</span>
                                    <p class="text-xs text-blue-600 mt-2">Suma de vouchers digitales</p>
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <button type="button" @click="nextStep"
                                    class="bg-slate-900 hover:bg-slate-800 text-white px-8 py-3 rounded-xl font-bold transition-all flex items-center gap-2">
                                    Continuar
                                    <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Conteo Físico -->
                    <div x-show="step === 2" x-transition:enter="transition ease-out duration-300 transform"
                        x-transition:enter-start="opacity-0 translate-x-12"
                        x-transition:enter-end="opacity-100 translate-x-0" style="display: none;">
                        <div class="p-8">
                            <div class="text-center mb-6">
                                <h2 class="text-2xl font-black text-slate-800 mb-2">Conteo de Dinero</h2>
                                <p class="text-slate-500">Ingresa la cantidad de billetes y monedas.</p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <!-- Entradas Directas Simplificadas -->
                                <div class="space-y-6">
                                    <div class="bg-emerald-50 p-6 rounded-2xl border-2 border-emerald-100">
                                        <label
                                            class="block text-sm font-black text-emerald-900 mb-2 uppercase tracking-widest">
                                            <i class="fas fa-money-bill-wave mr-1"></i> EFECTIVO REAL EN CAJA
                                        </label>
                                        <div class="relative">
                                            <span
                                                class="absolute left-4 top-1/2 -translate-y-1/2 text-emerald-400 font-black text-2xl">$</span>
                                            <input type="number" x-model.number="efectivoDeclarado" step="any"
                                                class="w-full bg-white border-2 border-emerald-200 rounded-xl py-4 pl-10 pr-4 font-black text-3xl text-emerald-700 focus:border-emerald-500 transition-all outline-none shadow-sm"
                                                placeholder="0" @focus="$event.target.select()">
                                        </div>
                                        <p class="text-[10px] text-emerald-600 font-bold uppercase mt-2">Ingresa el valor
                                            total contado en billetes y monedas</p>
                                    </div>

                                    <div class="bg-blue-50 p-6 rounded-2xl border-2 border-blue-100">
                                        <label
                                            class="block text-sm font-black text-blue-900 mb-2 uppercase tracking-widest">
                                            <i class="fas fa-credit-card mr-1"></i> TARJETAS / DATÁFONO
                                        </label>
                                        <div class="relative">
                                            <span
                                                class="absolute left-4 top-1/2 -translate-y-1/2 text-blue-400 font-black text-2xl">$</span>
                                            <input type="number" x-model.number="tarjetaDeclarada" step="any"
                                                class="w-full bg-white border-2 border-blue-200 rounded-xl py-4 pl-10 pr-4 font-black text-3xl text-blue-700 focus:border-blue-500 transition-all outline-none shadow-sm"
                                                placeholder="0" @focus="$event.target.select()">
                                        </div>
                                        <p class="text-[10px] text-blue-600 font-bold uppercase mt-2">Suma de vouchers
                                            físicos del datáfono</p>
                                    </div>

                                    <div class="bg-slate-100 p-6 rounded-2xl border-2 border-slate-200">
                                        <label
                                            class="block text-sm font-black text-slate-700 mb-2 uppercase tracking-widest">
                                            <i class="fas fa-globe mr-1"></i> OTROS (WEB / BONOS)
                                        </label>
                                        <div class="relative">
                                            <span
                                                class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-black text-2xl">$</span>
                                            <input type="number" x-model.number="otrosDeclarado" step="any"
                                                class="w-full bg-white border-2 border-slate-300 rounded-xl py-4 pl-10 pr-4 font-black text-3xl text-slate-800 focus:border-slate-500 transition-all outline-none shadow-sm"
                                                placeholder="0" @focus="$event.target.select()">
                                        </div>
                                        <p class="text-[10px] text-slate-500 font-bold uppercase mt-2">Ventas web, bonos o
                                            convenios externos</p>
                                    </div>
                                </div>

                                <!-- Resumen Lateral -->
                                <div class="bg-slate-900 p-8 rounded-3xl text-white shadow-2xl h-fit sticky top-4">
                                    <div class="mb-8 pb-6 border-b border-slate-700">
                                        <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">
                                            Total Declarado por Usuario</p>
                                        <p class="text-5xl font-black tracking-tighter"
                                            x-text="'$' + formatNumber(totalDeclarado)"></p>
                                    </div>

                                    <div class="space-y-4">
                                        <span class="text-slate-400 font-bold">Base Inicial (Efectivo)</span>
                                        <span
                                            class="font-black">${{ number_format((float) $caja->monto_inicial, 0) }}</span>
                                    </div>
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-slate-400 font-bold">Ventas Esperadas (Sistema)</span>
                                        <span
                                            class="font-black">${{ number_format($totales['total_general'] ?? 0, 0) }}</span>
                                    </div>
                                    <div class="pt-6 mt-6 border-t border-slate-700">
                                        <div class="flex justify-between items-center">
                                            <span class="text-xs font-black uppercase tracking-widest"
                                                :class="diferenciaTotal >= 0 ? 'text-emerald-400' : 'text-red-400'">Diferencia
                                                Total</span>
                                            <span class="text-2xl font-black"
                                                :class="diferenciaTotal >= 0 ? 'text-emerald-400' : 'text-red-400'"
                                                x-text="(diferenciaTotal > 0 ? '+' : '') + '$' + formatNumber(diferenciaTotal)"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-between mt-8 pt-6 border-t border-slate-100">
                            <button type="button" @click="step--"
                                class="text-slate-500 hover:text-slate-700 font-bold px-6 py-3">
                                <i class="fas fa-arrow-left mr-2"></i> Atrás
                            </button>
                            <button type="button" @click="nextStep"
                                class="bg-slate-900 hover:bg-slate-800 text-white px-8 py-3 rounded-xl font-bold transition-all flex items-center gap-2">
                                Revisar Diferencias
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>
            </div>

            <!-- Step 3: Revisión -->
            <div x-show="step === 3" x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="opacity-0 translate-x-12" x-transition:enter-end="opacity-100 translate-x-0"
                style="display: none;">
                <div class="p-8">
                    <div class="text-center mb-8">
                        <h2 class="text-2xl font-black text-slate-800 mb-2">Revisión Final</h2>
                        <p class="text-slate-500">Confirma que los números cuadren.</p>
                    </div>

                    <!-- Arqueo Consolidado -->
                    <div class="mb-8 bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-sm">
                        <div class="bg-slate-900 px-6 py-4 flex justify-between items-center text-white">
                            <h3 class="font-black uppercase tracking-widest text-sm">Arqueo Consolidado (Balance General)
                            </h3>
                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase"
                                :class="Math.abs(diferenciaTotal) < 1 ? 'bg-emerald-500' : 'bg-red-500'">
                                <span
                                    x-text="Math.abs(diferenciaTotal) < 1 ? 'Caja Cuadrada' : 'Diferencia Detectada'"></span>
                            </span>
                        </div>
                        <div class="p-8 grid grid-cols-1 md:grid-cols-3 gap-8 text-center divide-x divide-slate-100">
                            <div class="px-4">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Total
                                    Sistema</p>
                                <p class="text-3xl font-black text-slate-900">
                                    ${{ number_format($totales['monto_final_esperado_total'] ?? 0, 0) }}</p>
                                <p class="text-[9px] text-slate-400 italic mt-1">(Base + Ventas + Ingresos - Egresos)</p>
                            </div>
                            <div class="px-4">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Total
                                    Declarado</p>
                                <p class="text-3xl font-black text-slate-900" x-text="'$' + formatNumber(totalDeclarado)">
                                </p>
                                <p class="text-[9px] text-slate-400 italic mt-1">(Efectivo + Tarjetas + Otros)</p>
                            </div>
                            <div class="px-4">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Diferencia
                                    Final</p>
                                <p class="text-3xl font-black"
                                    :class="Math.abs(diferenciaTotal) < 1 ? 'text-emerald-500' : (diferenciaTotal > 0 ? 'text-blue-600' : 'text-red-600')"
                                    x-text="(diferenciaTotal > 0 ? '+' : '') + '$' + formatNumber(diferenciaTotal)">
                                </p>
                                <p class="text-[9px] font-bold uppercase mt-1"
                                    :class="Math.abs(diferenciaTotal) < 1 ? 'text-emerald-500' : (diferenciaTotal > 0 ? 'text-blue-600' : 'text-red-600')"
                                    x-text="Math.abs(diferenciaTotal) < 1 ? 'OK' : (diferenciaTotal > 0 ? 'Sobrante' : 'Faltante')">
                                </p>
                            </div>
                        </div>

                        <!-- Desglose Informativo (No activa alertas) -->
                        <div class="bg-slate-50 p-6 border-t border-slate-100">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-4">Desglose
                                Informativo</p>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-slate-500 font-medium">Efectivo (Declarado / Sistema)</span>
                                    <span class="font-bold text-slate-700"
                                        x-text="'$' + formatNumber(efectivoDeclarado) + ' / ${{ number_format($totales['efectivo_esperado'], 0) }}'"></span>
                                </div>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-slate-500 font-medium">Tarjeta (Declarado / Sistema)</span>
                                    <span class="font-bold text-slate-700"
                                        x-text="'$' + formatNumber(tarjetaDeclarada) + ' / ${{ number_format($totales['ventas_tarjeta'], 0) }}'"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Motivo (Condicional - Unificado por Total) -->
                    <div class="mb-6"
                        x-show="Math.abs(diferenciaTotal) > umbralMotivo"
                        x-transition>
                        <label class="block text-sm font-black text-red-600 mb-2">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Motivo de la Diferencia (Obligatorio)
                        </label>
                        <textarea name="motivo_diferencia" x-model="motivo" rows="3"
                            class="w-full bg-red-50 border-2 border-red-100 rounded-xl p-4 focus:border-red-500 focus:ring-0 transition-all font-medium text-slate-700"
                            placeholder="Explica por qué no cuadra el dinero (ej. error en cambio, gasto sin registrar, propina...)"></textarea>
                        <p class="text-xs text-red-500 mt-1 font-bold" x-show="motivo.length < 5">Debes escribir al
                            menos una palabra.</p>
                    </div>

                    <!-- Notas Opcionales -->
                    <div class="mb-6"
                        x-show="Math.abs(diferenciaTotal) <= umbralMotivo">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Observaciones (Opcional)</label>
                        <textarea name="notas" rows="2"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl p-4 focus:ring-0 focus:border-emerald-500"></textarea>
                    </div>

                    <div class="flex justify-between mt-8 pt-6 border-t border-slate-100">
                        <button type="button" @click="step--"
                            class="text-slate-500 hover:text-slate-700 font-bold px-6 py-3">
                            <i class="fas fa-arrow-left mr-2"></i> Atrás
                        </button>
                        <button type="button" @click="confirmCierre"
                            :disabled="Math.abs(diferenciaTotal) > umbralMotivo && motivo.length < 5"
                            class="bg-red-600 hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed text-white px-8 py-3 rounded-xl font-black transition-all shadow-lg shadow-red-100 flex items-center gap-2">
                            <i class="fas fa-lock"></i>
                            CERRAR CAJA
                        </button>
                    </div>
                </div>
            </div>

            </form>

            <!-- Modal de Confirmación Final -->
            <div x-show="showConfirmModal" x-cloak
                class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4"
                @click.self="showConfirmModal = false" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full p-8 transform"
                    x-transition:enter="transition ease-out duration-300 transform"
                    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    @click.stop>

                    <div class="text-center mb-6">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-lock text-3xl text-red-600"></i>
                        </div>
                        <h3 class="text-2xl font-black text-slate-900 mb-2">Confirmar Cierre de Caja</h3>
                        <p class="text-slate-600 text-sm">Esta acción cierra el día y genera el reporte final.</p>
                    </div>

                    <!-- Resumen Final REVISADO -->
                    <div class="bg-slate-50 rounded-2xl p-6 mb-6 space-y-4">
                        <div class="flex justify-between items-center pb-2 border-b border-slate-200">
                            <span class="text-sm font-black text-slate-800 uppercase tracking-widest">Balance Final Consolidado</span>
                        </div>
                        <div class="flex justify-between text-base">
                            <span class="text-slate-600 font-bold">Total Sistema:</span>
                            <span class="font-black text-slate-900">${{ number_format($totales['monto_final_esperado_total'] ?? 0, 0) }}</span>
                        </div>
                        <div class="flex justify-between text-base">
                            <span class="text-slate-600 font-bold">Total Declarado:</span>
                            <span class="font-black text-slate-900" x-text="'$' + formatNumber(totalDeclarado)"></span>
                        </div>
                        <div class="pt-4 border-t-2 border-dashed border-slate-200 flex justify-between items-center">
                            <span class="text-xs font-black uppercase tracking-widest" :class="Math.abs(diferenciaTotal) < 1 ? 'text-emerald-600' : 'text-slate-900'">Diferencia de Arqueo:</span>
                            <span class="text-2xl font-black"
                                :class="Math.abs(diferenciaTotal) < 1 ? 'text-emerald-600' : (diferenciaTotal > 0 ? 'text-blue-600' : 'text-red-600')"
                                x-text="(diferenciaTotal > 0 ? '+' : '') + '$' + formatNumber(diferenciaTotal)"></span>
                        </div>
                        <div class="text-center" x-show="Math.abs(diferenciaTotal) < 1">
                            <span class="bg-emerald-100 text-emerald-700 px-4 py-1 rounded-full text-[10px] font-black uppercase tracking-widest">✅ Caja Cuadrada</span>
                        </div>
                    </div>

                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 rounded">
                        <p class="text-xs text-yellow-800 font-bold">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Este cierre quedará registrado y no podrá editarse sin reapertura administrativa.
                        </p>
                    </div>

                    <div class="flex gap-3">
                        <button type="button" @click="showConfirmModal = false"
                            class="flex-1 bg-slate-200 hover:bg-slate-300 text-slate-700 px-6 py-3 rounded-xl font-bold transition-all">
                            Cancelar
                        </button>
                        <button type="button" @click="submitCierre"
                            class="flex-1 bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-xl font-bold transition-all shadow-lg">
                            <i class="fas fa-check mr-2"></i> Confirmar Cierre
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <script>
        function cierreWizard() {
            return {
                step: 1,
                loading: false,
                showConfirmModal: false,

                // Umbral configurable (inyectado desde backend)
                umbralMotivo: {{ config('caja.umbral_diferencia_motivo', 3000) }},

                // Datos del sistema (inyectados desde Blade)
                sistemaEfectivo: {{ $totales['efectivo_esperado'] ?? 0 }},
                sistemaTarjeta: {{ $totales['ventas_tarjeta'] ?? 0 }},

                // Datos del usuario (Inputs)
                efectivoDeclarado: {{ $totales['ventas_efectivo'] ?? 0 }},
                tarjetaDeclarada: {{ $totales['ventas_tarjeta'] ?? 0 }},
                otrosDeclarado: 0,
                motivo: '',

                // Computados
                get totalDeclarado() {
                    return Number(this.efectivoDeclarado) + Number(this.tarjetaDeclarada) + Number(this.otrosDeclarado);
                },

                get diferenciaTotal() {
                    return this.totalDeclarado - (this.sistemaEfectivo + this.sistemaTarjeta);
                },

                get diferenciaEfectivo() {
                    return this.efectivoDeclarado - this.sistemaEfectivo;
                },

                get diferenciaTarjeta() {
                    return this.tarjetaDeclarada - this.sistemaTarjeta;
                },

                // Métodos
                nextStep() {
                    if (this.step < 3) {
                        this.step++;
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                },

                formatNumber(num) {
                    return new Intl.NumberFormat('es-CO').format(num);
                },

                confirmCierre() {
                    // Validación de motivo obligatorio (POR TOTAL CONSOLIDADO)
                    if (Math.abs(this.diferenciaTotal) > this.umbralMotivo && this.motivo.length < 5) {
                        alert('Debes ingresar un motivo para la diferencia total detectada.');
                        return;
                    }

                    // Mostrar modal en lugar de confirm() nativo
                    this.showConfirmModal = true;
                },

                submitCierre() {
                    this.showConfirmModal = false;
                    this.loading = true;
                    document.getElementById('wizardForm').submit();
                },

                submitForm() {
                    return true;
                }
            }
        }
    </script>
@endsection