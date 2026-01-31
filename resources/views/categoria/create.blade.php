@extends('layouts.app')

@section('title','Crear categoría')

@push('css')
<style>
    #descripcion {
        resize: none;
    }
</style>
@endpush

@section('content')
<div class="w-full px-4 md:px-6 py-4">
    <h1 class="text-3xl font-bold text-center text-gray-900 mb-6">Crear Categoría</h1>

    <x-breadcrumb.template>
        <x-breadcrumb.item :href="route('panel')" content="Inicio" />
        <x-breadcrumb.item :href="route('categorias.index')" content="Categorías" />
        <x-breadcrumb.item active='true' content="Crear categoría" />
    </x-breadcrumb.template>

    <x-forms.template :action="route('categorias.store')" method='post'>

        <div class="space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <x-forms.input id="nombre" required='true' />
                </div>
            </div>

            <div>
                <x-forms.textarea id="descripcion"/>
            </div>
        </div>

        <x-slot name='footer'>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors">Guardar</button>
        </x-slot>

    </x-forms.template>


</div>
@endsection

@push('js')

@endpush
