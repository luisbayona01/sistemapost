@extends('layouts.app')

@section('title','Editar categoría')

@push('css')
<style>
    #descripcion {
        resize: none;
    }
</style>
@endpush

@section('content')
<div class="w-full px-4 md:px-6 py-4">
    <h1 class="text-3xl font-bold text-center text-gray-900 mb-6">Editar Categoría</h1>

    <x-breadcrumb.template>
        <x-breadcrumb.item :href="route('panel')" content="Inicio" />
        <x-breadcrumb.item :href="route('categorias.index')" content="Categorías" />
        <x-breadcrumb.item active='true' content="Editar categoría" />
    </x-breadcrumb.template>

    <x-forms.template :action="route('categorias.update',['categoria'=>$categoria])" method='post' patch='true'>

        <div class="space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <x-forms.input id="nombre"
                        :defaultValue='$categoria->caracteristica->nombre'
                        required='true' />
                </div>
            </div>

            <div>
                <x-forms.textarea id="descripcion"
                    :defaultValue='$categoria->caracteristica->descripcion' />
            </div>

        </div>

        <x-slot name='footer'>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors">Actualizar</button>
            <button type="reset" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-6 rounded-lg transition-colors ml-2">Reiniciar</button>
        </x-slot>


    </x-forms.template>

</div>
@endsection

@push('js')

@endpush
