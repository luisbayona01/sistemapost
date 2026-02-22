@extends('layouts.app')

@section('title','marcas')

@push('css-datatable')
<link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" type="text/css">
@endpush

@push('css')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')

<div class="w-full px-4 md:px-6 py-4">
    <h1 class="text-3xl font-bold text-center text-gray-900 mb-6">Marcas</h1>

    <x-breadcrumb.template>
        <x-breadcrumb.item :href="route('admin.dashboard.index')" content="Inicio" />
        <x-breadcrumb.item active='true' content="Marcas" />
    </x-breadcrumb.template>

    @can('crear-marca')
    <div class="mb-6">
        <a href="{{route('marcas.create')}}">
            <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors">Añadir nuevo registro</button>
        </a>
    </div>
    @endcan

    <div class="bg-white rounded-lg shadow">
        <div class="bg-gray-100 border-b border-gray-300 px-6 py-3 font-semibold text-gray-900 flex items-center">
            <i class="fas fa-table mr-2"></i>
            Tabla marcas
        </div>
        <div class="px-6 py-4">
            <table id="datatablesSimple" class="w-full border-collapse">
                <thead class="bg-gray-100 border-b border-gray-300">
                    <tr>
                        <th class="text-left text-gray-900 font-semibold p-3 border border-gray-300">Nombre</th>
                        <th class="text-left text-gray-900 font-semibold p-3 border border-gray-300">Descripción</th>
                        <th class="text-left text-gray-900 font-semibold p-3 border border-gray-300">Estado</th>
                        <th class="text-left text-gray-900 font-semibold p-3 border border-gray-300">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($marcas as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="text-gray-900 p-3 border border-gray-300">
                            {{$item->caracteristica->nombre}}
                        </td>
                        <td class="text-gray-900 p-3 border border-gray-300">
                            {{$item->caracteristica->descripcion}}
                        </td>
                        <td class="text-gray-900 p-3 border border-gray-300">
                            <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold {{ $item->caracteristica->estado ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $item->caracteristica->estado ? 'Activo' : 'Eliminado'}}</span>
                        </td>
                        <td class="text-gray-900 p-3 border border-gray-300">
                            <div class="flex justify-around items-center">

                                <div class="relative group">
                                    <button title="Opciones" class="text-gray-600 hover:text-gray-900 p-2">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div class="hidden group-hover:block absolute right-0 z-10 bg-white border border-gray-300 rounded-lg shadow-lg min-w-max">
                                        @can('editar-marca')
                                        <a class="block px-4 py-2 hover:bg-gray-100 text-gray-900 text-sm" href="{{route('marcas.edit',['marca'=>$item])}}">Editar</a>
                                        @endcan
                                    </div>
                                </div>

                                <div class="border-l border-gray-300" style="height: 24px;"></div>

                                <div>
                                    @can('eliminar-marca')
                                    @if ($item->caracteristica->estado == 1)
                                    <button title="Eliminar" onclick="document.getElementById('confirmModal-{{$item->id}}').classList.remove('hidden')" class="text-gray-600 hover:text-red-600 p-2">
                                        <i class="fas fa-trash-can"></i>
                                    </button>
                                    @else
                                    <button title="Restaurar" onclick="document.getElementById('confirmModal-{{$item->id}}').classList.remove('hidden')" class="text-gray-600 hover:text-green-600 p-2">
                                        <i class="fa-solid fa-rotate"></i>
                                    </button>
                                    @endif
                                    @endcan
                                </div>
                            </div>
                        </td>
                    </tr>

                    <!-- Modal -->
                    <div id="confirmModal-{{$item->id}}" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                        <div class="bg-white rounded-lg shadow-lg max-w-md w-full mx-4">
                            <div class="bg-gray-100 border-b border-gray-300 px-6 py-4 font-semibold text-gray-900">
                                Mensaje de confirmación
                            </div>
                            <div class="px-6 py-4">
                                {{ $item->caracteristica->estado == 1 ? '¿Seguro que quieres eliminar la marca?' : '¿Seguro que quieres restaurar la marca?' }}
                            </div>
                            <div class="bg-gray-100 border-t border-gray-300 px-6 py-4 flex justify-end gap-2">
                                <button type="button" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded-lg transition-colors"
                                    onclick="document.getElementById('confirmModal-{{$item->id}}').classList.add('hidden')">Cerrar</button>
                                <form action="{{ route('marcas.destroy',['marca'=>$item->id]) }}" method="post" style="display: inline;">
                                    @method('DELETE')
                                    @csrf
                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors">Confirmar</button>
                                </form>
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
