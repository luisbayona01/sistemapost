@extends('layouts.app')

@section('title','Crear cliente')

@push('css')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<style>
    #box-razon-social {
        display: none;
    }
</style>
@endpush

@section('content')
<div class="w-full px-4 md:px-6 py-4">
    <h1 class="text-3xl font-bold text-center text-gray-900 mb-6">Crear Cliente</h1>

    <x-breadcrumb.template>
        <x-breadcrumb.item :href="route('admin.dashboard.index')" content="Inicio" />
        <x-breadcrumb.item :href="route('clientes.index')" content="Clientes" />
        <x-breadcrumb.item active='true' content="Crear cliente" />
    </x-breadcrumb.template>

    <x-forms.template :action="route('clientes.store')" method='post'>

        <div class="space-y-6">

            <!----Tipo de persona----->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="tipo" class="block text-sm font-medium text-gray-700 mb-2">Tipo de cliente: <span class="text-red-600">*</span></label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" name="tipo" id="tipo">
                        <option value="" selected disabled>Seleccione una opción</option>
                        @foreach ($optionsTipoPersona as $item)
                        <option value="{{$item->value}}" {{ old('tipo') == $item->value ? 'selected' : '' }}>{{$item->name}}</option>
                        @endforeach
                    </select>
                    @error('tipo')
                    <small class="text-red-600">{{'*'.$message}}</small>
                    @enderror
                </div>
            </div>

            <!-------Razón social------->
            <div id="box-razon-social" style="display: none;">
                <label id="label-natural" for="razon_social" class="block text-sm font-medium text-gray-700 mb-2">Nombres y apellidos: <span class="text-red-600">*</span></label>
                <label id="label-juridica" for="razon_social" class="block text-sm font-medium text-gray-700 mb-2">Nombre de la empresa: <span class="text-red-600">*</span></label>

                <input required type="text" name="razon_social" id="razon_social" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="{{old('razon_social')}}">

                @error('razon_social')
                <small class="text-red-600">{{'*'.$message}}</small>
                @enderror
            </div>

            <!------Dirección---->
            <div>
                <label for="direccion" class="block text-sm font-medium text-gray-700 mb-2">Dirección:</label>
                <input type="text" name="direccion" id="direccion" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="{{old('direccion')}}">
                @error('direccion')
                <small class="text-red-600">{{'*'.$message}}</small>
                @enderror
            </div>

            <!------Email y Telefono---->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <x-forms.input id="email" type='email' labelText='Correo eléctronico' />
                </div>

                <div>
                    <x-forms.input id="telefono" type='number' />
                </div>
            </div>

            <!--------------Documento------->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="documento_id" class="block text-sm font-medium text-gray-700 mb-2">Tipo de documento: <span class="text-red-600">*</span></label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" name="documento_id" id="documento_id">
                        <option value="" selected disabled>Seleccione una opción</option>
                        @foreach ($documentos as $item)
                        <option value="{{$item->id}}" {{ old('documento_id') == $item->id ? 'selected' : '' }}>{{$item->nombre}}</option>
                        @endforeach
                    </select>
                    @error('documento_id')
                    <small class="text-red-600">{{'*'.$message}}</small>
                    @enderror
                </div>

                <div>
                    <label for="numero_documento" class="block text-sm font-medium text-gray-700 mb-2">Numero de documento: <span class="text-red-600">*</span></label>
                    <input required type="text" name="numero_documento" id="numero_documento" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="{{old('numero_documento')}}">
                    @error('numero_documento')
                    <small class="text-red-600">{{'*'.$message}}</small>
                    @enderror
                </div>
            </div>

        </div>

        <x-slot name='footer'>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors">Guardar</button>
        </x-slot>

    </x-forms.template>


</div>
@endsection

@push('js')
<script>
    $(document).ready(function() {
        $('#tipo').on('change', function() {
            let selectValue = $(this).val();
            //natural //juridica
            if (selectValue == 'NATURAL') {
                $('#label-juridica').hide();
                $('#label-natural').show();
            } else {
                $('#label-natural').hide();
                $('#label-juridica').show();
            }

            $('#box-razon-social').show();
        });
    });
</script>
@endpush
