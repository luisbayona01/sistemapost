@extends('layouts.app')

@section('title', 'Registrar Baja')

@section('content')
    <div class="max-w-4xl mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-slate-800 mb-2">Registrar Baja de Inventario</h1>
        <p class="text-slate-500 mb-8">Documenta salidas especiales, desperdicios o cortesías.</p>

        <div class="bg-white rounded-xl shadow-lg border border-slate-200 overflow-hidden">
            <div class="bg-slate-50 px-6 py-4 border-b border-slate-200">
                <h2 class="font-bold text-slate-700">Formulario de Baja</h2>
            </div>

            <form action="{{ route('inventario-avanzado.baja.store') }}" method="POST" class="p-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Insumo -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Insumo / Producto</label>
                        <select name="insumo_id"
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-red-500 outline-none"
                            required>
                            <option value="">Seleccione...</option>
                            @foreach($insumos as $insumo)
                                <option value="{{$insumo->id}}">{{$insumo->nombre}} (Stock: {{$insumo->stock_actual}}
                                    {{$insumo->unidad_medida}})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tipo -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Tipo de Salida</label>
                        <select name="tipo"
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-red-500 outline-none"
                            required>
                            <option value="BAJA">Baja (Daño/Pérdida/Vencimiento)</option>
                            <option value="MERMA">Merma Operativa</option>
                            <option value="CORTESIA">Cortesía / Marketing</option>
                        </select>
                    </div>

                    <!-- Cantidad -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Cantidad a descontar</label>
                        <input type="number" step="0.01" name="cantidad"
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-red-500 outline-none"
                            required placeholder="0.00">
                    </div>
                </div>

                <!-- Motivo -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Motivo (Obligatorio)</label>
                    <textarea name="motivo" rows="3"
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-red-500 outline-none"
                        required placeholder="Describa el detalle de la baja..."></textarea>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('inventario.index') }}"
                        class="px-6 py-2 bg-slate-100 text-slate-600 rounded-lg font-bold hover:bg-slate-200 transition-colors">Cancelar</a>
                    <button type="submit"
                        class="px-6 py-2 bg-red-600 text-white rounded-lg font-bold hover:bg-red-700 transition-colors shadow-lg shadow-red-200">
                        <i class="fas fa-trash-alt mr-2"></i> Procesar Baja
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
