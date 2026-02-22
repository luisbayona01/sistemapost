@extends('layouts.app')

@section('title', 'Carga Masiva de Inventario')

@section('content')
    <div class="max-w-2xl mx-auto px-4 py-8">
        <div class="mb-6">
            <a href="{{ route('inventario.index') }}"
                class="text-slate-400 hover:text-slate-700 font-bold text-sm flex items-center gap-2 mb-4">
                <i class="fas fa-arrow-left text-xs"></i> Volver a Inventario
            </a>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Carga Masiva de Stock</h1>
            <p class="text-slate-500 text-sm mt-1">Actualiza múltiples productos a la vez subiendo un archivo CSV.</p>
        </div>

        {{-- Alertas --}}
        @if(session('success'))
            <div
                class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl px-5 py-4 mb-4 flex items-start gap-3">
                <i class="fas fa-check-circle text-emerald-500 mt-0.5"></i>
                <div>
                    <p class="font-black text-sm">{{ session('success') }}</p>
                    @if(session('carga_errores'))
                        <ul class="mt-2 text-xs space-y-1 text-amber-700">
                            @foreach(session('carga_errores') as $err)
                                <li>⚠️ {{ $err }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-800 rounded-2xl px-5 py-4 mb-4 text-sm font-bold">
                <i class="fas fa-times-circle mr-2"></i> {{ session('error') }}
            </div>
        @endif

        {{-- Step 1: Descarga Plantilla --}}
        <div class="bg-white rounded-3xl border-2 border-slate-100 shadow-sm p-6 mb-4">
            <div class="flex items-center gap-3 mb-4">
                <span
                    class="w-8 h-8 bg-slate-900 text-white rounded-xl flex items-center justify-center font-black text-sm">1</span>
                <h2 class="font-black text-slate-800">Descarga la Plantilla</h2>
            </div>
            <p class="text-sm text-slate-500 mb-4">El archivo contiene las columnas exactas requeridas: <code
                    class="bg-slate-100 px-1 rounded">codigo_producto, cantidad, motivo</code></p>
            <a href="{{ route('inventario.carga-masiva.plantilla') }}"
                class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-700 text-white px-6 py-3 rounded-2xl font-black text-sm transition-all">
                <i class="fas fa-file-csv"></i> Descargar Plantilla CSV
            </a>
        </div>

        {{-- Step 2: Llenar y subir --}}
        <div class="bg-white rounded-3xl border-2 border-slate-100 shadow-sm p-6 mb-4">
            <div class="flex items-center gap-3 mb-4">
                <span
                    class="w-8 h-8 bg-emerald-600 text-white rounded-xl flex items-center justify-center font-black text-sm">2</span>
                <h2 class="font-black text-slate-800">Completa y Sube el CSV</h2>
            </div>

            <div class="bg-slate-50 rounded-2xl p-4 mb-5 text-xs text-slate-600 space-y-1 border border-slate-100">
                <p class="font-black text-slate-700 mb-2">Reglas:</p>
                <p>• <strong>codigo_producto</strong>: Código exacto del producto (ej: <code>PRO-0001</code>)</p>
                <p>• <strong>cantidad</strong>: Número positivo a SUMAR al stock actual</p>
                <p>• <strong>motivo</strong>: Descripción del movimiento (para Kardex)</p>
                <p>• Líneas con errores son omitidas pero no detienen el proceso</p>
            </div>

            <form action="{{ route('inventario.carga-masiva.procesar') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="border-2 border-dashed border-slate-200 rounded-2xl p-8 text-center mb-4 hover:border-emerald-400 transition-colors"
                    id="dropzone" ondragover="event.preventDefault(); this.classList.add('border-emerald-500')"
                    ondragleave="this.classList.remove('border-emerald-500')" ondrop="handleDrop(event)">
                    <i class="fas fa-cloud-upload-alt text-4xl text-slate-300 mb-3"></i>
                    <p class="text-sm font-bold text-slate-500 mb-2">Arrastra tu CSV aquí o</p>
                    <label
                        class="cursor-pointer bg-white border-2 border-slate-200 px-4 py-2 rounded-xl font-black text-xs text-slate-700 hover:border-emerald-500 hover:text-emerald-600 transition-all">
                        Seleccionar Archivo
                        <input type="file" name="archivo_csv" id="archivo_csv" accept=".csv,.txt" class="hidden"
                            onchange="mostrarArchivo(this)">
                    </label>
                    <p id="nombre-archivo" class="mt-3 text-xs font-bold text-emerald-600 hidden"></p>
                </div>

                <button type="submit"
                    class="w-full bg-emerald-600 hover:bg-emerald-500 text-white py-4 rounded-2xl font-black uppercase tracking-widest text-sm transition-all shadow-lg shadow-emerald-900/10">
                    <i class="fas fa-upload mr-2"></i> Procesar Carga
                </button>
            </form>
        </div>
    </div>

    <script>
        function mostrarArchivo(input) {
            if (input.files.length > 0) {
                const nombre = document.getElementById('nombre-archivo');
                nombre.textContent = '✅ ' + input.files[0].name;
                nombre.classList.remove('hidden');
            }
        }
        function handleDrop(event) {
            event.preventDefault();
            const dt = event.dataTransfer;
            const files = dt.files;
            if (files.length > 0) {
                const input = document.getElementById('archivo_csv');
                input.files = files;
                mostrarArchivo(input);
            }
            event.target.closest('#dropzone').classList.remove('border-emerald-500');
        }
    </script>
@endsection