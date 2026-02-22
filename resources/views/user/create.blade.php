@extends('layouts.app')

@section('title','Crear usuario')

@push('css')

@endpush

@section('content')
<div class="w-full px-4 md:px-6 py-4">
    <h1 class="text-3xl font-bold text-center text-gray-900 mb-6">Crear Usuario</h1>

    <x-breadcrumb.template>
        <x-breadcrumb.item :href="route('admin.dashboard.index')" content="Inicio" />
        <x-breadcrumb.item :href="route('users.index')" content="Usuarios" />
        <x-breadcrumb.item active='true' content="Crear Usuario" />
    </x-breadcrumb.template>

    <div class="bg-white rounded-lg shadow">
        <form action="{{ route('users.store') }}" method="post">
            @csrf
            <div class="bg-gray-100 border-b border-gray-300 px-6 py-4 font-semibold text-gray-900">
                <p>Nota: Los usuarios son los que pueden ingresar al sistema</p>
            </div>
            <div class="px-6 py-4">

                <!-----Empleado  .------>
                <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <label for="empleado_id" class="block text-sm font-medium text-gray-700">
                        Empleado:</label>
                    <div class="md:col-span-2">
                        <select name="empleado_id" id="empleado_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="" selected disabled>Seleccione:</option>
                            @foreach ($empleados as $item)
                            <option value="{{$item->id}}"
                                {{ old('empleado_id') == $item->id ? 'selected': '' }}>
                                {{$item->razon_social}}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        @error('empleado_id')
                        <small class="text-red-600">{{'*'.$message}}</small>
                        @enderror
                    </div>
                </div>

                <!---Nombre---->
                <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <label for="name" class="block text-sm font-medium text-gray-700">Nombres:</label>
                    <div class="md:col-span-2">
                        <input autocomplete="off" type="text" name="name"
                            id="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" value="{{old('name')}}"
                            aria-labelledby="nameHelpBlock">
                        <p class="text-xs text-gray-600 mt-1" id="nameHelpBlock">
                            Escriba un solo nombre
                        </p>
                    </div>
                    <div>
                        @error('name')
                        <small class="text-red-600">{{'*'.$message}}</small>
                        @enderror
                    </div>
                </div>

                <!---Username---->
                <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <label for="username" class="block text-sm font-medium text-gray-700">Usuario (Login):</label>
                    <div class="md:col-span-2">
                        <input autocomplete="off" type="text" name="username"
                            id="username" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" value="{{old('username')}}"
                            aria-labelledby="usernameHelpBlock">
                        <p class="text-xs text-gray-600 mt-1" id="usernameHelpBlock">
                            Nombre de usuario para iniciar sesión
                        </p>
                    </div>
                    <div>
                        @error('username')
                        <small class="text-red-600">{{'*'.$message}}</small>
                        @enderror
                    </div>
                </div>

                <!---Password---->
                <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <label for="password" class="block text-sm font-medium text-gray-700">Contraseña:</label>
                    <div class="md:col-span-2">
                        <input type="password" name="password" id="password"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" aria-labelledby="passwordHelpBlock">
                        <p class="text-xs text-gray-600 mt-1" id="passwordHelpBlock">
                            Escriba una constraseña segura. Debe incluir números.
                        </p>
                    </div>
                    <div>
                        @error('password')
                        <small class="text-red-600">{{'*'.$message}}</small>
                        @enderror
                    </div>
                </div>

                <!---Confirm_Password---->
                <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <label for="password_confirm" class="block text-sm font-medium text-gray-700">Confirmar:</label>
                    <div class="md:col-span-2">
                        <input type="password" name="password_confirm" id="password_confirm"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" aria-labelledby="passwordConfirmHelpBlock">
                        <p class="text-xs text-gray-600 mt-1" id="passwordConfirmHelpBlock">
                            Vuelva a escribir su contraseña.
                        </p>
                    </div>
                    <div>
                        @error('password_confirm')
                        <small class="text-red-600">{{'*'.$message}}</small>
                        @enderror
                    </div>
                </div>

                <!---Roles---->
                <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <label for="role" class="block text-sm font-medium text-gray-700">Rol:</label>
                    <div class="md:col-span-2">
                        <select name="role" id="role" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" aria-labelledby="rolHelpBlock">
                            <option value="" selected disabled>
                                Seleccione:</option>
                            @foreach ($roles as $item)
                            <option value="{{$item->name}}" @selected(old('role')==$item->name)>
                                {{$item->name}}
                            </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-600 mt-1" id="rolHelpBlock">
                            Escoja un rol para el usuario.
                        </p>
                    </div>
                    <div>
                        @error('role')
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

@endpush
