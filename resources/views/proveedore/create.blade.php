@extends('layouts.app')

@section('title','Crear proveedor')

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
    <h1 class="text-3xl font-bold text-center text-gray-900 mb-6">Crear Proveedor</h1>

    <x-breadcrumb.template>
        <x-breadcrumb.item :href="route('admin.dashboard.index')" content="Inicio" />
        <x-breadcrumb.item :href="route('proveedores.index')" content="Proveedor" />
        <x-breadcrumb.item active='true' content="Crear proveedor" />
    </x-breadcrumb.template>

    <div class="bg-white rounded-lg shadow">
        <form action="{{ route('proveedores.store') }}" method="post">
            @csrf
            <div class="px-6 py-4 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!----Tipo de persona----->
                    <div>
                        <label for="tipo" class="block text-sm font-medium text-gray-700 mb-2">Tipo de proveedor:</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" name="tipo" id="tipo">
                            <option value="" selected disabled>Seleccione una opción</option>
                            @foreach ($optionsTipoPersona as $item)
                            <option value="{{$item->value}}" {{ old('tipo') == $item->value ? 'selected' : '' }}>{{$item->name}}</option>
                            @endforeach
                        </select>
                        @error('tipo')
                        <small class="text-red-600">{{'*'.$message}}</small>
                        @enderror
                    </div>

                    <div></div>

                    <!-------Razón social------->
                    <div class="col-span-1 md:col-span-2" id="box-razon-social" style="display: none;">
                        <label id="label-natural" for="razon_social" class="block text-sm font-medium text-gray-700 mb-2">Nombres y apellidos: <span class="text-red-600">*</span></label>
                        <label id="label-juridica" for="razon_social" class="block text-sm font-medium text-gray-700 mb-2">Nombre de la empresa: <span class="text-red-600">*</span></label>

                        <input required type="text" name="razon_social" id="razon_social" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" value="{{old('razon_social')}}">

                        @error('razon_social')
                        <small class="text-red-600">{{'*'.$message}}</small>
                        @enderror
                    </div>

                    <!------Dirección---->
                    <div class="col-span-1 md:col-span-2">
                        <label for="direccion" class="block text-sm font-medium text-gray-700 mb-2">Dirección:</label>
                        <input type="text" name="direccion" id="direccion" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" value="{{old('direccion')}}">
                        @error('direccion')
                        <small class="text-red-600">{{'*'.$message}}</small>
                        @enderror
                    </div>

                    <!------Email---->
                    <div>
                        <x-forms.input id="email" type='email' labelText='Correo eléctronico' />
                    </div>

                    <!------Telefono---->
                    <div>
                        <x-forms.input id="telefono" type='number' />
                    </div>


                    <!--------------Documento------->
                    <div>
                        <label for="documento_id" class="block text-sm font-medium text-gray-700 mb-2">Tipo de documento:</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" name="documento_id" id="documento_id">
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
                        <input required type="text" name="numero_documento" id="numero_documento" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" value="{{old('numero_documento')}}">
                        @error('numero_documento')
                        <small class="text-red-600">{{'*'.$message}}</small>
                        @enderror
                    </div>
                </div>

            </div>
            <div class="bg-gray-100 border-t border-gray-300 px-6 py-4 text-center">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors">Guardar</button>
            </div>
        </form>
    </div>


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
