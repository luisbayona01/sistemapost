@extends('layouts.app')

@section('content')
    <div class="flex flex-col md:flex-row min-h-screen bg-slate-50">
        <!-- Mini Sidebar para Inventario -->
        <aside class="w-full md:w-64 bg-white border-r border-slate-200 p-6 flex flex-col gap-2">
            <div class="mb-8 pl-2">
                <h2 class="text-xl font-bold text-slate-800">Inventario</h2>
                <p class="text-xs text-slate-400 font-medium">Gestión & Rentabilidad</p>
            </div>

            <nav class="flex flex-col gap-1">
                <a href="{{ route('inventario-avanzado.index') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('inventario-avanzado.index') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-slate-500 hover:bg-slate-50' }}">
                    <i class="fas fa-chart-line w-5"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('inventario-avanzado.almacen') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('inventario-avanzado.almacen') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-slate-500 hover:bg-slate-50' }}">
                    <i class="fas fa-warehouse w-5"></i>
                    <span>Almacén</span>
                </a>
                <a href="{{ route('inventario-avanzado.cocina') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('inventario-avanzado.cocina') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-slate-500 hover:bg-slate-50' }}">
                    <i class="fas fa-utensils w-5"></i>
                    <span>Cocina de Precios</span>
                </a>
                <a href="{{ route('inventario-avanzado.auditoria') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('inventario-avanzado.auditoria') ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-slate-500 hover:bg-slate-50' }}">
                    <i class="fas fa-clipboard-check w-5"></i>
                    <span>Auditoría</span>
                </a>
                <a href="{{ route('inventario-avanzado.import.show') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('inventario-avanzado.import.show') ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-slate-500 hover:bg-slate-50' }}">
                    <i class="fas fa-file-import w-5"></i>
                    <span>Carga Masiva</span>
                </a>
            </nav>

            <div class="mt-auto pt-6 border-t border-slate-100">
                <div class="bg-slate-900 rounded-2xl p-4 text-white">
                    <p class="text-[10px] text-slate-400 uppercase tracking-widest mb-1">Valor de Activos</p>
                    <h3 class="text-lg font-bold">@yield('valor_activos', '$0.00')</h3>
                </div>
            </div>
        </aside>

        <!-- Contenido Principal -->
        <main class="flex-1 p-6 md:p-10">
            @yield('inventory_content')
        </main>
    </div>
@endsection

@push('css')
    <style>
        .bg-emerald-gradient {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .text-emerald-shadow {
            text-shadow: 0 2px 4px rgba(16, 185, 129, 0.2);
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
@endpush
