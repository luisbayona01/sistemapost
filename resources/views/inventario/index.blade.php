@extends('layouts.app')

@section('title', 'Inventario')

@push('css-datatable')
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" type="text/css">
@endpush

@push('css')
@endpush

@section('content')

    <div class="w-full px-4 md:px-6 py-4">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Inventario Inteligente</h1>
                <p class="text-xs text-slate-500">Gestión compacta y predictiva</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('inventario.carga-masiva.index') }}"
                    class="bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold uppercase tracking-wider py-2 px-4 rounded-lg transition-colors shadow-lg shadow-emerald-200 flex items-center gap-2">
                    <i class="fas fa-file-upload"></i> Carga Masiva CSV
                </a>
            </div>
        </div>

        <x-breadcrumb.template>
            <x-breadcrumb.item :href="route('admin.dashboard.index')" content="Inicio" />
            <x-breadcrumb.item active='true' content="Inventario Inteligente" />
        </x-breadcrumb.template>

        <div class="bg-white rounded-lg shadow">
            <div class="bg-gray-100 border-b border-gray-300 px-6 py-3 font-semibold text-gray-900 flex items-center">
                <i class="fas fa-table mr-2"></i>
                Tabla inventario
            </div>
            <div class="px-6 py-4 overflow-x-auto">
                <table id="datatablesSimple" class="w-full border-collapse">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="text-left text-xs font-bold text-gray-500 uppercase tracking-wider p-2 border-none">
                                Producto</th>
                            <th class="text-left text-xs font-bold text-gray-500 uppercase tracking-wider p-2 border-none">
                                Categoría</th>
                            <th class="text-right text-xs font-bold text-gray-500 uppercase tracking-wider p-2 border-none">
                                Stock</th>
                            <th class="text-right text-xs font-bold text-gray-500 uppercase tracking-wider p-2 border-none">
                                Costo Neto</th>
                            <th class="text-right text-xs font-bold text-gray-500 uppercase tracking-wider p-2 border-none">
                                Margen %</th>
                            <th
                                class="text-center text-xs font-bold text-gray-500 uppercase tracking-wider p-2 border-none">
                                Estado</th>
                            <th class="text-right text-xs font-bold text-gray-500 uppercase tracking-wider p-2 border-none">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($inventario as $item)
                            @php
                                // Calcular estado de stock
                                $stockStatusColor = 'bg-emerald-100 text-emerald-700';
                                $stockStatusText = 'Saludable';

                                // Si el modelo Insumo/Producto tuviera stock minimo:
                                $minStock = $item->producto->stock_minimo ?? 10;

                                if ($item->cantidad <= 0) {
                                    $stockStatusColor = 'bg-red-100 text-red-700';
                                    $stockStatusText = 'Agotado';
                                } elseif ($item->cantidad <= $minStock) {
                                    $stockStatusColor = 'bg-amber-100 text-amber-700';
                                    $stockStatusText = 'Bajo';
                                }

                                // Costos y Márgenes REALES desde el modelo
                                $costo = (float) ($item->producto->costo_total_unitario ?? 0);
                                $margen = (float) ($item->producto->margen_ganancia_porcentual ?? 0);
                            @endphp
                            <tr class="hover:bg-slate-50 transition-colors text-sm">
                                <td class="p-2 text-gray-700 font-medium whitespace-nowrap">
                                    {{$item->producto->nombre}}
                                    <span class="block text-[10px] text-gray-400">{{$item->producto->codigo}}</span>
                                </td>
                                <td class="p-2 text-gray-600">
                                    {{$item->producto->categoria->nombre ?? 'General'}}
                                </td>
                                <td class="p-2 text-right font-mono font-bold text-slate-700">
                                    {{number_format($item->cantidad, 2)}}
                                </td>
                                <td class="p-2 text-right text-gray-600">
                                    ${{number_format($costo, 2)}}
                                </td>
                                <td class="p-2 text-right">
                                    <span class="font-bold text-slate-700">{{$margen}}%</span>
                                </td>
                                <td class="p-2 text-center">
                                    <span
                                        class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{$stockStatusColor}}">
                                        {{$stockStatusText}}
                                    </span>
                                </td>
                                <td class="p-2 text-right">
                                    <button type="button"
                                        onclick="abrirModalAjuste({{ $item->producto_id }}, '{{ $item->producto->nombre }}', {{ $item->cantidad }})"
                                        class="bg-blue-600 hover:bg-blue-500 text-white text-[10px] font-bold py-1 px-3 rounded-lg transition-all shadow-md">
                                        <i class="fas fa-plus-minus mr-1"></i> AJUSTAR
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow mb-8 mt-8">
            <div
                class="bg-gray-100 border-b border-gray-300 px-6 py-3 font-semibold text-gray-900 flex items-center justify-between">
                <div>
                    <i class="fas fa-barcode mr-2"></i>
                    Insumos (Materas Primas / Recetas)
                </div>
                <a href="{{ route('inventario-avanzado.almacen') }}"
                    class="text-[10px] text-blue-600 hover:underline font-bold uppercase tracking-widest">
                    <i class="fas fa-cog"></i> Configurar Insumos
                </a>
            </div>
            <div class="px-6 py-4 overflow-x-auto">
                <table id="datatablesInsumos" class="w-full border-collapse">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="text-left text-xs font-bold text-gray-500 uppercase tracking-wider p-2 border-none">
                                Insumo</th>
                            <th class="text-left text-xs font-bold text-gray-500 uppercase tracking-wider p-2 border-none">
                                Und. Medida</th>
                            <th class="text-right text-xs font-bold text-gray-500 uppercase tracking-wider p-2 border-none">
                                Stock Actual</th>
                            <th class="text-right text-xs font-bold text-gray-500 uppercase tracking-wider p-2 border-none">
                                Stock Min.</th>
                            <th class="text-right text-xs font-bold text-gray-500 uppercase tracking-wider p-2 border-none">
                                Costo Unit.</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($insumos as $insumo)
                            @php
                                $statusColor = 'text-slate-600';
                                if ($insumo->stock_actual <= 0)
                                    $statusColor = 'text-red-500 font-bold';
                                elseif ($insumo->stock_actual <= $insumo->stock_minimo)
                                    $statusColor = 'text-amber-500 font-bold';
                            @endphp
                            <tr class="hover:bg-slate-50 transition-colors text-sm">
                                <td class="p-2 text-gray-700 font-medium">
                                    {{$insumo->nombre}}
                                    <span class="block text-[10px] text-gray-400">{{$insumo->codigo ?? 'SIN COD'}}</span>
                                </td>
                                <td class="p-2 text-gray-600 uppercase text-xs">{{$insumo->unidad_medida}}</td>
                                <td class="p-2 text-right font-mono {{$statusColor}}">
                                    {{number_format((float) $insumo->stock_actual, 3)}}
                                </td>
                                <td class="p-2 text-right text-gray-400 text-xs">
                                    {{number_format((float) ($insumo->stock_minimo ?? 0), 3)}}
                                </td>
                                <td class="p-2 text-right text-gray-600">
                                    ${{number_format((float) $insumo->costo_unitario, 2)}}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>


        <!-- Modal de Ajuste Rápido -->
        <div id="modalAjuste"
            class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 backdrop-blur-sm">
            <div class="bg-white rounded-[2rem] shadow-2xl max-w-md w-full mx-4 p-8">
                <h3 class="text-xl font-black text-slate-900 mb-2 uppercase tracking-tight">Entrada/Salida Inventario
                </h3>
                <p id="ajusteProductoNombre" class="text-xs text-slate-500 font-bold mb-6"></p>

                <form id="formAjuste" action="{{ route('admin.inventario.ajuste-rapido') }}" method="POST">
                    @csrf
                    <input type="hidden" name="producto_id" id="ajusteProductoId">

                    <div class="space-y-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-2">Cantidad
                                Movimiento</label>
                            <input type="number" name="cantidad" required step="0.01" value="0"
                                class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-6 py-4 text-2xl font-black text-slate-900 focus:border-blue-500 outline-none">
                            <p class="text-[9px] text-slate-400 mt-2 font-bold">Positivo para ENTRADA, Negativo para
                                SALIDA
                            </p>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-2">Motivo /
                                Descripción</label>
                            <input type="text" name="motivo" placeholder="Ej: Compra, Reposición..." required
                                class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-4 py-3 text-sm font-bold text-slate-900 focus:border-blue-500 outline-none">
                        </div>
                    </div>

                    <div class="flex gap-3 mt-8">
                        <button type="button" onclick="cerrarModalAjuste()"
                            class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold py-4 rounded-2xl transition-all">CANCELAR</button>
                        <button type="submit"
                            class="flex-1 bg-slate-900 hover:bg-slate-800 text-white font-bold py-4 rounded-2xl shadow-xl transition-all">PROCESAR</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" type="text/javascript"></script>
    <script src="{{ asset('js/datatables-simple-demo.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (document.getElementById('datatablesInsumos')) {
                new simpleDatatables.DataTable("#datatablesInsumos", {
                    searchable: true,
                    fixedHeight: false,
                    perPage: 10
                });
            }
        });

        function abrirModalAjuste(id, nombre, actual) {
            document.getElementById('ajusteProductoId').value = id;
            document.getElementById('ajusteProductoNombre').innerText = nombre + ' (Stock actual: ' + actual + ')';
            document.getElementById('modalAjuste').classList.remove('hidden');
        }
        function cerrarModalAjuste() {
            document.getElementById('modalAjuste').classList.add('hidden');
        }
    </script>
@endpush