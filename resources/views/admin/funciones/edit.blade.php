@extends('layouts.app')

@section('title', 'Editar Función')

@section('content')
    <div class="px-6 py-8 max-w-4xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-slate-900">Editar Función</h1>
            <p class="text-slate-600 mt-1">Manejo de programación para {{ $funcione->pelicula->titulo }}</p>
        </div>

        @if($tienteVentas)
            <div class="bg-red-50 border-l-4 border-red-400 p-5 mb-8 rounded-r-2xl shadow-sm">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-400 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-bold text-red-800">¡Advertencia: Función con Ventas Realizadas!</h3>
                        <p class="text-sm text-red-700 mt-1">
                            Esta función ya tiene boletos vendidos. Se recomienda <b>no cambiar la sala</b> para evitar
                            inconsistencias con los asientos ya asignados físicamente.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <x-breadcrumb.template class="mb-6">
            <x-breadcrumb.item :href="route('admin.dashboard.index')" content="Administración" />
            <x-breadcrumb.item :href="route('funciones.index')" content="Funciones" />
            <x-breadcrumb.item active='true' content="Editar" />
        </x-breadcrumb.template>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <form action="{{ route('funciones.update', $funcione) }}" method="POST" class="p-8">
                @csrf
                @method('PUT')

                @if(session('show_confirm'))
                    <input type="hidden" name="force_update" value="1">
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <!-- Película -->
                    <div class="col-span-1 md:col-span-2">
                        <label for="pelicula_id" class="block text-sm font-semibold text-slate-700 mb-2">Película</label>
                        <select name="pelicula_id" id="pelicula_id"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 {{ $tienteVentas && !session('show_confirm') ? 'bg-slate-50 cursor-not-allowed' : 'focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10' }} outline-none"
                            {{ $tienteVentas && !session('show_confirm') ? 'readonly' : '' }}>
                            @foreach($peliculas as $pelicula)
                                <option value="{{ $pelicula->id }}" {{ old('pelicula_id', $funcione->pelicula_id) == $pelicula->id ? 'selected' : '' }}>
                                    {{ $pelicula->titulo }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Sala -->
                    <div>
                        <label for="sala_id" class="block text-sm font-semibold text-slate-700 mb-2">Sala</label>
                        <select name="sala_id" id="sala_id"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 {{ $tienteVentas && !session('show_confirm') ? 'bg-slate-50 cursor-not-allowed' : 'focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10' }} transition-all outline-none"
                            {{ $tienteVentas && !session('show_confirm') ? 'disabled' : '' }}>
                            @foreach($salas as $sala)
                                <option value="{{ $sala->id }}" {{ old('sala_id', $funcione->sala_id) == $sala->id ? 'selected' : '' }}>
                                    {{ $sala->nombre }} ({{ $sala->capacidad }} asientos)
                                </option>
                            @endforeach
                        </select>
                        @if($tienteVentas && !session('show_confirm')) <input type="hidden" name="sala_id" value="{{ $funcione->sala_id }}"> @endif
                    </div>


                    <!-- Fecha y Hora -->
                    <div>
                        <label for="fecha_hora" class="block text-sm font-semibold text-slate-700 mb-2">Fecha y Hora</label>
                        <input type="datetime-local" name="fecha_hora" id="fecha_hora"
                            value="{{ old('fecha_hora', \Carbon\Carbon::parse($funcione->fecha_hora)->format('Y-m-d\TH:i')) }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none"
                            required>
                    </div>

                    <!-- Precio Base -->
                    <div>
                        <label for="precio" class="block text-sm font-semibold text-slate-700 mb-2">Precio General
                            ($)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold">$</span>
                            <input type="number" name="precio" id="precio" value="{{ old('precio', $funcione->precio) }}"
                                class="w-full pl-8 pr-4 py-2.5 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none"
                                step="100" required>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                    <a href="{{ route('funciones.index') }}"
                        class="px-6 py-2.5 rounded-xl text-slate-600 font-semibold hover:bg-slate-50 transition-colors">Cancelar</a>

                    @if(session('show_confirm'))
                        <button type="submit"
                            class="px-8 py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl transition-all shadow-md hover:shadow-lg">
                            Confirmar y Forzar Actualización
                        </button>
                    @else
                        <button type="submit"
                            class="px-8 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl transition-all shadow-md hover:shadow-lg transform active:scale-95">
                            Guardar Cambios
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>
@endsection
