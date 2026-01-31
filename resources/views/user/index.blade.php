@extends('layouts.app')

@section('title','usuarios')

@push('css-datatable')
<link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" type="text/css">
@endpush

@push('css')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')

<div class="w-full px-4 md:px-6 py-4">
    <h1 class="text-3xl font-bold text-center text-gray-900 mb-6">Usuarios</h1>

    <x-breadcrumb.template>
        <x-breadcrumb.item :href="route('panel')" content="Inicio" />
        <x-breadcrumb.item active='true' content="Usuarios" />
    </x-breadcrumb.template>

    @can('crear-user')
    <div class="mb-6">
        <a href="{{route('users.create')}}">
            <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors">
                Añadir nuevo usuario</button>
        </a>
    </div>
    @endcan

    <div class="bg-white rounded-lg shadow">
        <div class="bg-gray-100 border-b border-gray-300 px-6 py-3 font-semibold text-gray-900 flex items-center">
            <i class="fas fa-table mr-2"></i>
            Tabla de usuarios
        </div>
        <div class="px-6 py-4 overflow-x-auto">
            <table id="datatablesSimple" class="w-full border-collapse">
                <thead class="bg-gray-100 border-b border-gray-300">
                    <tr>
                        <th class="text-left text-gray-900 font-semibold p-3 border border-gray-300">Empleado</th>
                        <th class="text-left text-gray-900 font-semibold p-3 border border-gray-300">Alias</th>
                        <th class="text-left text-gray-900 font-semibold p-3 border border-gray-300">Email</th>
                        <th class="text-left text-gray-900 font-semibold p-3 border border-gray-300">Rol</th>
                        <th class="text-left text-gray-900 font-semibold p-3 border border-gray-300">Estado</th>
                        <th class="text-left text-gray-900 font-semibold p-3 border border-gray-300">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="text-gray-900 p-3 border border-gray-300">{{$item->empleado->razon_social}}</td>
                        <td class="text-gray-900 p-3 border border-gray-300">{{$item->name}}</td>
                        <td class="text-gray-900 p-3 border border-gray-300">{{$item->email}}</td>
                        <td class="text-gray-900 p-3 border border-gray-300">
                            {{$item->getRoleNames()->first()}}
                        </td>
                        <td class="text-gray-900 p-3 border border-gray-300">
                            <span
                                class="px-3 py-1 rounded-full font-semibold {{ $item->estado == 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{$item->estado == 1 ? 'Activo' : 'Inactivo'}}</span>
                        </td>
                        <td class="text-gray-900 p-3 border border-gray-300">
                            <div class="flex justify-between items-center gap-2">
                                <div>
                                    <button title="Opciones" class="text-gray-600 hover:text-gray-900 relative" onclick="document.getElementById('menu-{{$item->id}}').classList.toggle('hidden')">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div id="menu-{{$item->id}}" class="hidden absolute bg-white border border-gray-300 rounded shadow-lg mt-1 z-10">
                                        <!-----Editar usuarios--->
                                        @can('editar-user')
                                        <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" href="{{route('users.edit',['user'=>$item])}}">Editar</a>
                                        @endcan
                                    </div>
                                </div>
                                <div class="border-l border-gray-300 h-4"></div>
                                <div>
                                    <!------Eliminar user---->
                                    @can('eliminar-user')
                                    @if ($item->estado == 1)
                                    <button title="Eliminar" onclick="document.getElementById('confirmModal-{{$item->id}}').classList.remove('hidden')" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @else
                                    <button title="Restaurar" onclick="document.getElementById('confirmModal-{{$item->id}}').classList.remove('hidden')" class="text-green-600 hover:text-green-900">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                    @endif
                                    @endcan
                                </div>
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
                                {{ $item->estado == 1 ? '¿Seguro que quieres desactivar el usuario?' : '¿Seguro que quieres restaurar el usuario?' }}
                            </div>
                            <div class="bg-gray-100 border-t border-gray-300 px-6 py-4 flex justify-end gap-2">
                                <button type="button" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-6 rounded-lg transition-colors" onclick="document.getElementById('confirmModal-{{$item->id}}').classList.add('hidden')">Cerrar</button>
                                <form action="{{ route('users.destroy',['user'=>$item->id]) }}" method="post" class="inline">
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
