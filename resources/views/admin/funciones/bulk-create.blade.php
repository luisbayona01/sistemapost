@extends('layouts.app')

@section('title', 'Programador Masivo')

@section('content')
    <div class="px-6 py-8 max-w-5xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-slate-900 line-clamp-1">Programador Masivo de Funciones</h1>
            <p class="text-slate-600 mt-1">Genere múltiples horarios automáticamente para una película.</p>
        </div>

        <x-breadcrumb.template class="mb-6">
            <x-breadcrumb.item :href="route('admin.dashboard.index')" content="Administración" />
            <x-breadcrumb.item :href="route('funciones.index')" content="Funciones" />
            <x-breadcrumb.item active='true' content="Programador Masivo" />
        </x-breadcrumb.template>

        <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-200 overflow-hidden">
            <form action="{{ route('funciones.bulkStore') }}" method="POST" class="p-8">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                    <!-- Columna 1: Película y Sala -->
                    <div class="space-y-6">
                        <h3 class="text-lg font-bold text-slate-800 flex items-center">
                            <span
                                class="w-8 h-8 bg-indigo-100 text-indigo-600 rounded-lg flex items-center justify-center mr-3 text-sm">1</span>
                            Información Básica
                        </h3>

                        <div>
                            <label for="pelicula_id" class="block text-sm font-semibold text-slate-700 mb-2">Película <span
                                    class="text-red-500">*</span></label>
                            <select name="pelicula_id" id="pelicula_id"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none"
                                required>
                                <option value="">Seleccione película...</option>
                                @foreach($peliculas as $pelicula)
                                    <option value="{{ $pelicula->id }}" {{ old('pelicula_id') == $pelicula->id ? 'selected' : '' }}>
                                        {{ $pelicula->titulo }} ({{ $pelicula->duracion }})
                                    </option>
                                @endforeach
                            </select>
                            @error('pelicula_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="sala_id" class="block text-sm font-semibold text-slate-700 mb-2">Sala <span
                                    class="text-red-500">*</span></label>
                            <div class="grid grid-cols-2 gap-3">
                                @foreach($salas as $sala)
                                    <label
                                        class="relative flex items-center justify-center p-4 rounded-xl border-2 cursor-pointer transition-all hover:border-indigo-200 group">
                                        <input type="radio" name="sala_id" value="{{ $sala->id }}" class="sr-only peer" required
                                            {{ old('sala_id') == $sala->id ? 'checked' : '' }}>
                                        <div
                                            class="peer-checked:border-indigo-500 peer-checked:bg-indigo-50 absolute inset-0 rounded-xl border-2 border-transparent transition-all">
                                        </div>
                                        <span
                                            class="relative z-10 text-sm font-bold text-slate-600 peer-checked:text-indigo-700">{{ $sala->nombre }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <label for="precio" class="block text-sm font-semibold text-slate-700 mb-2">Precio Base ($)
                                <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold">$</span>
                                <input type="number" name="precio" id="precio" value="{{ old('precio', 30000) }}"
                                    class="w-full pl-8 pr-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none"
                                    step="100" required>
                            </div>
                        </div>
                    </div>

                    <!-- Columna 2: Rango y Días -->
                    <div class="space-y-6">
                        <h3 class="text-lg font-bold text-slate-800 flex items-center">
                            <span
                                class="w-8 h-8 bg-amber-100 text-amber-600 rounded-lg flex items-center justify-center mr-3 text-sm">2</span>
                            Programación Temporal
                        </h3>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="fecha_desde"
                                    class="block text-sm font-semibold text-slate-700 mb-2">Desde</label>
                                <input type="date" name="fecha_desde" id="fecha_desde"
                                    value="{{ old('fecha_desde', date('Y-m-d')) }}"
                                    class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-500 outline-none transition-all"
                                    required>
                            </div>
                            <div>
                                <label for="fecha_hasta"
                                    class="block text-sm font-semibold text-slate-700 mb-2">Hasta</label>
                                <input type="date" name="fecha_hasta" id="fecha_hasta" value="{{ old('fecha_hasta') }}"
                                    class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-500 outline-none transition-all"
                                    required>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-3">Días de la Semana <span
                                    class="text-red-500">*</span></label>
                            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-2 gap-2">
                                @php $diasArr = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo']; @endphp
                                @foreach($diasArr as $val => $nombre)
                                    <label
                                        class="flex items-center p-3 rounded-xl border border-slate-100 bg-slate-50 cursor-pointer hover:bg-slate-100 transition-colors group">
                                        <input type="checkbox" name="dias[]" value="{{ $val }}"
                                            class="w-5 h-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 mr-3"
                                            {{ is_array(old('dias')) && in_array($val, old('dias')) ? 'checked' : '' }}>
                                        <span
                                            class="text-xs font-bold text-slate-600 group-hover:text-slate-900">{{ $nombre }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('dias') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        {{-- REEMPLAZO BLOQUE HORARIOS --}}
                        <div class="mb-6">
                            <label class="block font-bold text-lg mb-3">
                                🕐 Horarios del día
                                <span class="text-sm font-normal text-gray-500">(agrega todos los que necesites)</span>
                            </label>

                            <div id="horarios-lista">
                                <div class="horario-fila flex gap-3 mb-3 items-center">
                                    <input type="time" name="horarios[]" value="14:00"
                                        class="border-2 rounded-lg px-4 py-2 text-xl font-bold border-slate-200 focus:border-indigo-500 outline-none"
                                        required>
                                    <button type="button" onclick="quitarHorario(this)"
                                        class="bg-red-100 text-red-600 hover:bg-red-200 px-4 py-2 rounded-lg font-bold transition-colors">
                                        ✕
                                    </button>
                                </div>
                            </div>

                            <button type="button" onclick="agregarHorario()"
                                class="bg-blue-100 text-blue-700 hover:bg-blue-200 px-4 py-3 rounded-lg font-semibold mt-2 w-full transition-colors border border-blue-200">
                                ➕ Agregar otro horario
                            </button>

                            <div class="mt-4 bg-slate-50 p-4 rounded-xl border border-slate-100">
                                <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-3">⚡ Horarios rápidos:
                                </p>
                                <div class="flex gap-2 flex-wrap">
                                    <button type="button" onclick="setHorarios(['14:00','17:00','20:00'])"
                                        class="bg-white border border-slate-200 hover:border-indigo-300 hover:text-indigo-600 px-3 py-2 rounded-lg text-xs font-bold shadow-sm transition-all">
                                        14:00 / 17:00 / 20:00
                                    </button>
                                    <button type="button" onclick="setHorarios(['14:00','17:00','20:00','22:30'])"
                                        class="bg-white border border-slate-200 hover:border-indigo-300 hover:text-indigo-600 px-3 py-2 rounded-lg text-xs font-bold shadow-sm transition-all">
                                        + 22:30
                                    </button>
                                    <button type="button" onclick="setHorarios(['16:00','19:00','22:00'])"
                                        class="bg-white border border-slate-200 hover:border-indigo-300 hover:text-indigo-600 px-3 py-2 rounded-lg text-xs font-bold shadow-sm transition-all">
                                        16 / 19 / 22
                                    </button>
                                </div>
                            </div>
                        </div>

                        <script>
                            function agregarHorario() {
                                const lista = document.getElementById('horarios-lista');
                                const div = document.createElement('div');
                                div.className = 'horario-fila flex gap-3 mb-3 items-center';
                                div.innerHTML = `
                                <input type="time" name="horarios[]"
                                    class="border-2 rounded-lg px-4 py-2 text-xl font-bold border-slate-200 focus:border-indigo-500 outline-none">
                                <button type="button" onclick="quitarHorario(this)"
                                        class="bg-red-100 text-red-600 hover:bg-red-200 px-4 py-2 rounded-lg font-bold transition-colors">
                                    ✕
                                </button>`;
                                lista.appendChild(div);
                            }

                            function quitarHorario(btn) {
                                const filas = document.querySelectorAll('.horario-fila');
                                if (filas.length > 1) {
                                    btn.closest('.horario-fila').remove();
                                }
                            }

                            function setHorarios(horarios) {
                                const lista = document.getElementById('horarios-lista');
                                lista.innerHTML = '';
                                horarios.forEach(hora => {
                                    const div = document.createElement('div');
                                    div.className = 'horario-fila flex gap-3 mb-3 items-center';
                                    div.innerHTML = `
                                    <input type="time" name="horarios[]" value="${hora}"
                                        class="border-2 rounded-lg px-4 py-2 text-xl font-bold border-slate-200 focus:border-indigo-500 outline-none">
                                    <button type="button" onclick="quitarHorario(this)"
                                            class="bg-red-100 text-red-600 hover:bg-red-200 px-4 py-2 rounded-lg font-bold transition-colors">
                                        ✕
                                    </button>`;
                                    lista.appendChild(div);
                                });
                            }
                        </script>
                    </div>

                    <!-- Columna 3: Tarifas Adicionales -->
                    <div class="space-y-6">
                        <h3 class="text-lg font-bold text-slate-800 flex items-center">
                            <span
                                class="w-8 h-8 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center mr-3 text-sm">3</span>
                            Tarifas y Confirmación
                        </h3>

                        <div>
                            <p class="text-sm font-semibold text-slate-700 mb-3">Opciones de Tarifa</p>
                            <div
                                class="space-y-2 max-h-48 overflow-y-auto p-4 bg-slate-50 rounded-2xl border border-slate-100">
                                @foreach($precios as $precio_cat)
                                    <label class="flex items-center group cursor-pointer">
                                        <input type="checkbox" name="precios_entrada[]" value="{{ $precio_cat->id }}"
                                            class="w-5 h-5 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 mr-3"
                                            {{ is_array(old('precios_entrada')) && in_array($precio_cat->id, old('precios_entrada')) ? 'checked' : '' }}>
                                        <span
                                            class="text-xs font-bold text-slate-600 group-hover:text-emerald-600 transition-colors">
                                            {{ $precio_cat->nombre }} (${{ number_format($precio_cat->precio, 0) }})
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="bg-indigo-50 p-6 rounded-2xl border border-indigo-100">
                            <div class="flex items-start mb-4">
                                <i class="fas fa-info-circle text-indigo-500 mt-1 mr-3"></i>
                                <p class="text-xs text-indigo-700 leading-relaxed font-medium">
                                    El sistema validará automáticamente choques de horario con funciones existentes en el
                                    mismo rango, dejando un margen de <b>20 minutos</b> para limpieza de sala.
                                </p>
                            </div>
                        </div>

                        <button type="submit"
                            class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-2xl shadow-lg shadow-indigo-200 transition-all transform hover:-translate-y-1 active:scale-95 flex items-center justify-center">
                            <i class="fas fa-magic mr-2"></i> Procesar Programación Masiva
                        </button>
                        <a href="{{ route('funciones.index') }}"
                            class="block text-center text-sm font-bold text-slate-500 hover:text-slate-700">Cancelar y
                            Volver</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection