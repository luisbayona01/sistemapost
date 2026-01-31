@extends('layouts.app')

@section('title','Editar proveedor')

@push('css')

@endpush

@section('content')
<div class="w-full px-4 md:px-6 py-4">
    <h1 class="text-3xl font-bold text-center text-gray-900 mb-6">Editar Proveedor</h1>

    <x-breadcrumb.template>
        <x-breadcrumb.item :href="route('panel')" content="Inicio" />
        <x-breadcrumb.item :href="route('proveedores.index')" content="Proveedores" />
        <x-breadcrumb.item active='true' content="Editar proveedor" />
    </x-breadcrumb.template>

    <div class="bg-white rounded-lg shadow">
        <form action="{{ route('proveedores.update',['proveedore'=>$proveedore]) }}" method="post">
            @method('PATCH')
            @csrf
            <div class="bg-blue-50 border-l-4 border-blue-500 px-6 py-4 mb-6">
                <p>Tipo de proveedor: <span class="font-bold">
                        {{ strtoupper($proveedore->persona->tipo->value)}}</span></p>
            </div>
            <div class="px-6 py-4 space-y-6">

                <div class="grid grid-cols-1 gap-6">

                    <!-------Razón social------->
                    <div>
                        <label for="razon_social" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ $proveedore->persona->tipo->value == 'NATURAL' ? 'Nombres y apellidos:' : 'Nombre de la empresa:'}}
                            <span class="text-red-600">*</span>
                        </label>
                        <input required
                            type="text"
                            name="razon_social"
                            id="razon_social"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                            value="{{old('razon_social',$proveedore->persona->razon_social)}}">
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
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                            value="{{old('direccion',$proveedore->persona->direccion)}}">
                        @error('direccion')
                        <small class="text-red-600">{{'*'.$message}}</small>
                        @enderror
                    </div>

                    <!------Email---->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-forms.input id="email"
                                type='email'
                                labelText='Correo eléctronico'
                                :defaultValue='$proveedore->persona->email' />
                        </div>

                        <!------Telefono---->
                        <div>
                            <x-forms.input id="telefono"
                                type='number'
                                :defaultValue='$proveedore->persona->telefono' />
                        </div>
                    </div>


                    <!--------------Documento------->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="documento_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Tipo de documento:</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" name="documento_id" id="documento_id">
                                @foreach ($documentos as $item)
                                <option value="{{ $item->id }}"
                                    {{ old('documento_id', $proveedore->persona->documento_id) == $item->id ? 'selected' : '' }}>
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
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                value="{{old('numero_documento',$proveedore->persona->numero_documento)}}">
                            @error('numero_documento')
                            <small class="text-red-600">{{'*'.$message}}</small>
                            @enderror
                        </div>
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

@endpush
