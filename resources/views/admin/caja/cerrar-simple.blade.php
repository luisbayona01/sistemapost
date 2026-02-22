@extends('layouts.admin')

@section('content')
    <div class="container mx-auto px-4 py-4 max-w-3xl">

        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 mb-4 rounded text-sm">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">

            {{-- Header --}}
            <div class="bg-slate-900 text-white px-6 py-4 flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-black uppercase tracking-tight">🔒 Cierre de Caja</h1>
                    <p class="text-slate-400 text-xs font-bold">Caja #{{ $caja->id }} &mdash;
                        {{ $caja->user->name ?? 'N/A' }}
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-[10px] text-slate-500 uppercase font-bold">Apertura</p>
                    <p class="text-sm font-black text-slate-300">{{ $caja->fecha_apertura?->format('d/m/Y H:i') ?? 'N/A' }}
                    </p>
                </div>
            </div>

            {{-- Totales del Sistema (Solo lectura - referencia) --}}
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">📊 Totales del Sistema
                    (Referencia)</p>
                <div class="grid grid-cols-3 gap-3">
                    <div class="bg-white rounded-xl p-3 border border-slate-200 text-center">
                        <p class="text-[9px] text-slate-400 font-bold uppercase mb-1">💵 Efectivo Sist.</p>
                        <p class="text-base font-black text-slate-800">
                            ${{ number_format($totales['efectivo_esperado'], 2) }}
                        </p>
                    </div>
                    <div class="bg-white rounded-xl p-3 border border-slate-200 text-center">
                        <p class="text-[9px] text-slate-400 font-bold uppercase mb-1">💳 Tarjeta Sist.</p>
                        <p class="text-base font-black text-blue-600">${{ number_format($totales['ventas_tarjeta'], 0) }}
                        </p>
                    </div>
                    <div class="bg-emerald-600 rounded-xl p-3 text-center">
                        <p class="text-[9px] text-emerald-200 font-bold uppercase mb-1">Total Esperado</p>
                        <p class="text-base font-black text-white">
                            ${{ number_format($totales['monto_final_esperado_total'], 0) }}</p>
                    </div>
                </div>
            </div>

            {{-- Formulario de Arqueo --}}
            <form method="POST" action="{{ route('admin.caja.procesar-cierre', $caja->id) }}" id="formCierre"
                class="px-6 py-5">
                @csrf

                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-3">💰 Arqueo Físico (Efectivo +
                    Tarjeta)</p>

                <div class="grid grid-cols-1 gap-3 mb-4">

                    {{-- Efectivo --}}
                    <div class="bg-green-50 border border-green-200 rounded-xl p-3">
                        <div class="flex items-center justify-between mb-2">
                            <label class="font-black text-sm text-green-800">💵 Efectivo Contado</label>
                            <span class="text-[9px] text-green-600 font-bold bg-green-100 px-2 py-0.5 rounded-full">
                                Esperado: ${{ number_format($totales['efectivo_esperado'], 0) }}
                            </span>
                        </div>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 font-black">$</span>
                            <input type="number" name="efectivo_declarado" id="efectivo_declarado" step="100"
                                value="{{ old('efectivo_declarado', 0) }}" required min="0"
                                class="w-full border-2 border-green-200 focus:border-green-500 rounded-lg pl-7 pr-4 py-2.5 text-lg font-black text-center outline-none transition-colors"
                                placeholder="0">
                        </div>
                        <p class="text-[9px] text-green-600 mt-1 italic">Suma de billetes y monedas en el cajón.</p>
                    </div>

                    {{-- Tarjeta --}}
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-3">
                        <div class="flex items-center justify-between mb-2">
                            <label class="font-black text-sm text-blue-800">💳 Vouchers / Datáfono</label>
                            <span class="text-[9px] text-blue-600 font-bold bg-blue-100 px-2 py-0.5 rounded-full">
                                Esperado: ${{ number_format($totales['ventas_tarjeta'], 0) }}
                            </span>
                        </div>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 font-black">$</span>
                            <input type="number" name="tarjeta_declarado" id="tarjeta_declarado" step="100"
                                value="{{ old('tarjeta_declarado', 0) }}" min="0"
                                class="w-full border-2 border-blue-200 focus:border-blue-500 rounded-lg pl-7 pr-4 py-2.5 text-lg font-black text-center outline-none transition-colors"
                                placeholder="0">
                        </div>
                        <p class="text-[9px] text-blue-600 mt-1 italic">Suma de vouchers impresos por el datáfono.</p>
                    </div>

                    {{-- Otros --}}
                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-3">
                        <div class="flex items-center justify-between mb-2">
                            <label class="font-black text-sm text-amber-800">🎫 Otros (Opcional)</label>
                        </div>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 font-black">$</span>
                            <input type="number" name="otros_declarado" id="otros_declarado" step="100"
                                value="{{ old('otros_declarado', 0) }}" min="0"
                                class="w-full border-2 border-amber-200 focus:border-amber-500 rounded-lg pl-7 pr-4 py-2.5 text-lg font-black text-center outline-none transition-colors"
                                placeholder="0">
                        </div>
                    </div>
                </div>

                {{-- Feedback de cuadre en tiempo real --}}
                <div id="feedback-cuadre" class="p-3 rounded-xl font-bold text-center text-sm mb-4 hidden"></div>

                {{-- Notas --}}
                <div class="mb-4">
                    <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">📝 Notas
                        (opcional)</label>
                    <textarea name="notas" rows="2"
                        class="w-full border-2 border-slate-200 focus:border-slate-400 rounded-xl px-3 py-2 text-sm outline-none resize-none"
                        placeholder="Ej: $50,000 de cambio entregado al cliente X...">{{ old('notas') }}</textarea>
                </div>

                {{-- Botones --}}
                <div class="flex gap-3">
                    <button type="submit" id="btnConfirmar"
                        class="flex-1 bg-emerald-600 hover:bg-emerald-500 text-white py-3 rounded-xl font-black uppercase tracking-widest text-sm transition-all shadow-lg shadow-emerald-900/10 active:scale-95">
                        ✅ CONFIRMAR CIERRE
                    </button>
                    <a href="{{ route('admin.caja.index') }}"
                        class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 py-3 rounded-xl font-black uppercase tracking-widest text-sm text-center transition-all leading-relaxed">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const efectivoInput = document.getElementById('efectivo_declarado');
            const tarjetaInput = document.getElementById('tarjeta_declarado');
            const otrosInput = document.getElementById('otros_declarado');
            const feedbackDiv = document.getElementById('feedback-cuadre');
            const btnConfirmar = document.getElementById('btnConfirmar');
            const totalEsperado = {{ $totales['monto_final_esperado_total'] }};

            function validarCuadre() {
                const efectivo = parseFloat(efectivoInput.value) || 0;
                const tarjeta = parseFloat(tarjetaInput.value) || 0;
                const otros = parseFloat(otrosInput.value) || 0;
                const totalDeclarado = efectivo + tarjeta + otros;
                const diferencia = totalDeclarado - totalEsperado;

                feedbackDiv.classList.remove('hidden');

                if (Math.abs(diferencia) < 1) {
                    feedbackDiv.className = 'p-3 rounded-xl font-bold text-center text-sm bg-green-100 text-green-800 border border-green-200 mb-4';
                    feedbackDiv.innerHTML = '✅ CUADRE PERFECTO &mdash; Total declarado: $' + totalDeclarado.toLocaleString('es-CO');
                } else if (diferencia > 0) {
                    feedbackDiv.className = 'p-3 rounded-xl font-bold text-center text-sm bg-blue-100 text-blue-800 border border-blue-200 mb-4';
                    feedbackDiv.innerHTML = '💰 SOBRANTE: $' + Math.abs(diferencia).toLocaleString('es-CO') + ' &mdash; Declarado: $' + totalDeclarado.toLocaleString('es-CO');
                } else {
                    feedbackDiv.className = 'p-3 rounded-xl font-bold text-center text-sm bg-red-100 text-red-800 border border-red-200 mb-4';
                    feedbackDiv.innerHTML = '⚠️ FALTANTE: $' + Math.abs(diferencia).toLocaleString('es-CO') + ' &mdash; Declarado: $' + totalDeclarado.toLocaleString('es-CO');
                }

                btnConfirmar.disabled = false;
                btnConfirmar.classList.remove('opacity-50', 'cursor-not-allowed');
            }

            efectivoInput.addEventListener('input', validarCuadre);
            tarjetaInput.addEventListener('input', validarCuadre);
            otrosInput.addEventListener('input', validarCuadre);
        });
    </script>
@endsection