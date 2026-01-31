@extends('layouts.app')

@section('title','compras')
@push('css-datatable')
<link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" type="text/css">
@endpush
@push('css')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    .row-not-space {
        width: 110px;
    }
</style>
@endpush

@section('content')

<div class="w-full px-4 md:px-6 py-4">
    <h1 class="text-3xl font-bold text-center text-gray-900 mb-6">Compras</h1>

    <x-breadcrumb.template>
        <x-breadcrumb.item :href="route('panel')" content="Inicio" />
        <x-breadcrumb.item active='true' content="Compras" />
    </x-breadcrumb.template>

    @can('crear-compra')
    <div class="mb-6">
        <a href="{{route('compras.create')}}">
            <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors">Añadir nuevo registro</button>
        </a>
    </div>
    @endcan

    <div class="bg-white rounded-lg shadow">
        <div class="bg-gray-100 border-b border-gray-300 px-6 py-3 font-semibold text-gray-900 flex items-center">
            <i class="fas fa-table mr-2"></i>
            Tabla compras
        </div>
        <div class="px-6 py-4 overflow-x-auto">
            <table id="datatablesSimple" class="w-full border-collapse">
                <thead class="bg-gray-100 border-b border-gray-300">
                    <tr>
                        <th class="text-left text-gray-900 font-semibold p-3 border border-gray-300">Comprobante</th>
                        <th class="text-left text-gray-900 font-semibold p-3 border border-gray-300">Proveedor</th>
                        <th class="text-left text-gray-900 font-semibold p-3 border border-gray-300">Fecha y hora</th>
                        <th class="text-left text-gray-900 font-semibold p-3 border border-gray-300">Usuario</th>
                        <th class="text-left text-gray-900 font-semibold p-3 border border-gray-300">Total</th>
                        <th class="text-left text-gray-900 font-semibold p-3 border border-gray-300">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($compras as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="text-gray-900 p-3 border border-gray-300">
                            <p class="font-semibold mb-1">{{$item->comprobante->nombre}}</p>
                            <p class="text-gray-600 text-sm mb-0">{{$item->numero_comprobante}}</p>
                        </td>
                        <td class="text-gray-900 p-3 border border-gray-300">
                            <p class="font-semibold mb-1">{{ ucfirst($item->proveedore->persona->tipo->value) }}</p>
                            <p class="text-gray-600 text-sm mb-0">{{$item->proveedore->persona->razon_social}}</p>
                        </td>
                        <td class="text-gray-900 p-3 border border-gray-300 w-28">
                            <p class="font-semibold mb-1">
                                <span class="mr-1"><i class="fa-solid fa-calendar-days"></i></span>{{$item->fecha}}
                            </p>
                            <p class="font-semibold mb-0">
                                <span class="mr-1"><i class="fa-solid fa-clock"></i></span>{{$item->hora}}
                            </p>
                        </td>
                        <td class="text-gray-900 p-3 border border-gray-300">
                            {{$item->user->name}}
                        </td>
                        <td class="text-gray-900 p-3 border border-gray-300">
                            {{$item->total}}
                        </td>
                        <td class="text-gray-900 p-3 border border-gray-300">
                            <div class="flex gap-2">

                                @can('mostrar-compra')
                                <form action="{{route('compras.show', ['compra'=>$item]) }}" method="get">
                                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors text-sm">
                                        Ver
                                    </button>
                                </form>
                                @endcan

                                <!-- Button trigger modal -->
                                <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors text-sm" onclick="document.getElementById('verPDFModal-{{$item->id}}').classList.remove('hidden')">
                                    PDF
                                </button>

                            </div>
                        </td>

                    </tr>

                    <!-- Modal -->
                    <div id="verPDFModal-{{$item->id}}" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                        <div class="bg-white rounded-lg shadow-lg max-w-4xl w-full mx-4 max-h-96 overflow-y-auto">
                            <div class="bg-gray-100 border-b border-gray-300 px-6 py-4 font-semibold text-gray-900">
                                PDF de la compra
                            </div>
                            <div class="px-6 py-4">
                                @if ($item->comprobante_path)
                                <iframe src="{{asset($item->comprobante_path)}}" style="width: 100%; height:500px;" frameborder="0"></iframe>
                                @else
                                <p class="text-gray-600">No se ha cargado un comprobante</p>
                                @endif
                            </div>
                            <div class="bg-gray-100 border-t border-gray-300 px-6 py-4 flex justify-end">
                                <button type="button" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-6 rounded-lg transition-colors" onclick="document.getElementById('verPDFModal-{{$item->id}}').classList.add('hidden')">Cerrar</button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" type="text/javascript"></script>
<script src="{{ asset('js/datatables-simple-demo.js') }}"></script>
@endpush
