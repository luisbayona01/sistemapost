@extends('layouts.app')

@section('title','Crear rol')

@push('css')

@endpush

@section('content')
<div class="w-full px-4 md:px-6 py-4">
    <h1 class="text-3xl font-bold text-center text-gray-900 mb-6">Crear Rol</h1>

    <x-breadcrumb.template>
        <x-breadcrumb.item :href="route('panel')" content="Inicio" />
        <x-breadcrumb.item :href="route('roles.index')" content="Roles" />
        <x-breadcrumb.item active='true' content="Crear rol" />
    </x-breadcrumb.template>

    <div class="bg-white rounded-lg shadow">
        <div class="bg-gray-100 border-b border-gray-300 px-6 py-4 font-semibold text-gray-900">
            <p>Nota: Los roles son un conjunto de permisos</p>
        </div>
        <div class="px-6 py-4">
            <form action="{{ route('roles.store') }}" method="post">
                @csrf
                <!---Nombre de rol---->
                <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nombre del rol:</label>
                    </div>
                    <div class="md:col-span-2">
                        <input autocomplete="off" type="text" name="name"
                            id="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" value="{{old('name')}}">
                    </div>
                    <div>
                        @error('name')
                        <small class="text-red-600">
                            {{'*'.$message}}</small>
                        @enderror
                    </div>
                </div>

                <!---Permisos---->
                <div class="mb-6">
                    <p class="text-sm font-medium text-gray-700 mb-3">Permisos para el rol:</p>
                    <div class="space-y-2">
                        @foreach ($permisos as $item)
                        <div class="flex items-center">
                            <input type="checkbox" name="permission[]" id="{{$item->id}}"
                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-500" value="{{$item->id}}">
                            <label for="{{$item->id}}" class="ml-2 text-sm text-gray-700">
                                {{$item->name}}</label>
                        </div>
                        @endforeach
                    </div>
                </div>
                @error('permission')
                <small class="text-red-600">
                    {{'*'.$message}}</small>
                @enderror

                <div class="w-full text-center">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors">Guardar</button>
                </div>

            </form>
        </div>
    </div>


</div>
@endsection

@push('js')

@endpush
