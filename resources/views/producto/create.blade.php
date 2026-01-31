@extends('layouts.app')

@section('title','Crear Producto')

@push('css')
<style>
    #descripcion {
        resize: none;
    }
</style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
@endpush

@section('content')
<div class="w-full px-4 md:px-6 py-4">
    <h1 class="text-3xl font-bold text-center text-gray-900 mb-6">Crear Producto</h1>

    <x-breadcrumb.template>
        <x-breadcrumb.item :href="route('panel')" content="Inicio" />
        <x-breadcrumb.item :href="route('productos.index')" content="Productos" />
        <x-breadcrumb.item active='true' content="Crear producto" />
    </x-breadcrumb.template>

    <div class="bg-white rounded-lg shadow">
        <form action="{{ route('productos.store') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="bg-gray-50 px-6 py-4 space-y-6">

                <div class="grid grid-cols-1 gap-6">

                    <!---Nombre---->
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">Nombre:</label>
                        <input type="text" name="nombre" id="nombre" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" value="{{old('nombre')}}">
                        @error('nombre')
                        <small class="text-red-600">{{'*'.$message}}</small>
                        @enderror
                    </div>

                    <!---Descripción---->
                    <div>
                        <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-2">Descripción:</label>
                        <textarea name="descripcion" id="descripcion" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 resize-none">{{old('descripcion')}}</textarea>
                        @error('descripcion')
                        <small class="text-red-600">{{'*'.$message}}</small>
                        @enderror
                    </div>

                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div>

                        <div class="space-y-6">

                            <!---Imagen---->
                            <div>
                                <label for="img_path" class="block text-sm font-medium text-gray-700 mb-2">Imagen:</label>
                                <input type="file" name="img_path" id="img_path" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" accept="image/*">
                                @error('img_path')
                                <small class="text-red-600">{{'*'.$message}}</small>
                                @enderror
                            </div>

                            <!----Codigo---->
                            <div>
                                <label for="codigo" class="block text-sm font-medium text-gray-700 mb-2">Código:</label>
                                <input type="text" name="codigo" id="codigo" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" value="{{old('codigo')}}">
                                @error('codigo')
                                <small class="text-red-600">{{'*'.$message}}</small>
                                @enderror
                            </div>

                            <!---Marca---->
                            <div>
                                <label for="marca_id" class="block text-sm font-medium text-gray-700 mb-2">Marca:</label>
                                <select data-size="4"
                                    title="Seleccione una marca"
                                    data-live-search="true"
                                    name="marca_id"
                                    id="marca_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 selectpicker show-tick">
                                    <option value="">No tiene marca</option>
                                    @foreach ($marcas as $item)
                                    <option value="{{$item->id}}" {{ old('marca_id') == $item->id ? 'selected' : '' }}>{{$item->nombre}}</option>
                                    @endforeach
                                </select>
                                @error('marca_id')
                                <small class="text-red-600">{{'*'.$message}}</small>
                                @enderror
                            </div>

                            <!---Presentaciones---->
                            <div>
                                <label for="presentacione_id" class="block text-sm font-medium text-gray-700 mb-2">Presentación:</label>
                                <select data-size="4"
                                    title="Seleccione una presentación"
                                    data-live-search="true"
                                    name="presentacione_id"
                                    id="presentacione_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 selectpicker show-tick">
                                    @foreach ($presentaciones as $item)
                                    <option value="{{$item->id}}" {{ old('presentacione_id') == $item->id ? 'selected' : '' }}>
                                        {{$item->nombre}}
                                    </option>
                                    @endforeach
                                </select>
                                @error('presentacione_id')
                                <small class="text-red-600">{{'*'.$message}}</small>
                                @enderror
                            </div>

                            <!---Categorías---->
                            <div>
                                <label for="categoria_id" class="block text-sm font-medium text-gray-700 mb-2">Categoría:</label>
                                <select data-size="4"
                                    title="Seleccione la categoría"
                                    data-live-search="true"
                                    name="categoria_id"
                                    id="categoria_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 selectpicker show-tick">
                                    <option value="">No tiene categoría</option>
                                    @foreach ($categorias as $item)
                                    <option value="{{$item->id}}" {{ old('categoria_id') == $item->id ? 'selected' : '' }}>
                                        {{$item->nombre}}
                                    </option>
                                    @endforeach
                                </select>
                                @error('categoria_id')
                                <small class="text-red-600">{{'*'.$message}}</small>
                                @enderror
                            </div>
                        </div>

                    </div>
                    <div>
                        <p class="text-sm text-gray-700 mb-3 font-medium">Imagen del producto:</p>

                        <img id="img-default"
                            class="w-full"
                            src="{{ asset('assets/img/paisaje.png') }}"
                            alt="Imagen por defecto">

                        <img src="" alt="Ha cargado un archivo no compatible"
                            id="img-preview"
                            class="w-full border-2 border-gray-300 rounded" style="display: none;">

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
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
<script>
    const inputImagen = document.getElementById('img_path');
    const imagenPreview = document.getElementById('img-preview');
    const imagenDefault = document.getElementById('img-default');

    inputImagen.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                imagenPreview.src = e.target.result;
                imagenPreview.style.display = 'block';
                imagenDefault.style.display = 'none';
            }
            reader.readAsDataURL(this.files[0]);
        }
    });
</script>
@endpush
