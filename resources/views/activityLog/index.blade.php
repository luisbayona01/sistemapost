@extends('layouts.app')

@section('title','Registro de actividad')

@push('css-datatable')
<link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" type="text/css">
@endpush

@push('css')
@endpush

@section('content')

<div class="w-full px-4 md:px-6 py-4">
    <h1 class="text-3xl font-bold text-center text-gray-900 mb-6">Registro de actividad</h1>

    <x-breadcrumb.template>
        <x-breadcrumb.item :href="route('panel')" content="Inicio" />
        <x-breadcrumb.item active='true' content="Registro de actividad" />
    </x-breadcrumb.template>

    <div class="bg-white rounded-lg shadow">
        <div class="bg-gray-100 border-b border-gray-300 px-6 py-3 font-semibold text-gray-900 flex items-center">
            <i class="fas fa-table mr-2"></i>
            Tabla Registro de actividad
        </div>
        <div class="px-6 py-4 overflow-x-auto">
            <table id="datatablesSimple" class="w-full border-collapse">
                <thead class="bg-gray-100 border-b border-gray-300">
                    <tr>
                        <th class="text-left text-gray-900 font-semibold p-3 border border-gray-300">Usuario</th>
                        <th class="text-left text-gray-900 font-semibold p-3 border border-gray-300">Acción</th>
                        <th class="text-left text-gray-900 font-semibold p-3 border border-gray-300">Módulo</th>
                        <th class="text-left text-gray-900 font-semibold p-3 border border-gray-300">Ejecutado el</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($activityLogs as $log)
                    <tr class="hover:bg-gray-50">
                        <td class="text-gray-900 p-3 border border-gray-300">
                            {{$log->user->name}}
                        </td>
                        <td class="text-gray-900 p-3 border border-gray-300">
                            {{$log->action}}
                        </td>
                        <td class="text-gray-900 p-3 border border-gray-300">
                           {{$log->module}}
                        </td>
                        <td class="text-gray-900 p-3 border border-gray-300">
                            {{$log->created_at_formatted}}
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
