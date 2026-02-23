@extends('layouts.app')

@section('title', 'cajas')

@push('css-datatable')
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" type="text/css">
@endpush

@push('css')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')

    <div class="w-full px-4 md:px-6 py-4">
        <h1 class="text-3xl font-bold text-center text-gray-900 mb-6">Cajas</h1>

        <x-breadcrumb.template>
            <x-breadcrumb.item :href="route('admin.dashboard.index')" content="Inicio" />
            <x-breadcrumb.item active='true' content="Cajas" />
        </x-breadcrumb.template>

        @can('aperturar-caja')
            <div class="mb-6">
                <a href="{{route('cajas.create')}}">
                    <button type="button"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors">Aperturar
                        caja</button>
                </a>
            </div>
        @endcan


        <div class="bg-white rounded-lg shadow">
            <div class="p-4 border-b border-gray-200 flex items-center gap-2">
                <i class="fas fa-table"></i>
                <span class="font-semibold text-gray-900">Tabla cajas</span>
            </div>
            <div class="p-6">
                <table id="datatablesSimple" class="w-full border-collapse">
                    <thead>
                        <tr class="bg-blue-600 text-white">
                            <th class="border border-gray-300 p-3 text-left font-semibold">Nombre</th>
                            <th class="border border-gray-300 p-3 text-left font-semibold">Apertura</th>
                            <th class="border border-gray-300 p-3 text-left font-semibold">Cierre</th>
                            <th class="border border-gray-300 p-3 text-left font-semibold">Saldo inicial</th>
                            <th class="border border-gray-300 p-3 text-left font-semibold">Saldo final</th>
                            <th class="border border-gray-300 p-3 text-center font-semibold">Estado</th>
                            <th class="border border-gray-300 p-3 text-center font-semibold">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cajas as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="border border-gray-300 p-3">
                                    {{$item->nombre}}
                                </td>
                                <td class="border border-gray-300 p-3">
                                    <p class="font-semibold text-gray-900 mb-1">
                                        <span class="inline-block mx-1"><i class="fa-solid fa-calendar-days"></i></span>
                                        {{$item->fecha_apertura}}
                                    </p>
                                    <p class="font-semibold text-gray-900 mb-0"><span class="inline-block mx-1"><i
                                                class="fa-solid fa-clock"></i></span>
                                        {{$item->hora_apertura}}
                                    </p>
                                </td>
                                <td class="border border-gray-300 p-3">
                                    @if ($item->fecha_cierre)
                                        <p class="font-semibold text-gray-900 mb-1">
                                            <span class="inline-block mx-1"><i class="fa-solid fa-calendar-days"></i></span>
                                            {{$item->fecha_cierre}}
                                        </p>
                                        <p class="font-semibold text-gray-900 mb-0"><span class="inline-block mx-1"><i
                                                    class="fa-solid fa-clock"></i></span>
                                            {{$item->hora_cierre}}
                                        </p>
                                    @endif
                                </td>
                                <td class="border border-gray-300 p-3">
                                    {{$item->monto_inicial}}
                                </td>
                                <td class="border border-gray-300 p-3">
                                    {{$item->saldo_final}}
                                </td>
                                <td class="border border-gray-300 p-3 text-center">
                                    <span
                                        class="inline-block px-3 py-1 rounded-full text-sm font-semibold {{ $item->estado == 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{$item->estado == 1 ? 'aperturada' : 'cerrada'}}</span>
                                </td>
                                <td class="border border-gray-300 p-3">
                                    <div class="flex gap-2 items-center justify-center flex-wrap">
                                        @can('ver-movimiento')
                                            <form action="{{route('movimientos.index')}}" method="get">
                                                <input type="hidden" name="caja_id" value="{{$item->id}}">
                                                <button type="submit"
                                                    class="bg-green-600 hover:bg-green-700 text-white font-semibold py-1 px-3 rounded text-sm transition-colors">
                                                    Ver
                                                </button>
                                            </form>
                                        @endcan

                                        @can('cerrar-caja')
                                            @if ($item->estado == 1 || $item->estado == 'ABIERTA')
                                                <a href="{{ route('caja.mostrar-cierre', ['id' => $item->id]) }}"
                                                    class="bg-red-600 hover:bg-red-700 text-white font-semibold py-1 px-3 rounded text-sm transition-colors">
                                                    Cierre Profesional
                                                </a>
                                            @endif
                                        @endcan

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
    <script src="{{ asset('js/datatables-simple-demo.js') }}"></script>
@endpush
