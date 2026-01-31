@extends('layouts.app')

@section('title','Crear empleado')

@push('css')
@endpush

@section('content')
<div class="w-full px-4 md:px-6 py-4">
    <h1 class="text-3xl font-bold text-center text-gray-900 mb-6">Crear Empleado</h1>

    <x-breadcrumb.template>
        <x-breadcrumb.item :href="route('panel')" content="Inicio" />
        <x-breadcrumb.item :href="route('empleados.index')" content="Empleados" />
        <x-breadcrumb.item active='true' content="Crear empleado" />
    </x-breadcrumb.template>

    <x-forms.template :action="route('empleados.store')" method='post' file='true'>

        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div>
                    <x-forms.input id="razon_social" required='true' labelText='Nombres y Apellidos' />
                </div>

                <div>
                    <x-forms.input id="cargo" required='true' />
                </div>

                <div>
                    <x-forms.input id="img" type='file' labelText='Seleccione una imagen'/>
                </div>

                <div>
                    <p class="text-sm text-gray-700 mb-3">Imagen seleccionada:</p>

                    <img id="img-default"
                        class="w-full max-w-xs"
                        src="{{ asset('assets/img/paisaje.png') }}"
                        alt="Imagen por defecto">

                    <img src="" alt="Ha cargado un archivo no compatible"
                        id="img-preview"
                        class="w-full max-w-xs border-2 border-gray-300 rounded" style="display: none;">
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
    const inputImagen = document.getElementById('img');
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
