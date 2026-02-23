@extends('layouts.app')

@section('title','Editar presentación')

@push('css')
<style>
    #descripcion {
        resize: none;
    }
</style>
@endpush

@section('content')
<div class="w-full px-4 md:px-6 py-4">
    <h1 class="text-3xl font-bold text-center text-gray-900 mb-6">Editar Presentación</h1>

    <x-breadcrumb.template>
        <x-breadcrumb.item :href="route('admin.dashboard.index')" content="Inicio" />
        <x-breadcrumb.item :href="route('presentaciones.index')" content="Presentaciones" />
        <x-breadcrumb.item active='true' content="Editar presentación" />
    </x-breadcrumb.template>

    <x-forms.template :action="route('presentaciones.update',['presentacione'=>$presentacione])" method='post' patch='true'>

        <div class="space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div>
                    <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">Nombre: <span class="text-red-600">*</span></label>
                    <input type="text" name="nombre" id="nombre" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="{{old('nombre',$presentacione->caracteristica->nombre)}}">
                    @error('nombre')
                    <small class="text-red-600">{{'*'.$message}}</small>
                    @enderror
                </div>

                <div>
                    <x-forms.input id="sigla" required='true' :defaultValue='$presentacione->sigla' />
                </div>
            </div>

            <div>
                <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-2">Descripción:</label>
                <textarea name="descripcion" id="descripcion" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" style="resize: none;">{{old('descripcion',$presentacione->caracteristica->descripcion)}}</textarea>
                @error('descripcion')
                <small class="text-red-600">{{'*'.$message}}</small>
                @enderror
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
