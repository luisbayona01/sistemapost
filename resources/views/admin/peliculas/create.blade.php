@extends('layouts.app')

@section('title', 'Nueva Película')

@push('css')
    <style>
        .poster-preview {
            max-width: 200px;
            max-height: 300px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
    </style>
@endpush

@section('content')
    <div class="w-full px-4 md:px-6 py-4">
        <h1 class="text-3xl font-bold text-center text-gray-900 mb-6">Registrar Nueva Película</h1>

        <x-breadcrumb.template>
            <x-breadcrumb.item :href="route('admin.dashboard.index')" content="Inicio" />
            <x-breadcrumb.item :href="route('peliculas.index')" content="Películas" />
            <x-breadcrumb.item active='true' content="Nueva" />
        </x-breadcrumb.template>

        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden max-w-5xl mx-auto">
            <form action="{{ route('peliculas.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="p-8">
                    <!-- Sección Principal -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">

                        <!-- Columna Izquierda: Póster -->
                        <div class="lg:col-span-1 space-y-4">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Póster de la Película</label>
                            <div class="bg-gray-50 border-2 border-dashed border-gray-300 rounded-xl p-4 flex flex-col items-center justify-center text-center cursor-pointer hover:bg-gray-100 transition-colors relative h-[400px]"
                                id="poster-dropzone">
                                <input type="file" name="afiche" id="afiche"
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept="image/*">
                                <div id="poster-placeholder" class="space-y-2">
                                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400"></i>
                                    <p class="text-sm text-gray-500 font-medium">Haga clic o arrastre una imagen</p>
                                    <p class="text-xs text-gray-400">PNG, JPG hasta 2MB</p>
                                </div>
                                <img id="poster-preview"
                                    class="poster-preview hidden absolute inset-0 w-full h-full object-contain p-2" src=""
                                    alt="Vista previa">
                            </div>
                            @error('afiche') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror

                            <!-- URL Trailer -->
                            <div class="mt-4">
                                <label for="trailer_url" class="block text-sm font-semibold text-gray-700 mb-2">URL del
                                    Trailer (YouTube)</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"><i
                                            class="fab fa-youtube"></i></span>
                                    <input type="url" name="trailer_url" id="trailer_url" value="{{ old('trailer_url') }}"
                                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="https://youtube.com/...">
                                </div>
                                @error('trailer_url') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Columna Derecha: Información -->
                        <div class="lg:col-span-2 space-y-6">

                            <!-- Título -->
                            <div>
                                <label for="titulo" class="block text-sm font-bold text-gray-700 mb-2">Título de la Película
                                    <span class="text-red-500">*</span></label>
                                <input type="text" name="titulo" id="titulo" value="{{ old('titulo') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-lg font-semibold"
                                    placeholder="Ej: Avatar: El Camino del Agua" required>
                                @error('titulo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Género -->
                                <div>
                                    <label for="genero"
                                        class="block text-sm font-semibold text-gray-700 mb-2">Género</label>
                                    <select name="genero" id="genero"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        <option value="">Seleccione...</option>
                                        @foreach(['Acción', 'Aventura', 'Comedia', 'Drama', 'Terror', 'Ciencia Ficción', 'Fantasía', 'Animación', 'Documental', 'Thriller', 'Romance'] as $gen)
                                            <option value="{{ $gen }}" {{ old('genero') == $gen ? 'selected' : '' }}>{{ $gen }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('genero') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>

                                <!-- Duración -->
                                <div>
                                    <label for="duracion" class="block text-sm font-semibold text-gray-700 mb-2">Duración
                                        (minutos) <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <input type="text" name="duracion" id="duracion" value="{{ old('duracion') }}"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                            placeholder="Ej: 120 min" required>
                                        <span
                                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">min</span>
                                    </div>
                                    @error('duracion') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Clasificación -->
                                <div>
                                    <label for="clasificacion"
                                        class="block text-sm font-semibold text-gray-700 mb-2">Clasificación <span
                                            class="text-red-500">*</span></label>
                                    <select name="clasificacion" id="clasificacion"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                        required>
                                        <option value="">Seleccione...</option>
                                        <option value="ATP" {{ old('clasificacion') == 'ATP' ? 'selected' : '' }}>ATP (Apta
                                            Todo Público)</option>
                                        <option value="+7" {{ old('clasificacion') == '+7' ? 'selected' : '' }}>+7 Años
                                        </option>
                                        <option value="+13" {{ old('clasificacion') == '+13' ? 'selected' : '' }}>+13 Años
                                        </option>
                                        <option value="+16" {{ old('clasificacion') == '+16' ? 'selected' : '' }}>+16 Años
                                        </option>
                                        <option value="+18" {{ old('clasificacion') == '+18' ? 'selected' : '' }}>+18 Años
                                            (Restringida)</option>
                                    </select>
                                    @error('clasificacion') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Distribuidor -->
                                <div>
                                    <label for="distribuidor_id"
                                        class="block text-sm font-semibold text-gray-700 mb-2">Distribuidor</label>
                                    <select name="distribuidor_id" id="distribuidor_id"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        <option value="">Seleccione...</option>
                                        @foreach($distribuidores as $distribuidor)
                                            <option value="{{ $distribuidor->id }}" {{ old('distribuidor_id') == $distribuidor->id ? 'selected' : '' }}>{{ $distribuidor->nombre }}</option>
                                        @endforeach
                                    </select>
                                    @error('distribuidor_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Fecha Estreno -->
                                <div>
                                    <label for="fecha_estreno" class="block text-sm font-semibold text-gray-700 mb-2">Fecha
                                        Estreno</label>
                                    <input type="date" name="fecha_estreno" id="fecha_estreno"
                                        value="{{ old('fecha_estreno') }}"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                </div>

                                <!-- Fecha Fin -->
                                <div>
                                    <label for="fecha_fin_exhibicion"
                                        class="block text-sm font-semibold text-gray-700 mb-2">Fin Exhibición</label>
                                    <input type="date" name="fecha_fin_exhibicion" id="fecha_fin_exhibicion"
                                        value="{{ old('fecha_fin_exhibicion') }}"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>

                            <!-- Sinopsis -->
                            <div>
                                <label for="sinopsis" class="block text-sm font-semibold text-gray-700 mb-2">Sinopsis /
                                    Resumen</label>
                                <textarea name="sinopsis" id="sinopsis" rows="4"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 resize-none"
                                    placeholder="Escriba aquí la sinopsis de la película...">{{ old('sinopsis') }}</textarea>
                                @error('sinopsis') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 border-t border-gray-200 px-8 py-5 flex justify-end gap-3">
                    <a href="{{ route('peliculas.index') }}"
                        class="px-6 py-2.5 rounded-lg text-gray-700 font-medium hover:bg-gray-200 transition-colors">Cancelar</a>
                    <button type="submit"
                        class="px-6 py-2.5 rounded-lg bg-blue-600 text-white font-bold hover:bg-blue-700 shadow-md transition-all transform hover:-translate-y-0.5">
                        <i class="fas fa-save mr-2"></i> Guardar Película
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('js')
    <script>
        const inputAfiche = document.getElementById('afiche');
        const posterPreview = document.getElementById('poster-preview');
        const posterPlaceholder = document.getElementById('poster-placeholder');

        inputAfiche.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    posterPreview.src = e.target.result;
                    posterPreview.classList.remove('hidden');
                    posterPlaceholder.classList.add('hidden');
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
@endpush
