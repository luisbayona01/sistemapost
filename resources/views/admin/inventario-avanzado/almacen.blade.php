@extends('admin.inventario-avanzado.layout')

@section('inventory_content')
    <div class="max-w-full mx-auto px-4 sm:px-6 md:px-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Gestión de Almacén</h1>
                <p class="text-sm text-slate-500 mt-1">Control de insumos y lotes.</p>
            </div>
            <button onclick="document.getElementById('modal-nuevo-insumo').classList.remove('hidden')"
                class="bg-slate-900 text-white px-5 py-2.5 rounded-xl font-bold hover:bg-slate-800 transition-all shadow-lg shadow-slate-200 flex items-center gap-2 text-sm">
                <i class="fas fa-plus"></i> Nuevo Insumo
            </button>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-4">
                <table id="tableInsumos" class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-slate-500 uppercase font-bold text-xs">
                        <tr>
                            <th class="px-4 py-3 rounded-l-lg">Código</th>
                            <th class="px-4 py-3">Insumo</th>
                            <th class="px-4 py-3">Unidad</th>
                            <th class="px-4 py-3 text-center">Lotes</th>
                            <th class="px-4 py-3 text-right">Stock Min</th>
                            <th class="px-4 py-3 text-right">Stock Actual</th>
                            <th class="px-4 py-3 text-center rounded-r-lg">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($insumos as $insumo)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-4 py-3 font-mono text-xs text-slate-400">{{ $insumo->codigo ?: '---' }}</td>
                                <td class="px-4 py-3 font-bold text-slate-700">
                                    {{ $insumo->nombre }}
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        class="bg-slate-100 text-slate-600 px-2 py-1 rounded text-xs font-bold uppercase">{{ $insumo->unidad_medida }}</span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span
                                        class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-blue-50 text-blue-600 font-bold text-xs border border-blue-100 placeholder-popover"
                                        title="Lotes Activos: {{ $insumo->lotes->count() }}">
                                        {{ $insumo->lotes->count() }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right text-slate-500">{{ number_format($insumo->stock_minimo, 2) }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <span
                                        class="font-black {{ $insumo->stock_actual <= $insumo->stock_minimo ? 'text-red-500' : 'text-emerald-600' }}">
                                        {{ number_format($insumo->stock_actual, 2) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button onclick="openModalLote('{{ $insumo->id }}', '{{ $insumo->unidad_medida }}')"
                                            class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors"
                                            title="Cargar Stock">
                                            <i class="fas fa-truck-loading"></i>
                                        </button>
                                        <button
                                            onclick="openModalEdit('{{ $insumo->id }}', '{{ $insumo->nombre }}', '{{ $insumo->unidad_medida }}', '{{ $insumo->stock_minimo }}')"
                                            class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                            title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </div>

                                    <!-- Modal Carga Lote (Hidden Template) -->
                                    <div id="modal-lote-{{ $insumo->id }}" class="hidden modal-lote-data">
                                        <form action="{{ route('inventario-avanzado.insumos.lote.store', $insumo) }}"
                                            method="POST">
                                            @csrf
                                            <!-- Fields populate via JS -->
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Dinámico Carga Lote -->
    <div id="modal-carga-lote"
        class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-sm p-6 shadow-2xl">
            <h3 class="text-lg font-bold text-slate-900 mb-4">Entrada de Stock</h3>
            <form id="form-carga-lote" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Cantidad (<span
                            id="modal-unidad"></span>)</label>
                    <input type="number" step="0.001" name="cantidad" required
                        class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold focus:ring-2 focus:ring-slate-900 focus:border-slate-900 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Costo Unitario</label>
                    <input type="number" step="0.01" name="costo_unitario" required
                        class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold text-emerald-600 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Lote / Referencia</label>
                    <input type="text" name="numero_lote"
                        class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono text-xs">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Vencimiento</label>
                    <input type="date" name="fecha_vencimiento"
                        class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-xs">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Motivo / Descripción</label>
                    <input type="text" name="descripcion" required placeholder="Ej: Compra a proveedor"
                        class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold text-xs">
                </div>
                <div class="flex gap-2 mt-6">
                    <button type="button" onclick="document.getElementById('modal-carga-lote').classList.add('hidden')"
                        class="flex-1 py-2.5 text-slate-500 font-bold hover:bg-slate-50 rounded-lg transition-colors">Cancelar</button>
                    <button type="submit"
                        class="flex-1 py-2.5 bg-slate-900 text-white font-bold rounded-lg hover:bg-slate-800 transition-colors">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Nuevo Insumo -->
    <div id="modal-nuevo-insumo"
        class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl w-full max-w-md p-8 shadow-2xl">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-slate-900">Configurar Nuevo Insumo</h2>
                <button onclick="document.getElementById('modal-nuevo-insumo').classList.add('hidden')"
                    class="text-slate-400 hover:text-slate-600"><i class="fas fa-times"></i></button>
            </div>
            <form action="{{ route('inventario-avanzado.insumos.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-2">Nombre del Insumo</label>
                    <input type="text" name="nombre" required placeholder="Ej: Café en Grano"
                        class="w-full bg-slate-50 border-none rounded-xl p-4 font-bold">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-2">Unidad Base</label>
                        <select name="unidad_medida" class="w-full bg-slate-50 border-none rounded-xl p-4 font-bold">
                            <option value="kg">Kilogramos</option>
                            <option value="g">Gramos</option>
                            <option value="l">Litros</option>
                            <option value="ml">Mililitros</option>
                            <option value="und">Unidades</option>
                            <option value="oz">Onzas</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-2">Stock Mínimo</label>
                        <input type="number" name="stock_minimo" step="0.01"
                            class="w-full bg-slate-50 border-none rounded-xl p-4 font-bold">
                    </div>
                </div>
                <button type="submit" class="w-full bg-emerald-600 text-white py-4 rounded-xl font-bold mt-4">Guardar
                    Insumo</button>
            </form>
        </div>
    </div>

    <!-- Modal Editar Insumo -->
    <div id="modal-editar-insumo"
        class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl w-full max-w-md p-8 shadow-2xl">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-slate-900">Editar Insumo</h2>
                <button onclick="document.getElementById('modal-editar-insumo').classList.add('hidden')"
                    class="text-slate-400 hover:text-slate-600"><i class="fas fa-times"></i></button>
            </div>
            <form id="form-editar-insumo" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-2">Nombre del Insumo</label>
                    <input type="text" name="nombre" id="edit_nombre" required
                        class="w-full bg-slate-50 border-none rounded-xl p-4 font-bold">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-2">Unidad Base</label>
                        <select name="unidad_medida" id="edit_unidad"
                            class="w-full bg-slate-50 border-none rounded-xl p-4 font-bold">
                            <option value="kg">Kilogramos</option>
                            <option value="g">Gramos</option>
                            <option value="l">Litros</option>
                            <option value="ml">Mililitros</option>
                            <option value="und">Unidades</option>
                            <option value="oz">Onzas</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-2">Stock Mínimo</label>
                        <input type="number" name="stock_minimo" id="edit_stock_minimo" step="0.01"
                            class="w-full bg-slate-50 border-none rounded-xl p-4 font-bold">
                    </div>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-4 rounded-xl font-bold mt-4">Actualizar
                    Insumo</button>
            </form>
        </div>
    </div>

    @push('js')
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" type="text/javascript"></script>
        <script>
            window.addEventListener('DOMContentLoaded', event => {
                const dataTable = new simpleDatatables.DataTable("#tableInsumos", {
                    labels: {
                        placeholder: "Buscar insumo...",
                        perPage: "Registros por página",
                        noRows: "No se encontraron insumos",
                        info: "Mostrando {start} a {end} de {rows} insumos"
                    }
                });
            });

            function openModalLote(insumoId, unidad) {
                const modal = document.getElementById('modal-carga-lote');
                const form = document.getElementById('form-carga-lote');
                const unidadSpan = document.getElementById('modal-unidad');

                // Construct route dynamically
                const actionRoute = "{{ route('inventario-avanzado.insumos.lote.store', ':id') }}".replace(':id', insumoId);

                form.action = actionRoute;
                unidadSpan.textContent = unidad;

                modal.classList.remove('hidden');
            }

            function openModalEdit(insumoId, nombre, unidad, stockMinimo) {
                const modal = document.getElementById('modal-editar-insumo');
                const form = document.getElementById('form-editar-insumo');

                const actionRoute = "{{ route('inventario-avanzado.insumos.update', ':id') }}".replace(':id', insumoId);
                form.action = actionRoute;

                document.getElementById('edit_nombre').value = nombre;
                document.getElementById('edit_unidad').value = unidad;
                document.getElementById('edit_stock_minimo').value = stockMinimo;

                modal.classList.remove('hidden');
            }
        </script>
    @endpush
@endsection