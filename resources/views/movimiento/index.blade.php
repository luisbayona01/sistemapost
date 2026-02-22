@extends('layouts.app')

@section('title', 'movimientos')

@push('css-datatable')
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" type="text/css">
@endpush

@push('css')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')

    <div class="w-full px-4 md:px-6 py-4">
        <h1 class="text-3xl font-bold text-center text-gray-900 mb-6">Movimientos de caja</h1>

        <x-breadcrumb.template>
            <x-breadcrumb.item :href="route('admin.dashboard.index')" content="Inicio" />
            <x-breadcrumb.item :href="route('admin.cajas.index')" content="Cajas" />
            <x-breadcrumb.item active='true' content="Movimientos de caja" />
        </x-breadcrumb.template>

        <div class="bg-white rounded-lg shadow mb-6">
            <div class="bg-gray-100 border-b border-gray-300 px-6 py-3 font-semibold text-gray-900">
                {{$caja->nombre}}
            </div>
            <div class="px-6 py-4 space-y-2">
                <div class="text-sm text-gray-600">
                    Apertura: {{$caja->fecha_apertura}} - {{$caja->hora_apertura}}</div>
                @if ($caja->fecha_cierre)
                    <div class="text-sm text-gray-600">
                        Cierre: {{$caja->fecha_cierre}} - {{$caja->hora_cierre}}</div>
                @endif
                <div class="text-sm text-gray-600">
                    Saldo inicial: {{$caja->monto_inicial}}</div>
                @if ($caja->saldo_final)
                    <div class="text-sm text-gray-600">
                        Saldo final: {{$caja->saldo_final}}</div>
                @endif

                <hr class="my-4">
                @if ($caja->estado == 1)
                    @can('crear-venta')
                        <a href="{{route('ventas.create')}}">
                            <button type="button"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors mr-2">Nueva
                                venta</button>
                        </a>
                    @endcan

                    @can('crear-movimiento')
                        <a href="{{route('movimientos.create', ['caja_id' => $caja->id])}}">
                            <button type="button"
                                class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors mr-2">Nuevo
                                retiro</button>
                        </a>
                    @endcan

                    @can('cerrar-caja')
                        <button type="button"
                            class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors"
                            onclick="document.getElementById('confirmModal-{{$caja->id}}').classList.remove('hidden')">
                            Cerrar</button>
                    @endcan

                @endif


            </div>

        </div>

        <div class="bg-white rounded-lg shadow">
            <div class="bg-gray-100 border-b border-gray-300 px-6 py-3 font-semibold text-gray-900 flex items-center">
                <i class="fas fa-table mr-2"></i>
                Tabla movimientos
            </div>
            <div class="px-6 py-4">
                <table id="datatablesSimple" class="w-full border-collapse">
                    <thead class="bg-gray-100 border-b border-gray-300">
                        <tr>
                            <th class="text-left text-gray-900 font-semibold p-3 border border-gray-300">Tipo</th>
                            <th class="text-left text-gray-900 font-semibold p-3 border border-gray-300">Descripción</th>
                            <th class="text-left text-gray-900 font-semibold p-3 border border-gray-300">Monto</th>
                            <th class="text-left text-gray-900 font-semibold p-3 border border-gray-300">Método de pago</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($caja->movimientos as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="text-gray-900 p-3 border border-gray-300">
                                    {{$item->tipo->value}}
                                </td>
                                <td class="text-gray-900 p-3 border border-gray-300">
                                    {{$item->descripcion}}
                                </td>
                                <td class="text-gray-900 p-3 border border-gray-300">
                                    {{$item->monto}}
                                </td>
                                <td class="text-gray-900 p-3 border border-gray-300">
                                    {{$item->metodo_pago->value}}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>

        <!-- Modal para cerrar la caja-->
        <div id="confirmModal-{{$caja->id}}"
            class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg shadow-lg max-w-md w-full mx-4">
                <div
                    class="bg-gray-100 border-b border-gray-300 px-6 py-4 font-semibold text-gray-900 flex justify-between items-center">
                    <h1 class="text-lg">Mensaje de confirmación</h1>
                    <button type="button" class="text-gray-500 hover:text-gray-700"
                        onclick="document.getElementById('confirmModal-{{$caja->id}}').classList.add('hidden')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="px-6 py-4">
                    ¿Seguro que quieres cerrar la caja?
                </div>
                <div class="bg-gray-100 border-t border-gray-300 px-6 py-4 flex justify-end gap-2">
                    <button type="button"
                        class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded-lg transition-colors"
                        onclick="document.getElementById('confirmModal-{{$caja->id}}').classList.add('hidden')">
                        Cancelar</button>

                    <form action="{{route('cajas.destroy', ['caja' => $caja->id])}}" method="post" style="display: inline;">
                        @method('DELETE')
                        @csrf
                        <button type="submit"
                            class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors">
                            Confirmar</button>
                    </form>

                </div>
            </div>
        </div>

    </div>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" type="text/javascript"></script>
    <script src="{{ asset('js/datatables-simple-demo.js') }}"></script>
@endpush
