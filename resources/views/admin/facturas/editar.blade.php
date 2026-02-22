@extends('layouts.admin')
@section('content')
    <div class="container mx-auto px-4 py-6 max-w-3xl">
        <div class="bg-white rounded-2xl shadow-2xl p-8 border border-gray-100">

            <div class="flex justify-between items-center mb-8 border-b pb-6">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-900">✏️ Editar Factura
                        #{{ $factura->numero_factura ?? $factura->id }}</h1>
                    <p class="text-amber-600 font-bold mt-1 uppercase text-xs tracking-widest">⚠️ Editar esta factura
                        ajustará el inventario actual.</p>
                </div>
                <a href="{{ route('admin.facturas.index') }}"
                    class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-4 py-2 rounded-xl transition font-bold">
                    ← Volver
                </a>
            </div>

            <form method="POST" action="{{ route('admin.facturas.actualizar', $factura->id) }}" id="formFactura">
                @csrf
                @method('PUT')

                <!-- A. DATOS BÁSICOS -->
                <div class="space-y-6 mb-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label
                                class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Proveedor</label>
                            <select name="proveedor_id"
                                class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:border-blue-500 focus:ring-0 transition"
                                required>
                                @foreach($proveedores as $prov)
                                    <option value="{{ $prov->id }}" {{ $factura->proveedor_id == $prov->id ? 'selected' : '' }}>
                                        {{ $prov->persona->razon_social }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Número de
                                Factura</label>
                            <input type="text" name="numero_factura" value="{{ $factura->numero_factura }}"
                                class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:border-blue-500 focus:ring-0 transition">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Fecha de
                                Factura</label>
                            <input type="date" name="fecha_compra" value="{{ $factura->fecha_compra->format('Y-m-d') }}"
                                class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:border-blue-500 focus:ring-0 transition"
                                required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Producto que
                                Ingresa</label>
                            <select name="producto_id"
                                class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:border-blue-500 focus:ring-0 transition"
                                required>
                                @foreach($productos as $prod)
                                    <option value="{{ $prod->id }}" {{ ($factura->movimiento->producto_id ?? 0) == $prod->id ? 'selected' : '' }}>
                                        {{ $prod->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-4">
                        <div class="bg-blue-50 p-6 rounded-2xl border-2 border-blue-100">
                            <label class="block text-blue-800 font-extrabold mb-2 uppercase text-sm tracking-wider">Total
                                Pagado (Moneda)</label>
                            <div class="relative">
                                <span
                                    class="absolute left-4 top-1/2 -translate-y-1/2 text-2xl font-bold text-blue-400">$</span>
                                <input type="number" name="total_pagado" id="total_pagado"
                                    value="{{ intval($factura->total_pagado) }}"
                                    class="w-full border-none bg-transparent pl-10 text-4xl font-black text-blue-900 focus:ring-0"
                                    step="1" required oninput="recalcular()">
                            </div>
                        </div>
                        <div class="bg-indigo-50 p-6 rounded-2xl border-2 border-indigo-100">
                            <label
                                class="block text-indigo-800 font-extrabold mb-2 uppercase text-sm tracking-wider">Cantidad
                                Unidades</label>
                            <input type="number" name="cantidad" id="cantidad"
                                value="{{ intval($factura->movimiento->cantidad ?? 0) }}"
                                class="w-full border-none bg-transparent text-4xl font-black text-indigo-900 focus:ring-0"
                                step="1" min="1" required oninput="recalcular()">
                        </div>
                    </div>
                </div>

                <!-- B. IMPUESTOS -->
                @php $tieneImpuesto = !empty($factura->impuesto_porcentaje) && $factura->impuesto_porcentaje > 0; @endphp
                <div class="mb-8">
                    <button type="button" onclick="toggleImpuestos()"
                        class="flex items-center gap-2 text-blue-600 font-bold hover:text-blue-800 transition group">
                        <span id="tax-icon"
                            class="text-xl transition duration-300 {{ $tieneImpuesto ? 'rotate-90' : '' }}">▶</span>
                        <span class="border-b-2 border-blue-100 group-hover:border-blue-300">Impuestos (IVA/Consumo)</span>
                    </button>

                    <div id="tax-block"
                        class="{{ $tieneImpuesto ? '' : 'hidden' }} mt-6 bg-gray-50 p-6 rounded-2xl border-2 border-dashed border-gray-200">
                        <input type="hidden" name="tiene_impuesto" id="tiene_impuesto"
                            value="{{ $tieneImpuesto ? '1' : '0' }}">
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-2 uppercase">Tipo de Impuesto</label>
                                <select name="impuesto_tipo"
                                    class="w-full border-2 border-white rounded-xl px-4 py-3 shadow-sm focus:border-blue-400 focus:ring-0 transition bg-white">
                                    <option value="IVA" {{ $factura->impuesto_tipo == 'IVA' ? 'selected' : '' }}>IVA</option>
                                    <option value="CONSUMO" {{ $factura->impuesto_tipo == 'CONSUMO' ? 'selected' : '' }}>
                                        Impuesto al Consumo</option>
                                    <option value="OTRO" {{ $factura->impuesto_tipo == 'OTRO' ? 'selected' : '' }}>Otro
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-2 uppercase">Impuesto (%)</label>
                                <input type="number" name="impuesto_porcentaje" id="tax-pct"
                                    value="{{ $factura->impuesto_porcentaje }}"
                                    class="w-full border-2 border-white rounded-xl px-4 py-3 shadow-sm focus:border-blue-400 focus:ring-0 transition bg-white font-bold"
                                    step="0.1" oninput="recalcular()">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- C. RESUMEN AUTOMÁTICO -->
                <div class="bg-gray-900 rounded-2xl p-8 text-white shadow-2xl relative overflow-hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-4">
                            <div class="flex justify-between items-center text-sm border-b border-gray-700 pb-2">
                                <span class="text-gray-400">Subtotal (Neto):</span>
                                <span id="res-subtotal" class="font-mono text-lg font-bold">$-</span>
                            </div>
                            <div class="flex justify-between items-center text-sm border-b border-gray-700 pb-2">
                                <span class="text-gray-400">Impuestos:</span>
                                <span id="res-tax" class="font-mono text-lg font-bold">$-</span>
                            </div>
                        </div>

                        <div
                            class="flex flex-col items-center justify-center p-4 bg-white/5 rounded-xl border border-white/10">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Costo Unitario Real
                            </p>
                            <p id="res-unitario" class="text-5xl font-black text-green-400 tracking-tighter">$-</p>
                        </div>
                    </div>
                </div>

                <div class="mt-8">
                    <label class="block text-xs font-bold text-gray-400 mb-2 uppercase">Notas adicionales</label>
                    <textarea name="notas" rows="2"
                        class="w-full border-2 border-gray-100 rounded-xl px-4 py-3 focus:border-gray-300 focus:ring-0 transition bg-gray-50">{{ $factura->notas }}</textarea>
                </div>

                <button type="submit"
                    class="w-full mt-10 bg-indigo-600 hover:bg-indigo-700 text-white py-6 rounded-2xl text-2xl font-black shadow-xl transition transform hover:-translate-y-1 active:scale-95">
                    Guardar cambios y ajustar inventario
                </button>
            </form>
        </div>
    </div>

    <script>
        let hasTax = {{ $tieneImpuesto ? 'true' : 'false' }};

        function toggleImpuestos() {
            const block = document.getElementById('tax-block');
            const icon = document.getElementById('tax-icon');
            const toggleInput = document.getElementById('tiene_impuesto');

            if (block.classList.contains('hidden')) {
                block.classList.remove('hidden');
                icon.classList.add('rotate-90');
                toggleInput.value = "1";
                hasTax = true;
            } else {
                block.classList.add('hidden');
                icon.classList.remove('rotate-90');
                toggleInput.value = "0";
                hasTax = false;
                document.getElementById('tax-pct').value = "";
            }
            recalcular();
        }

        function recalcular() {
            const total = parseFloat(document.getElementById('total_pagado').value) || 0;
            const qty = parseFloat(document.getElementById('cantidad').value) || 0;
            const pct = parseFloat(document.getElementById('tax-pct').value) || 0;

            let subtotal = total;
            let taxVal = 0;

            if (hasTax && pct > 0) {
                subtotal = total / (1 + (pct / 100));
                taxVal = total - subtotal;
            }

            const unitario = qty > 0 ? subtotal / qty : 0;

            document.getElementById('res-subtotal').textContent = '$' + subtotal.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            document.getElementById('res-tax').textContent = '$' + taxVal.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            document.getElementById('res-unitario').textContent = '$' + unitario.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 2 });
        }

        // Inicializar cálculo
        window.onload = recalcular;
    </script>
@endsection