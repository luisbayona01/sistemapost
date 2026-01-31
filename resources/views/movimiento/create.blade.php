@extends('layouts.app')

@section('title','Crear movimiento')

@push('css')
@endpush

@section('content')
<div class="w-full px-4 md:px-6 py-4">
    <h1 class="text-3xl font-bold text-center text-gray-900 mb-6">Nuevo retiro</h1>

    <x-breadcrumb.template>
        <x-breadcrumb.item :href="route('panel')" content="Inicio" />
        <x-breadcrumb.item :href="route('cajas.index')" content="Cajas" />
        <x-breadcrumb.item :href="route('movimientos.index',['caja_id' => $caja_id])"
            content="Movimientos de caja" />
        <x-breadcrumb.item active='true' content="Nuevo retiro" />
    </x-breadcrumb.template>

    <x-forms.template :action="route('movimientos.store')" method='post'>

        <div class="space-y-6">

            <div>
                <x-forms.input id="descripcion" required='true'
                    labelText="Descripcion del retiro" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="metodo_pago" class="block text-sm font-medium text-gray-700 mb-2">
                        Método de retiro:
                        <span class="text-red-600">*</span></label>
                    <select required name="metodo_pago"
                        id="metodo_pago"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @foreach ($optionsMetodoPago as $item)
                        <option value="{{$item->value}}">{{$item->name}}</option>
                        @endforeach
                    </select>
                    @error('metodo_pago')
                    <small class="text-red-600 text-xs mt-1 block">{{ '*'.$message }}</small>
                    @enderror
                </div>

                <div>
                    <x-forms.input id="monto" required='true'
                        labelText="Monto del retiro" type='number' />
                </div>
            </div>

            <input type="hidden" name="caja_id" value='{{$caja_id}}'>
            <input type="hidden" name="tipo" value="RETIRO">

        </div>

        <x-slot name='footer'>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-8 rounded-lg transition-colors">Guardar</button>
        </x-slot>

    </x-forms.template>


</div>
@endsection

@push('js')

@endpush
