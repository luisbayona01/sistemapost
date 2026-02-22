@extends('layouts.app')

@section('title', 'Nueva Función')

@section('content')
    <div class="px-6 py-8 max-w-4xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-slate-900">Nueva Función</h1>
            <p class="text-slate-600 mt-1">Programe un nuevo horario para una película en cartelera.</p>
        </div>

        <x-breadcrumb.template class="mb-6">
            <x-breadcrumb.item :href="route('admin.dashboard.index')" content="Administración" />
            <x-breadcrumb.item :href="route('funciones.index')" content="Funciones" />
            <x-breadcrumb.item active='true' content="Nueva" />
        </x-breadcrumb.template>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <form action="{{ route('funciones.store') }}" method="POST" class="p-8">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <!-- Película -->
                    <div class="col-span-1 md:col-span-2">
                        <label for="pelicula_id" class="block text-sm font-semibold text-slate-700 mb-2">Película <span
                                class="text-red-500">*</span></label>
                        <select name="pelicula_id" id="pelicula_id"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all outline-none"
                            required>
                            <option value="">Seleccione una película...</option>
                            @foreach($peliculas as $pelicula)
                                <option value="{{ $pelicula->id }}" {{ old('pelicula_id') == $pelicula->id ? 'selected' : '' }}>
                                    {{ $pelicula->titulo }} ({{ $pelicula->duracion }} - {{ $pelicula->clasificacion }})
                                </option>
                            @endforeach
                        </select>
                        @error('pelicula_id') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <!-- Sala -->
                    <div>
                        <label for="sala_id" class="block text-sm font-semibold text-slate-700 mb-2">Sala <span
                                class="text-red-500">*</span></label>
                        <select name="sala_id" id="sala_id"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all outline-none"
                            required>
                            <option value="">Seleccione una sala...</option>
                            @foreach($salas as $sala)
                                <option value="{{ $sala->id }}" {{ old('sala_id') == $sala->id ? 'selected' : '' }}>
                                    {{ $sala->nombre }} ({{ $sala->capacidad }} asientos)
                                </option>
                            @endforeach
                        </select>
                        @error('sala_id') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <!-- Fecha y Hora Separados -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="fecha" class="block text-sm font-semibold text-slate-700 mb-2">Fecha <span
                                    class="text-red-500">*</span></label>
                            <input type="date" name="fecha" id="fecha" value="{{ old('fecha', now()->format('Y-m-d')) }}"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all outline-none"
                                required>
                            @error('fecha') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="hora" class="block text-sm font-semibold text-slate-700 mb-2">Hora (24h) <span
                                    class="text-red-500">*</span></label>
                            <input type="time" name="hora" id="hora" value="{{ old('hora', '14:00') }}"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all outline-none"
                                required>
                            @error('hora') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Precio Base -->
                    <div>
                        <label for="precio" class="block text-sm font-semibold text-slate-700 mb-2">Precio General ($) <span
                                class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold">$</span>
                            <input type="number" name="precio" id="precio" value="{{ old('precio', 30000) }}"
                                class="w-full pl-8 pr-4 py-2.5 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all outline-none"
                                placeholder="30000" step="100" required>
                        </div>
                        @error('precio') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <!-- Tarifas Activas -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-3">Tarifas Disponibles</label>
                        <div class="space-y-2 max-h-40 overflow-y-auto p-4 bg-slate-50 rounded-xl border border-slate-100">
                            @foreach($precios as $precio_cat)
                                <label class="flex items-center group cursor-pointer">
                                    <input type="checkbox" name="precios_entrada[]" value="{{ $precio_cat->id }}"
                                        class="w-5 h-5 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 mr-3" {{ is_array(old('precios_entrada')) && in_array($precio_cat->id, old('precios_entrada')) ? 'checked' : '' }}>
                                    <span class="text-slate-700 group-hover:text-emerald-600 transition-colors">
                                        {{ $precio_cat->nombre }} (${{ number_format($precio_cat->precio, 0) }})
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        <p class="text-xs text-slate-500 mt-2 italic">Estas tarifas se mostrarán como opciones en el POS.
                        </p>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                    <a href="{{ route('funciones.index') }}"
                        class="px-6 py-2.5 rounded-xl text-slate-600 font-semibold hover:bg-slate-50 transition-colors">Cancelar</a>
                    <button type="submit"
                        class="px-8 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition-all shadow-md hover:shadow-lg transform active:scale-95">
                        Generar Función y Asientos
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
