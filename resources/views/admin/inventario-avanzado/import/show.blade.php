@extends('admin.inventario-avanzado.layout')

@section('title', 'Carga Masiva de Inventario')

@section('inventory_content')
    <div class="max-w-4xl mx-auto py-10 px-4">
        <!-- Header Section -->
        <div class="mb-10 text-center">
            <div class="inline-flex p-4 rounded-3xl bg-indigo-50 text-indigo-600 mb-6">
                <i class="fas fa-file-excel text-4xl"></i>
            </div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight uppercase">Inicialización de Inventario</h1>
            <p class="text-slate-500 font-medium mt-2">Carga tus productos e insumos de forma masiva en segundos.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Step 1: Download Template -->
            <div
                class="bg-white p-8 rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-50 flex flex-col items-center text-center">
                <div
                    class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center font-black text-xl mb-6">
                    1</div>
                <h3 class="text-xl font-bold text-slate-900 mb-4">Descargar Plantilla</h3>
                <p class="text-slate-500 text-sm mb-8 leading-relaxed">
                    Utiliza nuestro formato oficial para asegurar que los datos se carguen correctamente. Incluye pestañas
                    para <b>Productos</b> e <b>Insumos</b>.
                </p>
                <a href="{{ route('inventario-avanzado.import.template') }}"
                    class="mt-auto w-full py-4 bg-emerald-500 hover:bg-emerald-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest transition-all shadow-lg shadow-emerald-200">
                    <i class="fas fa-download mr-2"></i> Descargar Excel
                </a>
            </div>

            <!-- Step 2: Upload File -->
            <div class="bg-white p-8 rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-50">
                <div class="flex flex-col items-center text-center h-full">
                    <div
                        class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center font-black text-xl mb-6">
                        2</div>
                    <h3 class="text-xl font-bold text-slate-900 mb-4">Subir Archivo</h3>

                    <form action="{{ route('inventario-avanzado.import.store') }}" method="POST"
                        enctype="multipart/form-data" class="w-full flex-1 flex flex-col">
                        @csrf
                        <div
                            class="relative group cursor-pointer border-2 border-dashed border-slate-200 rounded-3xl p-8 transition-all hover:border-indigo-400 hover:bg-indigo-50/30 mb-6">
                            <input type="file" name="file" required
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            <div class="text-slate-400 group-hover:text-indigo-500 transition-colors">
                                <i class="fas fa-cloud-upload-alt text-3xl mb-2"></i>
                                <p class="text-xs font-bold uppercase tracking-tighter">Arrastra tu Excel aquí</p>
                            </div>
                        </div>

                        <button type="submit"
                            class="w-full py-4 bg-slate-900 hover:bg-slate-800 text-white rounded-2xl font-black text-xs uppercase tracking-widest transition-all shadow-xl shadow-slate-900/20">
                            <i class="fas fa-rocket mr-2"></i> Iniciar Importación
                        </button>
                        <a href="{{ route('inventario-avanzado.export.current') }}"
                            class="mt-4 text-xs font-bold text-slate-400 hover:text-indigo-500 transition-colors uppercase tracking-widest">
                            <i class="fas fa-cloud-download-alt mr-1"></i> Exportar inventario actual
                        </a>
                    </form>
                </div>
            </div>
        </div>

        <!-- Features / Validation Info -->
        <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="flex items-start gap-4 p-6 bg-white/50 rounded-3xl border border-white">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-check-double"></i>
                </div>
                <div>
                    <h4 class="text-xs font-black text-slate-900 uppercase mb-1">Evita Duplicados</h4>
                    <p class="text-[10px] text-slate-500 leading-relaxed">El sistema omite automáticamente productos con el
                        mismo nombre para evitar errores.</p>
                </div>
            </div>
            <div class="flex items-start gap-4 p-6 bg-white/50 rounded-3xl border border-white">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-magic"></i>
                </div>
                <div>
                    <h4 class="text-xs font-black text-slate-900 uppercase mb-1">Auto-completado</h4>
                    <p class="text-[10px] text-slate-500 leading-relaxed">Si faltan categorías o unidades, el sistema las
                        creará por ti usando valores genéricos.</p>
                </div>
            </div>
            <div class="flex items-start gap-4 p-6 bg-white/50 rounded-3xl border border-white">
                <div
                    class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-500 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-tachometer-alt"></i>
                </div>
                <div>
                    <h4 class="text-xs font-black text-slate-900 uppercase mb-1">Listo para POS</h4>
                    <p class="text-[10px] text-slate-500 leading-relaxed">Tras la carga, tus snacks y bebidas aparecerán
                        inmediatamente en el punto de venta.</p>
                </div>
            </div>
        </div>
    </div>
@endsection
