@extends('layouts.app')

@section('title','empleados')

@push('css-datatable')
<link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" type="text/css">
@endpush

@push('css')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    .img {
        width: 80px;
    }
</style>
@endpush

@section('content')

<div class="w-full px-4 md:px-6 py-4">
    <h1 class="text-3xl font-bold text-center text-gray-900 mb-6">Empleados</h1>

    <x-breadcrumb.template>
        <x-breadcrumb.item :href="route('panel')" content="Inicio" />
        <x-breadcrumb.item active='true' content="Empleados" />
    </x-breadcrumb.template>

    @can('crear-empleado')
    <div class="mb-6">
        <a href="{{route('empleados.create')}}">
            <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors">Añadir nuevo registro</button>
        </a>
    </div>
    @endcan

    <div class="bg-white rounded-lg shadow">
        <div class="bg-gray-100 border-b border-gray-300 px-6 py-3 font-semibold text-gray-900 flex items-center">
            <i class="fas fa-table mr-2"></i>
            Tabla empleados
        </div>
        <div class="px-6 py-4">
            <table id="datatablesSimple" class="w-full border-collapse">
                <thead class="bg-gray-100 border-b border-gray-300">
                    <tr>
                        <th class="text-left text-gray-900 font-semibold p-3 border border-gray-300">Nombres y Apellidos</th>
                        <th class="text-left text-gray-900 font-semibold p-3 border border-gray-300">Cargo</th>
                        <th class="text-left text-gray-900 font-semibold p-3 border border-gray-300">Imagen</th>
                        <th class="text-left text-gray-900 font-semibold p-3 border border-gray-300">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($empleados as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="text-gray-900 p-3 border border-gray-300">
                            {{$item->razon_social}}
                        </td>
                        <td class="text-gray-900 p-3 border border-gray-300">
                            {{$item->cargo}}
                        </td>
                        <td class="text-gray-900 p-3 border border-gray-300">
                            @if ($item->img_path)
                            <img class="w-20 h-20 border-2 border-gray-300 rounded mx-auto"
                                src="{{ asset($item->img_path)}}"
                                alt="{{$item->razon_social}}">
                            @else
                            <p class="text-gray-600 text-center text-sm">No tiene una imagen</p>
                            @endif
                        </td>
                        <td class="text-gray-900 p-3 border border-gray-300">
                            <div class="flex justify-around items-center">

                                <div class="relative group">
                                    <button title="Opciones" class="text-gray-600 hover:text-gray-900 p-2">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div class="hidden group-hover:block absolute right-0 z-10 bg-white border border-gray-300 rounded-lg shadow-lg min-w-max">
                                        @can('editar-empleado')
                                        <a class="block px-4 py-2 hover:bg-gray-100 text-gray-900 text-sm" href="{{route('empleados.edit',['empleado'=>$item])}}">Editar</a>
                                        @endcan
                                    </div>
                                </div>

                                <div class="border-l border-gray-300" style="height: 24px;"></div>

                                <div>
                                    @can('eliminar-empleado')
                                    <button title="Eliminar"
                                        onclick="document.getElementById('confirmModal-{{$item->id}}').classList.remove('hidden')"
                                        class="text-gray-600 hover:text-red-600 p-2">
                                        <i class="fas fa-trash-can"></i>
                                    </button>
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
                                ¿Seguro que quieres eliminar el empleado?
                            </div>
                            <div class="bg-gray-100 border-t border-gray-300 px-6 py-4 flex justify-end gap-2">
                                <button type="button" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded-lg transition-colors"
                                    onclick="document.getElementById('confirmModal-{{$item->id}}').classList.add('hidden')">Cerrar</button>
                                <form action="{{ route('empleados.destroy',['empleado'=>$item->id]) }}" method="post" style="display: inline;">
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
        <div class="bg-gray-50 border-t border-gray-300 px-6 py-4">
            <form action="{{ route('import.excel-empleados') }}"
                method="post" enctype="multipart/form-data"
                class="mb-3">
                @csrf
                <div class="mb-3">
                    <label for="file" class="block text-sm font-medium text-gray-700 mb-2">
                        Subir archivo:</label>
                    <input type="file" name="file" id="file"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors">
                    Importar datos</button>
            </form>
        </div>
    </div>

</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" type="text/javascript"></script>
<script src="{{ asset('js/datatables-simple-demo.js') }}"></script>
@endpush
