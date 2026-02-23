@extends('layouts.app')

@section('title', 'Empresa')

@push('css')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')
    <div class="w-full px-4 md:px-6 py-4">
        <h1 class="text-3xl font-bold text-center text-gray-900 mb-6">Mi empresa</h1>

        <x-breadcrumb.template>
            <x-breadcrumb.item :href="route('admin.dashboard.index')" content="Inicio" />
            <x-breadcrumb.item active='true' content="Mi empresa" />
        </x-breadcrumb.template>

        <x-forms.template :action="route('empresa.update', ['empresa' => $empresa])" method='post' patch='true'>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div>
                    <x-forms.input id="nombre" required='true' :defaultValue='$empresa->nombre' />
                </div>

                <div>
                    <x-forms.input id="propietario" required='true' :defaultValue='$empresa->propietario' />
                </div>

                <div>
                    <x-forms.input id="ruc" required='true' :defaultValue='$empresa->ruc' />
                </div>

                <div>
                    <x-forms.input id="direccion" required='true' :defaultValue='$empresa->direccion' />
                </div>

                <div>
                    <x-forms.input id="porcentaje_impuesto" required='true' :defaultValue='$empresa->porcentaje_impuesto'
                        type='number' labelText='Porcentaje del impuesto (%)' />
                </div>

                <div>
                    <x-forms.input id="abreviatura_impuesto" required='true' :defaultValue='$empresa->abreviatura_impuesto'
                        labelText='Abreviatura del impuesto' />
                </div>

                <div>
                    <x-forms.input id="gastos_indirectos_porcentaje" required='false'
                        :defaultValue='$empresa->gastos_indirectos_porcentaje ?? 0' type='number'
                        labelText='% Gastos Operativos (Luz/Agua/Gas)' step="0.01" />
                </div>

                <div>
                    <x-forms.input id="merma_esperada_porcentaje" required='false'
                        :defaultValue='$empresa->merma_esperada_porcentaje ?? 0' type='number'
                        labelText='% Merma Esperada General' step="0.01" />
                </div>

                <div>
                    <x-forms.input id="correo" :defaultValue='$empresa->correo' type='email' />
                </div>

                <div>
                    <x-forms.input id="telefono" :defaultValue='$empresa->telefono' />
                </div>

                <div>
                    <x-forms.input id="ubicacion" :defaultValue='$empresa->ubicacion' />
                </div>

                <div class="md:col-span-2">
                    <label for="moneda_id" class="block text-sm font-medium text-gray-700 mb-2">Moneda seleccionada:</label>
                    <select name="moneda_id" id="moneda_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @foreach ($monedas as $moneda)
                            <option value="{{$moneda->id}}" {{$empresa->moneda_id == $moneda->id || old('moneda_id') == $moneda->id ? 'selected' : ''}}>
                                {{$moneda->nombre_completo}}
                            </option>
                        @endforeach
                    </select>
                    @error('moneda_id')
                        <small class="text-red-600">{{'* .$messsage'}}</small>
                    @enderror
                </div>

            </div>

            @can('update-empresa')
                <x-slot name='footer'>
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors">Actualizar</button>
                </x-slot>
            @endcan

        </x-forms.template>


    </div>
@endsection

@push('js')
@endpush
