@extends('layouts.app')

@section('title','Inicializar producto')

@push('css')
@endpush

@section('content')
<div class="w-full px-4 md:px-6 py-4">
    <h1 class="text-3xl font-bold text-center text-gray-900 mb-6">Inicializar Producto</h1>

    <x-breadcrumb.template>
        <x-breadcrumb.item :href="route('panel')" content="Inicio" />
        <x-breadcrumb.item :href="route('productos.index')" content="Productos" />
        <x-breadcrumb.item active='true' content="Inicializar producto" />
    </x-breadcrumb.template>

    <div class="mb-6">
        <button type="button"
            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors"
            onclick="document.getElementById('verPlanoModal').classList.remove('hidden')">
            Ver plano
        </button>
    </div>

    <x-forms.template :action="route('inventario.store')" method='post'>

        <x-slot name='header'>
            <p>Producto: <span class='font-bold'>{{$producto->nombre_completo}}</span></p>
        </x-slot>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-----Producto id---->
            <input type="hidden" name="producto_id" value="{{$producto->id}}">

            <!---Ubicaciones-->
            <div>
                <label for="ubicacione_id" class="block text-sm font-medium text-gray-700 mb-2">Seleccione una ubicación:</label>
                <select name="ubicacione_id"
                    id="ubicaciones_id"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    @foreach ($ubicaciones as $item)
                    <option value="{{$item->id}}" {{ old('ubicacione_id') == $item->id ? 'selected' : '' }}>
                        {{$item->nombre}}
                    </option>
                    @endforeach
                </select>
                @error('ubicacione_id')
                <small class="text-red-600">{{'*'.$message}}</small>
                @enderror
            </div>

            <!---Cantidad--->
            <div>
                <x-forms.input id="cantidad" required='true' type='number' />
            </div>

            <!-----Fecha de vencimiento----->
            <div>
                <x-forms.input id="fecha_vencimiento" type='date' labelText='Fecha de Vencimiento' />
            </div>

              <!-----Costo Unitario----->
              <div>
                <x-forms.input id="costo_unitario" type='number' labelText='Costo unitario' required='true'/>
            </div>
        </div>

        <x-slot name='footer'>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors">Inicializar</button>
        </x-slot>

    </x-forms.template>


    <!-- Modal -->
    <div id="verPlanoModal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 max-h-96 overflow-y-auto">
        <div class="bg-white rounded-lg shadow-lg max-w-4xl w-full mx-4">
            <div class="bg-gray-100 border-b border-gray-300 px-6 py-4 font-semibold text-gray-900">
                Plano de Ubicaciones
            </div>
            <div class="px-6 py-4 text-center">
                <img src="{{ asset('assets/img/plano.png')}}" alt="Plano de ubicaciones"
                    class="w-full border-2 border-gray-300 rounded">
            </div>
            <div class="bg-gray-100 border-t border-gray-300 px-6 py-4 flex justify-end">
                <button type="button" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-6 rounded-lg transition-colors" onclick="document.getElementById('verPlanoModal').classList.add('hidden')">Cerrar</button>
            </div>
        </div>
    </div>

</div>
@endsection

@push('js')

@endpush
