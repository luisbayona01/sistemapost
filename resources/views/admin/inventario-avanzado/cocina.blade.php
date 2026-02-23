@extends('admin.inventario-avanzado.layout')

@section('inventory_content')
    <div class="max-w-full mx-auto px-4 sm:px-6 md:px-8">
        <div class="mb-6">
            <h1 class="text-2xl font-black text-slate-900 tracking-tighter uppercase">Análisis de Costos & Recetas</h1>
            <p class="text-sm text-slate-500 mt-1">Define cuánto te cuesta producir cada snack y cuánto estás ganando
                realmente.</p>
        </div>

        <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
            <div class="p-4">
                <table id="tableCocina" class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-slate-400 uppercase font-black text-[10px] tracking-widest">
                        <tr>
                            <th class="px-4 py-4 rounded-l-2xl">Producto / Snack</th>
                            <th class="px-4 py-4">Categoría</th>
                            <th class="px-4 py-4 text-right" title="Materia prima + Gastos operativos fijos">Costo de
                                Producción <i class="fas fa-info-circle text-[9px]"></i></th>
                            <th class="px-4 py-4 text-right">Precio de Venta</th>
                            <th class="px-4 py-4 text-center" title="Precio de venta – Costo del producto">Ganancia Bruta
                                (Venta - Costo) <i class="fas fa-info-circle text-[9px]"></i></th>
                            <th class="px-4 py-4 text-center rounded-r-2xl">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($productos as $producto)
                            <tr class="hover:bg-slate-50/50 transition-colors group">
                                <td class="px-4 py-4 font-bold text-slate-700 tracking-tight">{{ $producto->nombre }}</td>
                                <td class="px-4 py-4 text-xs font-bold text-slate-400 uppercase tracking-tighter">
                                    {{ $producto->categoria->nombre ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-4 text-right font-mono text-slate-600">
                                    ${{ number_format($producto->analisis['costo_final'], 0) }}</td>
                                <td class="px-4 py-4 text-right font-black text-slate-900">
                                    ${{ number_format($producto->precio, 0) }}</td>
                                <td class="px-4 py-4 text-center">
                                    <span
                                        class="px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest shadow-sm {{ $producto->analisis['margen_porcentaje'] < 30 ? 'bg-rose-50 text-rose-600 border border-rose-100' : 'bg-emerald-50 text-emerald-600 border border-emerald-100' }}">
                                        {{ number_format($producto->analisis['margen_porcentaje'], 1) }}%
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button onclick="openRecipeModal({{ $producto->id }})"
                                        class="text-blue-600 hover:text-blue-800 font-medium text-xs border border-blue-200 hover:bg-blue-50 px-3 py-1.5 rounded-lg transition-all">
                                        <i class="fas fa-utensils mr-1"></i> Receta
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modales de Receta (Generados por loop para simplicidad inmediata, idealmente AJAX) -->
    @foreach($productos as $producto)
        <div id="modal-recipe-{{ $producto->id }}"
            class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-start justify-center p-4 pt-10 overflow-y-auto">
            <div class="bg-white rounded-3xl w-full max-w-4xl shadow-2xl my-8">
                <div class="flex justify-between items-center p-6 border-b border-slate-100">
                    <div>
                        <h2 class="text-xl font-bold text-slate-900">{{ $producto->nombre }}</h2>
                        <p class="text-xs text-slate-400">Gestión de Ingredientes y Análisis</p>
                    </div>
                    <button onclick="document.getElementById('modal-recipe-{{ $producto->id }}').classList.add('hidden')"
                        class="text-slate-400 hover:text-slate-600 w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-100 transition-all">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="p-6 grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Columna Izquierda: Lista de Ingredientes -->
                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-slate-50 rounded-2xl p-4">
                            <h3 class="text-xs font-bold text-slate-500 uppercase mb-4">Ingredientes Actuales</h3>
                            <div class="space-y-3">
                                @forelse($producto->insumos as $insumo)
                                    <div
                                        class="flex items-center justify-between bg-white p-3 rounded-xl border border-slate-100 shadow-sm">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs">
                                                <i class="fas fa-box"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-slate-700">{{ $insumo->nombre }}</p>
                                                <p class="text-[10px] text-slate-400">
                                                    {{ $insumo->pivot->cantidad }} {{ $insumo->unidad_medida }}
                                                    @if($insumo->pivot->merma_esperada > 0)
                                                        <span class="text-red-400">• Merma {{ $insumo->pivot->merma_esperada }}%</span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <form
                                                action="{{ route('inventario-avanzado.recetas.destroy', ['receta' => $insumo->pivot->id]) }}"
                                                method="POST">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="text-slate-300 hover:text-red-500 transition-colors p-1">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-center text-sm text-slate-400 italic py-4">Sin ingredientes definidos.</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- Form Añadir Insumo -->
                        <div class="border-t border-slate-100 pt-6">
                            <h3 class="text-xs font-bold text-slate-500 uppercase mb-4">Añadir Ingrediente</h3>
                            <form action="{{ route('inventario-avanzado.recetas.store') }}" method="POST"
                                class="grid grid-cols-12 gap-3 items-end">
                                @csrf
                                <input type="hidden" name="producto_id" value="{{ $producto->id }}">
                                <div class="col-span-12 md:col-span-5">
                                    <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Insumo</label>
                                    <select name="insumo_id" class="w-full bg-slate-50 border-none rounded-lg text-sm p-2.5">
                                        @foreach($insumosBase as $ib)
                                            <option value="{{ $ib->id }}">{{ $ib->nombre }} ({{ $ib->unidad_medida }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-span-6 md:col-span-3">
                                    <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Cant.</label>
                                    <input type="number" step="0.001" name="cantidad" required
                                        class="w-full bg-slate-50 border-none rounded-lg text-sm p-2.5" placeholder="0.00">
                                </div>
                                <div class="col-span-6 md:col-span-2">
                                    <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Merma %</label>
                                    <input type="number" step="0.1" name="merma_esperada"
                                        class="w-full bg-slate-50 border-none rounded-lg text-sm p-2.5" placeholder="0">
                                </div>
                                <div class="col-span-12 md:col-span-2">
                                    <button type="submit"
                                        class="w-full bg-slate-900 text-white rounded-lg p-2.5 text-sm font-bold hover:bg-slate-800">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Columna Derecha: Análisis Financiero -->
                    <div class="space-y-6">
                        <div class="bg-slate-900 rounded-2xl p-5 text-white">
                            <h3 class="text-xs font-bold opacity-60 uppercase mb-4">Métricas Clave</h3>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center text-sm border-b border-white/10 pb-2">
                                    <span>Costo Receta</span>
                                    <span
                                        class="font-mono text-emerald-400">${{ number_format($producto->analisis['costo_receta'], 2) }}</span>
                                </div>
                                <div class="flex justify-between items-center text-sm border-b border-white/10 pb-2">
                                    <span>Gastos Indir.</span>
                                    <span
                                        class="font-mono opacity-80">+${{ number_format($producto->analisis['costo_indirectos'], 2) }}</span>
                                </div>
                                <div class="flex justify-between items-center border-b border-white/10 pb-2">
                                    <span class="font-bold">Costo Total</span>
                                    <span
                                        class="font-mono font-bold text-lg text-rose-300">{{ $producto->analisis['costo_final'] > 0 ? '-' : '' }}${{ number_format($producto->analisis['costo_final'], 2) }}</span>
                                </div>
                                @if(isset($producto->analisis['monto_impuesto']) && $producto->analisis['monto_impuesto'] > 0)
                                    <div class="flex justify-between items-center text-sm border-b border-white/10 pb-2 pt-2">
                                        <span>Impuesto ({{ $producto->tipo_impuesto }})</span>
                                        <span
                                            class="font-mono opacity-80 text-amber-300">{{ $producto->analisis['monto_impuesto'] > 0 ? '-' : '' }}${{ number_format($producto->analisis['monto_impuesto'], 2) }}</span>
                                    </div>
                                    <div class="flex justify-between items-center text-sm border-b border-white/10 pb-2">
                                        <span>Ingreso Neto</span>
                                        <span
                                            class="font-mono text-emerald-400">${{ number_format($producto->analisis['ingreso_neto'], 2) }}</span>
                                    </div>
                                @endif
                            </div>

                            <div class="mt-6">
                                @php
                                    $PV40 = $producto->analisis['costo_final'] / (1 - 0.4);
                                @endphp
                                <p class="text-[10px] uppercase font-bold opacity-60 mb-2">Precio Sugerido (40%)</p>
                                <div class="flex gap-2 items-center">
                                    <span class="text-xl font-black text-emerald-400">${{ number_format($PV40, 2) }}</span>
                                    <form action="{{ route('inventario-avanzado.cocina.update-precio', $producto->id) }}"
                                        method="POST">
                                        @csrf
                                        <input type="hidden" name="precio" value="{{ $PV40 }}">
                                        <button type="submit"
                                            class="text-[10px] bg-white/20 hover:bg-white/30 px-2 py-1 rounded text-white transition-colors">Aplicar</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="bg-slate-50 rounded-2xl p-5 border border-slate-100">
                            <h3 class="text-xs font-bold text-slate-500 uppercase mb-4">Configuración de Precio y Costo</h3>
                            <form action="{{ route('inventario-avanzado.cocina.update-precio', $producto->id) }}" method="POST"
                                class="space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Gasto Operativo
                                        Fijo (Moneda)</label>
                                    <input type="number" step="0.01" name="gasto_operativo_fijo"
                                        value="{{ $producto->gasto_operativo_fijo }}"
                                        class="w-full bg-white border border-slate-200 rounded-lg p-2 text-sm font-bold text-slate-700 outline-none focus:border-indigo-500">
                                </div>

                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Tipo
                                            Impuesto</label>
                                        <select name="tipo_impuesto"
                                            class="w-full bg-white border border-slate-200 rounded-lg p-2 text-xs font-bold outline-none">
                                            <option value="EXENTO" {{ $producto->tipo_impuesto == 'EXENTO' ? 'selected' : '' }}>
                                                Exento</option>
                                            <option value="IVA" {{ $producto->tipo_impuesto == 'IVA' ? 'selected' : '' }}>IVA
                                            </option>
                                            <option value="IMPOCONSUMO" {{ $producto->tipo_impuesto == 'IMPOCONSUMO' ? 'selected' : '' }}>Impoconsumo</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">%
                                            Impuesto</label>
                                        <input type="number" step="0.01" name="porcentaje_impuesto"
                                            value="{{ $producto->porcentaje_impuesto }}"
                                            class="w-full bg-white border border-slate-200 rounded-lg p-2 text-sm font-bold text-slate-700 outline-none">
                                    </div>
                                </div>

                                <div class="pt-2">
                                    <label class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Precio de Venta
                                        Final</label>
                                    <div class="flex gap-2">
                                        <input type="number" step="0.01" name="precio" value="{{ $producto->precio }}"
                                            class="w-full bg-white border border-slate-200 rounded-lg p-2.5 text-lg font-bold text-slate-900 outline-none focus:border-indigo-500">
                                        <button type="submit"
                                            class="bg-indigo-600 text-white px-4 rounded-lg hover:bg-indigo-700 transition-colors">
                                            <i class="fas fa-save"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                            <p
                                class="text-xs text-center mt-3 {{ $producto->analisis['margen_porcentaje'] < 30 ? 'text-amber-500' : 'text-emerald-600' }} font-bold">
                                Margen Actual: {{ number_format($producto->analisis['margen_porcentaje'], 1) }}%
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    @push('js')
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" type="text/javascript"></script>
        <script>
            window.addEventListener('DOMContentLoaded', event => {
                new simpleDatatables.DataTable("#tableCocina", {
                    searchable: true,
                    fixedHeight: true,
                    perPage: 15
                });
            });

            function openRecipeModal(id) {
                document.getElementById('modal-recipe-' + id).classList.remove('hidden');
            }
        </script>
    @endpush
@endsection