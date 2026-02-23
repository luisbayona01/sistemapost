@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto bg-white rounded-[2rem] shadow-xl overflow-hidden border border-slate-100">
            <div class="bg-slate-900 p-8 text-white">
                <h1 class="text-2xl font-black uppercase tracking-tighter">Ajuste Manual de Inventario</h1>
                <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mt-1">Impacto directo en stock y
                    rentabilidad</p>
            </div>

            <form action="{{ route('inventario-avanzado.ajuste.store') }}" method="POST" class="p-8 space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Insumo o Producto -->
                    <div class="col-span-2">
                        <label class="block text-[10px] font-black text-slate-500 uppercase mb-2">Seleccionar Ítem</label>
                        <select name="insumo_id"
                            class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl p-4 text-sm font-bold focus:border-emerald-500 outline-none transition-all">
                            <option value="">-- Seleccionar Insumo --</option>
                            @foreach($insumos as $insumo)
                                <option value="{{ $insumo->id }}">{{ $insumo->nombre }} (Actual: {{ $insumo->stock_actual }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tipo de Movimiento -->
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase mb-2">Tipo de Ajuste</label>
                        <select name="tipo" required
                            class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl p-4 text-sm font-bold focus:border-emerald-500 outline-none transition-all">
                            <option value="INCREMENTO">➕ Incremento (Suma al stock)</option>
                            <option value="DECREMENTO">➖ Decremento (Resta al stock)</option>
                        </select>
                    </div>

                    <!-- Cantidad -->
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase mb-2">Cantidad</label>
                        <input type="number" name="cantidad" step="0.001" required placeholder="0.00"
                            class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl p-4 text-sm font-bold focus:border-emerald-500 outline-none transition-all">
                    </div>

                    <!-- Motivo -->
                    <div class="col-span-2">
                        <label class="block text-[10px] font-black text-slate-500 uppercase mb-2">Motivo Obligatorio</label>
                        <select name="motivo" required
                            class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl p-4 text-sm font-bold focus:border-emerald-500 outline-none transition-all">
                            @foreach($motivos as $motivo)
                                <option value="{{ $motivo }}">{{ strtoupper($motivo) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Observaciones -->
                    <div class="col-span-2">
                        <label class="block text-[10px] font-black text-slate-500 uppercase mb-2">Observaciones /
                            Detalles</label>
                        <textarea name="observaciones" rows="3" placeholder="Explique por qué se realiza este ajuste..."
                            class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl p-4 text-sm font-bold focus:border-emerald-500 outline-none transition-all"></textarea>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit"
                        class="w-full bg-emerald-600 hover:bg-emerald-500 text-white py-5 rounded-2xl font-black uppercase tracking-widest transition-all shadow-lg shadow-emerald-900/20">
                        Registrar Ajuste Técnico
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection