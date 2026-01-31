@extends('layouts.app')

@section('title','ventas')

@push('css-datatable')
<link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" type="text/css">
@endpush
@push('css')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')

<div class="w-full px-4 md:px-6 py-4">
    <h1 class="text-3xl font-bold text-center text-gray-900 mb-6">Ventas</h1>
    <nav class="flex text-sm text-gray-600 mb-6">
        <a href="{{ route('panel') }}" class="hover:text-gray-900">Inicio</a>
        <span class="mx-2">/</span>
        <span class="text-gray-900 font-semibold">Ventas</span>
    </nav>

    @can('crear-venta')
    <div class="mb-6 flex gap-3">
        <a href="{{route('ventas.create')}}">
            <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors">Crear venta</button>
        </a>
         <a href="{{ route('export.excel-ventas-all') }}">
            <button type="button" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors">Exportar en excel</button>
        </a>
    </div>
    @endcan

    <div class="bg-white rounded-lg shadow mb-4">
        <div class="p-4 border-b border-gray-200 flex items-center gap-2">
            <i class="fas fa-table"></i>
            <span class="font-semibold text-gray-900">Tabla ventas</span>
        </div>
        <div class="p-6">
            <table id="datatablesSimple" class="w-full border-collapse">
                <thead>
                    <tr class="bg-blue-600 text-white">
                        <th class="border border-gray-300 p-3 text-left font-semibold">Comprobante</th>
                        <th class="border border-gray-300 p-3 text-left font-semibold">Cliente</th>
                        <th class="border border-gray-300 p-3 text-left font-semibold">Fecha y hora</th>
                        <th class="border border-gray-300 p-3 text-left font-semibold">Vendedor</th>
                        <th class="border border-gray-300 p-3 text-left font-semibold">Total</th>
                        <th class="border border-gray-300 p-3 text-center font-semibold">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($ventas as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="border border-gray-300 p-3">
                            <p class="font-semibold text-gray-900 mb-1">
                                {{$item->comprobante->tipo_comprobante}}
                            </p>
                            <p class="text-gray-600 text-sm mb-0">
                                {{$item->numero_comprobante}}
                            </p>
                        </td>
                        <td class="border border-gray-300 p-3">
                            <p class="font-semibold text-gray-900 mb-1">
                                {{ ucfirst($item->cliente->persona->tipo_persona) }}
                            </p>
                            <p class="text-gray-600 text-sm mb-0">
                                {{$item->cliente->persona->razon_social}}
                            </p>
                        </td>
                        <td class="border border-gray-300 p-3">
                            <div class="w-32">
                                <p class="font-semibold text-gray-900 mb-1">
                                    <span class="inline-block mx-1"><i class="fa-solid fa-calendar-days"></i></span>
                                    {{$item->fecha}}
                                </p>
                                <p class="font-semibold text-gray-900 mb-0">
                                    <span class="inline-block mx-1"><i class="fa-solid fa-clock"></i></span>
                                    {{$item->hora}}
                                </p>
                            </div>
                        </td>
                        <td class="border border-gray-300 p-3">
                            {{$item->user->name}}
                        </td>
                        <td class="border border-gray-300 p-3">
                            {{$item->total}}
                        </td>
                        <td class="border border-gray-300 p-3">
                            <div class="flex gap-2 items-center justify-center flex-wrap">

                                @can('mostrar-venta')
                                <form action="{{route('ventas.show', ['venta'=>$item]) }}" method="get">
                                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-1 px-3 rounded text-sm transition-colors">
                                        Ver
                                    </button>
                                </form>
                                @endcan

                                <a type="button" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-1 px-3 rounded text-sm transition-colors"
                                    href="{{ route('export.pdf-comprobante-venta',['id' => Crypt::encrypt($item->id)]) }}"
                                    target="_blank">
                                    Exportar
                                </a>

                            </div>
                        </td>
                    </tr>

                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" type="text/javascript"></script>
<script>
    // Simple-DataTables
    // https://github.com/fiduswriter/Simple-DataTables/wiki
    window.addEventListener('DOMContentLoaded', event => {
        const dataTable = new simpleDatatables.DataTable("#datatablesSimple", {})
    });
</script>
@endpush
