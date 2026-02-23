@extends('layouts.app')

@section('title','Kardex')

@push('css-datatable')
<link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
@endpush

@push('css')
@endpush

@section('content')

<div class="w-full px-4 md:px-6 py-4">
    <h1 class="text-3xl font-bold text-center text-gray-900 mb-6">Kardex</h1>

    <x-breadcrumb.template>
        <x-breadcrumb.item :href="route('admin.dashboard.index')" content="Inicio" />
        <x-breadcrumb.item active='true' content="Kardex" />
    </x-breadcrumb.template>

    <div class="mb-6">
        <form action="{{route('kardex.index')}}" method="get">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                <label for="producto_id" class="md:col-span-2 block text-sm font-medium text-gray-700">
                    Producto</label>
                <div class="md:col-span-8">
                    <select name="producto_id" id="producto_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 selectpicker"
                        data-live-search='true' data-size='3' title='Busque un producto aquí'>
                        @foreach ($productos as $item)
                        <option value="{{$item->id}}" {{$item->id == $producto_id ? 'selected': ''}}>
                            {{$item->nombre_completo}}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors">
                        Buscar</button>
                </div>
            </div>
        </form>
    </div>

    @if ($kardex->count())
    <div class="bg-white rounded-lg shadow">
        <div class="bg-gray-100 border-b border-gray-300 px-6 py-3 font-semibold text-gray-900 flex items-center">
            <i class="fas fa-table mr-2"></i>
            Tabla kardex del producto
        </div>
        <div class="px-6 py-4 overflow-x-auto">
            <table id="datatablesSimple" class="w-full border-collapse">
                <thead class="bg-gray-100 border-b border-gray-300">
                    <tr>
                        <th class="text-left text-gray-900 font-semibold p-3 border border-gray-300">Fecha y Hora</th>
                        <th class="text-left text-gray-900 font-semibold p-3 border border-gray-300">Transacción</th>
                        <th class="text-left text-gray-900 font-semibold p-3 border border-gray-300">Descripción </th>
                        <th class="text-left text-gray-900 font-semibold p-3 border border-gray-300">Entrada</th>
                        <th class="text-left text-gray-900 font-semibold p-3 border border-gray-300">Salida</th>
                        <th class="text-left text-gray-900 font-semibold p-3 border border-gray-300">Saldo</th>
                        <th class="text-left text-gray-900 font-semibold p-3 border border-gray-300">Costo unitario</th>
                        <th class="text-left text-gray-900 font-semibold p-3 border border-gray-300">Costo total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($kardex as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="text-gray-900 p-3 border border-gray-300">
                            {{$item->fecha}} - {{$item->hora}}
                        </td>
                        <td class="text-gray-900 p-3 border border-gray-300">
                            {{$item->tipo_transaccion}}
                        </td>
                        <td class="text-gray-900 p-3 border border-gray-300">
                            {{$item->descripcion_transaccion}}
                        </td>
                        <td class="text-gray-900 p-3 border border-gray-300">
                            {{$item->entrada}}
                        </td>
                        <td class="text-gray-900 p-3 border border-gray-300">
                            {{$item->salida}}
                        </td>
                        <td class="text-gray-900 p-3 border border-gray-300">
                            {{$item->saldo}}
                        </td>
                        <td class="text-gray-900 p-3 border border-gray-300">
                            {{$item->costo_unitario}}
                        </td>
                        <td class="text-gray-900 p-3 border border-gray-300">
                            {{$item->costo_total}}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>
    @else
    <p class="text-center my-5">Sin datos</p>
    @endif


</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" type="text/javascript"></script>
<script src="{{ asset('js/datatables-simple-demo.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
@endpush
