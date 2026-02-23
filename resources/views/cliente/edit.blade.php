@extends('layouts.app')

@section('title','Editar cliente')

@push('css')

@endpush

@section('content')
<div class="w-full px-4 md:px-6 py-4">
    <h1 class="text-3xl font-bold text-center text-gray-900 mb-6">Editar Cliente</h1>

    <x-breadcrumb.template>
        <x-breadcrumb.item :href="route('admin.dashboard.index')" content="Inicio" />
        <x-breadcrumb.item :href="route('clientes.index')" content="Clientes" />
        <x-breadcrumb.item active='true' content="Editar cliente" />
    </x-breadcrumb.template>

    <x-forms.template :action="route('clientes.update',['cliente'=>$cliente])" method='post' patch='true'>
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
            <p class="text-gray-900">Tipo de cliente: <span class="font-bold">
                    {{ strtoupper($cliente->persona->tipo->value)}}
                </span></p>
        </div>

        <div class="space-y-6">

            <!-------Razón social------->
            <div>
                <label id="label-juridica" for="razon_social" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ $cliente->persona->tipo->value == 'NATURAL' ? 'Nombres y apellidos:' : 'Nombre de la empresa:'}} <span class="text-red-600">*</span>
                </label>
                <input required
                    type="text"
                    name="razon_social"
                    id="razon_social"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    value="{{old('razon_social',$cliente->persona->razon_social)}}">
                @error('razon_social')
                <small class="text-red-600">{{'*'.$message}}</small>
                @enderror
            </div>

            <!------Dirección---->
            <div>
                <label for="direccion" class="block text-sm font-medium text-gray-700 mb-2">Dirección:</label>
                <input type="text"
                    name="direccion"
                    id="direccion"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    value="{{old('direccion',$cliente->persona->direccion)}}">
                @error('direccion')
                <small class="text-red-600">{{'*'.$message}}</small>
                @enderror
            </div>

            <!------Email y Telefono---->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <x-forms.input id="email"
                        type='email'
                        labelText='Correo eléctronico'
                        :defaultValue='$cliente->persona->email' />
                </div>

                <div>
                    <x-forms.input id="telefono"
                        type='number'
                        :defaultValue='$cliente->persona->telefono' />
                </div>
            </div>

            <!--------------Documento------->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="documento_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Tipo de documento: <span class="text-red-600">*</span></label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" name="documento_id" id="documento_id">
                        @foreach ($documentos as $item)
                        <option value="{{ $item->id }}"
                            {{ old('documento_id', $cliente->persona->documento_id) == $item->id ? 'selected' : '' }}>
                            {{ $item->nombre }}
                        </option>
                        @endforeach
                    </select>
                    @error('documento_id')
                    <small class="text-red-600">{{'*'.$message}}</small>
                    @enderror
                </div>

                <div>
                    <label for="numero_documento" class="block text-sm font-medium text-gray-700 mb-2">Numero de documento: <span class="text-red-600">*</span></label>
                    <input required
                        type="text"
                        name="numero_documento"
                        id="numero_documento"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        value="{{old('numero_documento',$cliente->persona->numero_documento)}}">
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

@endpush
