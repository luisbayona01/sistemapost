@extends('layouts.app')

@section('title','roles')
@push('css-datatable')
<link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" type="text/css">
@endpush
@push('css')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')

<div class="w-full px-4 md:px-6 py-4">
    <h1 class="text-3xl font-bold text-center text-gray-900 mb-6">Roles</h1>

    <x-breadcrumb.template>
        <x-breadcrumb.item :href="route('admin.dashboard.index')" content="Inicio" />
        <x-breadcrumb.item active='true' content="Roles" />
    </x-breadcrumb.template>

    @can('crear-role')
    <div class="mb-6">
        <a href="{{route('roles.create')}}">
            <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors">Añadir nuevo rol</button>
        </a>
    </div>
    @endcan

    <div class="bg-white rounded-lg shadow">
        <div class="bg-gray-100 border-b border-gray-300 px-6 py-3 font-semibold text-gray-900 flex items-center">
            <i class="fas fa-table mr-2"></i>
            Tabla roles
        </div>
        <div class="px-6 py-4 overflow-x-auto">
            <table id="datatablesSimple" class="w-full border-collapse">
                <thead class="bg-gray-100 border-b border-gray-300">
                    <tr>
                        <th class="text-left text-gray-900 font-semibold p-3 border border-gray-300">Rol</th>
                        <th class="text-left text-gray-900 font-semibold p-3 border border-gray-300">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($roles as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="text-gray-900 p-3 border border-gray-300">
                            {{$item->name}}
                        </td>
                        <td class="text-gray-900 p-3 border border-gray-300">
                            <div class="flex gap-2 flex-wrap">

                                @can('editar-role')
                                <form action="{{route('roles.edit',['role'=>$item])}}" method="get">
                                    <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-4 rounded-lg transition-colors">Editar</button>
                                </form>
                                @endcan

                                @can('eliminar-role')
                                <button type="button" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors" onclick="document.getElementById('confirmModal-{{$item->id}}').classList.remove('hidden')">Eliminar</button>
                                @endcan

                            </div>
                        </td>
                    </tr>

                    <!-- Modal de confirmación-->
                    <div id="confirmModal-{{$item->id}}" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                        <div class="bg-white rounded-lg shadow-lg max-w-md w-full mx-4">
                            <div class="bg-gray-100 border-b border-gray-300 px-6 py-4 font-semibold text-gray-900">
                                Mensaje de confirmación
                            </div>
                            <div class="px-6 py-4">
                                ¿Seguro que quieres eliminar el rol?
                            </div>
                            <div class="bg-gray-100 border-t border-gray-300 px-6 py-4 flex justify-end gap-2">
                                <button type="button" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-6 rounded-lg transition-colors" onclick="document.getElementById('confirmModal-{{$item->id}}').classList.add('hidden')">Cerrar</button>
                                <form action="{{ route('roles.destroy',['role'=>$item->id]) }}" method="post">
                                    @method('DELETE')
                                    @csrf
                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors">Confirmar</button>
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
