@extends('layouts.app')

@section('title','Inventario')

@push('css-datatable')
<link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" type="text/css">
@endpush

@push('css')
@endpush

@section('content')

<div class="w-full px-4 md:px-6 py-4">
    <h1 class="text-3xl font-bold text-center text-gray-900 mb-6">Inventario</h1>

    <x-breadcrumb.template>
        <x-breadcrumb.item :href="route('panel')" content="Inicio" />
        <x-breadcrumb.item active='true' content="Inventario" />
    </x-breadcrumb.template>

    <div class="mb-6">
        <button type="button"
            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors"
            onclick="document.getElementById('verPlanoModal').classList.remove('hidden')">
            Ver plano
        </button>
    </div>

    <div class="bg-white rounded-lg shadow">
        <div class="bg-gray-100 border-b border-gray-300 px-6 py-3 font-semibold text-gray-900 flex items-center">
            <i class="fas fa-table mr-2"></i>
            Tabla inventario
        </div>
        <div class="px-6 py-4 overflow-x-auto">
            <table id="datatablesSimple" class="w-full border-collapse">
                <thead class="bg-gray-100 border-b border-gray-300">
                    <tr>
                        <th class="text-left text-gray-900 font-semibold p-3 border border-gray-300">Producto</th>
                        <th class="text-left text-gray-900 font-semibold p-3 border border-gray-300">Stock</th>
                        <th class="text-left text-gray-900 font-semibold p-3 border border-gray-300">Ubicación</th>
                        <th class="text-left text-gray-900 font-semibold p-3 border border-gray-300">Fecha de Vencimiento</th>
                        <th class="text-left text-gray-900 font-semibold p-3 border border-gray-300">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($inventario as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="text-gray-900 p-3 border border-gray-300">
                            {{$item->producto->nombre_completo}}
                        </td>
                        <td class="text-gray-900 p-3 border border-gray-300">
                            {{$item->cantidad}}
                        </td>
                        <td class="text-gray-900 p-3 border border-gray-300">
                            {{$item->ubicacione->nombre}}
                        </td>
                        <td class="text-gray-900 p-3 border border-gray-300">
                            {{$item->fecha_vencimiento_format ?? $item->fecha_vencimiento_format}}
                        </td>
                        <td class="text-gray-900 p-3 border border-gray-300">

                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div id="verPlanoModal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 max-h-96 overflow-y-auto">
        <div class="bg-white rounded-lg shadow-lg max-w-4xl w-full mx-4">
            <div class="bg-gray-100 border-b border-gray-300 px-6 py-4 font-semibold text-gray-900">
                Plano de Ubicaciones
            </div>
            <div class="px-6 py-4 text-center">
                <img src="{{ asset('assets/img/plano.png')}}" alt="Plano de ubicaciones"
                    class="w-full border-2 border-gray-300 rounded">
            </div>
            <div class="bg-gray-100 border-t border-gray-300 px-6 py-4 flex justify-end">
                <button type="button" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-6 rounded-lg transition-colors" onclick="document.getElementById('verPlanoModal').classList.add('hidden')">Cerrar</button>
            </div>
        </div>
    </div>

</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" type="text/javascript"></script>
<script src="{{ asset('js/datatables-simple-demo.js') }}"></script>
@endpush
