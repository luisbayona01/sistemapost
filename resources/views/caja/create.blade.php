@extends('layouts.app')

@section('title','Crear caja')

@push('css')
@endpush

@section('content')
<div class="w-full px-4 md:px-6 py-4">
    <h1 class="text-3xl font-bold text-center text-gray-900 mb-6">Aperturar Caja</h1>

    <x-breadcrumb.template>
        <x-breadcrumb.item :href="route('admin.dashboard.index')" content="Inicio" />
        <x-breadcrumb.item :href="route('cajas.index')" content="Cajas" />
        <x-breadcrumb.item active='true' content="Aperturar caja" />
    </x-breadcrumb.template>

    <x-forms.template :action="route('cajas.store')" method='post'>

        <div class="space-y-6">

            <div>
                <x-forms.input id="monto_inicial" required='true' type='number'
                    labelText='Saldo inicial' />
            </div>

        </div>

        <x-slot name='footer'>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-8 rounded-lg transition-colors">Aperturar caja</button>
        </x-slot>

    </x-forms.template>


</div>
@endsection

@push('js')

@endpush
