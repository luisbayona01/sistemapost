@extends('layouts.app')

@section('title','Perfil')

@push('css')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')

<div class="w-full px-4 md:px-6 py-4 max-w-2xl mx-auto">
    <h1 class="text-3xl font-bold text-center text-gray-900 mb-6">Configurar perfil</h1>

    <div class="bg-white rounded-lg shadow">
        <div class="bg-gray-100 border-b border-gray-300 px-6 py-4">
            <p class="text-lg font-bold text-gray-900">Configure y personalize su perfil</p>
        </div>
        <form action="{{route('profile.update',['profile' => auth()->user() ])}}" method="POST">
            @method('PATCH')
            @csrf
            <div class="px-6 py-4">
                <div class="space-y-6">

                    <!---Name--->
                    <div>
                        <x-forms.input id='name'
                            required='true'
                            labelText='Nombre de usuario'
                            :defaultValue='auth()->user()->name' />
                    </div>

                    <!----Email--->
                    <div>
                        <x-forms.input id='email'
                            required='true'
                            type='email'
                            labelText='Correo electrónico'
                            :defaultValue='auth()->user()->email' />
                    </div>

                    <!----Password--->
                    <div>
                        <x-forms.input id='password'
                            type='password'
                            labelText='Nueva contraseña' />
                    </div>

                </div>
            </div>

            <div class="bg-gray-100 border-t border-gray-300 px-6 py-4 text-center">
                @can('editar-perfil')
                <input class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors cursor-pointer" type="submit" value="Guardar cambios">
                @endcan
            </div>

        </form>
    </div>

</div>
@endsection

@push('js')

@endpush
