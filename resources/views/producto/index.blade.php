@extends('layouts.app')

@section('title', 'Catálogo de Dulcería')

@push('css-datatable')
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" type="text/css">
@endpush

@push('css')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')

    <div class="w-full px-4 md:px-6 py-4">
        <h1 class="text-3xl font-black text-slate-900 mb-6 uppercase tracking-tighter">Catálogo de Dulcería & Snacks</h1>

        <x-breadcrumb.template class="mb-6">
            <x-breadcrumb.item :href="route('admin.dashboard.index')" content="Panel" />
            <x-breadcrumb.item active='true' content="Dulcería" />
        </x-breadcrumb.template>

        @can('crear-producto')
            <div class="mb-6">
                <a href="{{route('productos.create')}}">
                    <button type="button"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors">Añadir
                        nuevo registro</button>
                </a>
            </div>
        @endcan

        <div class="bg-white rounded-lg shadow">
            <div class="bg-gray-100 border-b border-gray-300 px-6 py-3 font-semibold text-gray-900 flex items-center">
                <i class="fas fa-table mr-2"></i>
                Tabla productos
            </div>
            <div class="px-6 py-4">
                <table id="datatablesSimple" class="w-full border-collapse">
                    <thead class="bg-gray-100 border-b border-gray-300">
                        <tr>
                            <th class="text-left text-gray-900 font-semibold p-3 border border-gray-300">Producto</th>
                            <th class="text-left text-gray-900 font-semibold p-3 border border-gray-300">Costo</th>
                            <th class="text-left text-gray-900 font-semibold p-3 border border-gray-300">Ganancia</th>
                            <th class="text-left text-gray-900 font-semibold p-3 border border-gray-300">Margen %</th>
                            <th class="text-left text-gray-900 font-semibold p-3 border border-gray-300">Estado</th>
                            <th class="text-left text-gray-900 font-semibold p-3 border border-gray-300">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($productos as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="text-gray-900 p-3 border border-gray-300">
                                    {{$item->nombreCompleto}}
                                </td>
                                <td class="text-gray-900 p-3 border border-gray-300">
                                    <div class="font-bold text-blue-700">${{ number_format($item->precio, 0) }}</div>
                                </td>
                                <td class="text-gray-900 p-3 border border-gray-300 text-sm">
                                    ${{ number_format($item->costo_total_unitario, 0) }}
                                </td>
                                <td class="text-gray-900 p-3 border border-gray-300 text-sm">
                                    <span class="{{ $item->margen_ganancia_absoluta > 0 ? 'text-green-600' : 'text-red-600' }}">
                                        ${{ number_format($item->margen_ganancia_absoluta, 0) }}
                                    </span>
                                </td>
                                <td class="text-gray-900 p-3 border border-gray-300">
                                    @php
                                        $margen = $item->margen_ganancia_porcentual ?? 0;
                                        $badgeColor = $margen >= 35 ? 'bg-green-100 text-green-800' : ($margen >= 20 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800');
                                    @endphp
                                    <span class="inline-block px-2 py-1 rounded text-xs font-bold {{ $badgeColor }}">
                                        {{ number_format($margen, 1) }}%
                                    </span>
                                </td>
                                <td class="text-gray-900 p-3 border border-gray-300">
                                    <span
                                        class="inline-block px-3 py-1 rounded-full text-sm font-semibold {{ $item->estado ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $item->estado ? 'Activo' : 'Inactivo'}}</span>
                                </td>
                                <td class="text-gray-900 p-3 border border-gray-300">
                                    <div class="flex justify-around items-center">
                                        <div class="relative group">
                                            <button title="Opciones" class="text-gray-600 hover:text-gray-900 p-2">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <div
                                                class="hidden group-hover:block absolute right-0 z-10 bg-white border border-gray-300 rounded-lg shadow-lg min-w-max">
                                                <!-----Editar Producto--->
                                                @can('editar-producto')
                                                <a class="block px-4 py-2 hover:bg-gray-100 text-gray-900 text-sm"
                                                    href="{{route('productos.edit',['producto' => $item])}}">
                                                    Editar
                                                </a>

                                                <form action="{{ route('productos.toggleStatus', $item) }}" method="POST"
                                                    class="block">
                                                    @csrf
                                                    <button type="submit"
                                                        class="w-full text-left px-4 py-2 hover:bg-gray-100 text-gray-900 text-sm">
                                                        {{ $item->estado ? 'Desactivar' : 'Activar' }}
                                                    </button>
                                                </form>
                                                @endcan
                                                <!----Ver-producto--->
                                                @can('ver-producto')
                                                <button
                                                    class="block w-full text-left px-4 py-2 hover:bg-gray-100 text-gray-900 text-sm"
                                                    onclick="document.getElementById('verModal-{{$item->id}}').classList.remove('hidden')">
                                                    Ver
                                                </button>
                                                @endcan
                                            </div>
                                        </div>
                                        <div class="border-l border-gray-300" style="height: 24px;"></div>
                                        <div>
                                            <!------Inicializar producto---->
                                            @can('crear-inventario')
                                                <form action="{{route('inventario.create')}}" method="get" style="display: inline;">
                                                    <input type="hidden" name="producto_id" value="{{$item->id}}">
                                                    <button title="Inicializar" class="text-gray-600 hover:text-blue-600 p-2"
                                                        type="submit">
                                                        <i class="fa-solid fa-rotate"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <!-- Modal -->
                            <div id="verModal-{{$item->id}}"
                                class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                                <div class="bg-white rounded-lg shadow-lg max-w-2xl w-full mx-4 max-h-96 overflow-y-auto">
                                    <div class="bg-gray-100 border-b border-gray-300 px-6 py-4 font-semibold text-gray-900">
                                        Detalles del producto
                                    </div>
                                    <div class="px-6 py-4">
                                        <div class="grid grid-cols-1 gap-4">
                                            <div>
                                                <p><span class="font-bold">Descripción:
                                                    </span>{{$item->descripcion ?? 'No tiene'}}</p>
                                            </div>
                                            <div>
                                                <p class="font-bold mb-2">Imagen:</p>
                                                <div>
                                                    @if (!empty($item->img_path))
                                                        <img src="{{ asset($item->img_path) }}" alt="{{ $item->nombre }}"
                                                            class="w-full border-4 border-gray-300 rounded">
                                                    @else
                                                        <p class="text-gray-600">Sin imagen</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bg-gray-100 border-t border-gray-300 px-6 py-4 flex justify-end">
                                        <button type="button"
                                            class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded-lg transition-colors"
                                            onclick="document.getElementById('verModal-{{$item->id}}').classList.add('hidden')">Cerrar</button>
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
