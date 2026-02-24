@extends('layouts.app')

@section('title', 'Detalle de Empresa - ' . $empresa->nombre)

@section('content')
    <div class="min-h-screen bg-slate-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Breadcrumbs & Nav -->
            <nav class="flex mb-8 items-center justify-between">
                <ol class="flex items-center space-x-2 text-sm text-slate-500">
                    <li><a href="{{ route('root.dashboard') }}" class="hover:text-blue-600 transition-colors">Modo Dios</a>
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fas fa-chevron-right text-[10px] opacity-30"></i>
                        <a href="{{ route('root.empresas.index') }}"
                            class="hover:text-blue-600 transition-colors">Empresas</a>
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fas fa-chevron-right text-[10px] opacity-30"></i>
                        <span class="font-bold text-slate-900">{{ $empresa->nombre }}</span>
                    </li>
                </ol>

                <div class="flex gap-3">
                    @if($empresa->estado === 'activa')
                        <form action="{{ route('root.empresas.suspend', $empresa) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="px-4 py-2 border border-red-200 text-red-600 font-semibold rounded-lg hover:bg-red-50 transition-all text-sm">
                                Suspender Empresa
                            </button>
                        </form>
                    @else
                        <form action="{{ route('root.empresas.activate', $empresa) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="px-4 py-2 border border-emerald-200 text-emerald-600 font-semibold rounded-lg hover:bg-emerald-50 transition-all text-sm">
                                Activar Empresa
                            </button>
                        </form>
                    @endif
                </div>
            </nav>

            @if(session('success'))
                <div
                    class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center gap-3 text-emerald-800">
                    <i class="fas fa-check-circle text-emerald-500"></i>
                    <p class="text-sm font-medium">{{ session('success') }}</p>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- Sidebar: Info & Stats -->
                <div class="space-y-6">
                    <!-- Info Card -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                        <div class="flex flex-col items-center text-center mb-6">
                            <div
                                class="w-20 h-20 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-3xl font-black mb-4 shadow-xl shadow-blue-100">
                                {{ substr($empresa->nombre, 0, 1) }}
                            </div>
                            <h2 class="text-xl font-bold text-slate-900">{{ $empresa->nombre }}</h2>
                            <span class="text-xs font-mono text-slate-400 mt-1">UUID: {{ $empresa->id }}</span>
                        </div>

                        <div class="space-y-4">
                            <div class="flex justify-between items-center py-2 border-b border-slate-50">
                                <span class="text-xs font-semibold text-slate-500 uppercase tracking-widest">Plan
                                    Actual</span>
                                <span class="text-sm font-bold text-blue-600">{{ $empresa->plan?->nombre ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-slate-50">
                                <span class="text-xs font-semibold text-slate-500 uppercase tracking-widest">Moneda</span>
                                <span class="text-sm font-bold text-slate-700">{{ $empresa->moneda?->nombre }}
                                    ({{ $empresa->moneda?->simbolo }})</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-slate-50">
                                <span class="text-xs font-semibold text-slate-500 uppercase tracking-widest">RUC/NIT</span>
                                <span class="text-sm font-bold text-slate-700">{{ $empresa->ruc }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2">
                                <span class="text-xs font-semibold text-slate-500 uppercase tracking-widest">Creado
                                    el</span>
                                <span
                                    class="text-sm font-bold text-slate-700">{{ $empresa->created_at->format('d/m/Y') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Metrics -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-white p-4 rounded-xl border border-slate-200">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-1">Ventas
                                Totales</span>
                            <span
                                class="text-lg font-black text-slate-900">${{ number_format($estadisticas['total_ventas'], 0, ',', '.') }}</span>
                        </div>
                        <div class="bg-white p-4 rounded-xl border border-slate-200">
                            <span
                                class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-1">Transacciones</span>
                            <span class="text-lg font-black text-slate-900">{{ $estadisticas['numero_ventas'] }}</span>
                        </div>
                    </div>
                </div>

                <!-- Main Panel -->
                <div class="lg:col-span-2 space-y-8">

                    <!-- Modules Configuration -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                            <h3 class="font-bold text-slate-900">Control de Módulos (Feature Flags)</h3>
                            <i class="fas fa-toggle-on text-blue-500"></i>
                        </div>
                        <div class="p-6">
                            <form action="{{ route('root.empresas.modules', $empresa) }}" method="POST">
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    @php
                                        $modules = [
                                            'pos' => ['icon' => 'fa-cash-register', 'name' => 'Punto de Venta (POS)', 'desc' => 'Ventas rápidas y facturación'],
                                            'cinema' => ['icon' => 'fa-film', 'name' => 'Cine & Reservas', 'desc' => 'Cartelera y mapa de asientos'],
                                            'inventory' => ['icon' => 'fa-boxes', 'name' => 'Inventario Pro', 'desc' => 'Kardex y control de stock'],
                                            'reports' => ['icon' => 'fa-chart-pie', 'name' => 'Reportes IA', 'desc' => 'Analítica avanzada'],
                                            'api' => ['icon' => 'fa-code', 'name' => 'Acceso API/Integración', 'desc' => 'Webhooks y conexiones externas'],
                                        ];
                                    @endphp

                                    @foreach($modules as $key => $mod)
                                        <div
                                            class="flex items-start gap-4 p-4 rounded-xl border {{ !empty($config->modules_enabled[$key]) ? 'border-blue-100 bg-blue-50/10' : 'border-slate-100 bg-slate-50/30' }}">
                                            <div
                                                class="w-10 h-10 rounded-lg flex items-center justify-center {{ !empty($config->modules_enabled[$key]) ? 'bg-blue-100 text-blue-600' : 'bg-slate-200 text-slate-500' }}">
                                                <i class="fas {{ $mod['icon'] }}"></i>
                                            </div>
                                            <div class="flex-1">
                                                <div class="flex items-center justify-between">
                                                    <span class="text-sm font-bold text-slate-900">{{ $mod['name'] }}</span>
                                                    <label class="relative inline-flex items-center cursor-pointer">
                                                        <input type="checkbox" name="module_{{ $key }}" class="sr-only peer" {{ !empty($config->modules_enabled[$key]) ? 'checked' : '' }}>
                                                        <div
                                                            class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600">
                                                        </div>
                                                    </label>
                                                </div>
                                                <p
                                                    class="text-[10px] text-slate-500 mt-1 uppercase tracking-wider font-semibold">
                                                    {{ $mod['desc'] }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="mt-8">
                                    <button type="submit"
                                        class="w-full py-3 bg-slate-900 text-white font-bold rounded-xl hover:bg-slate-800 transition-all shadow-lg shadow-slate-200">
                                        Guardar Configuración de Módulos
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Users & Impersonation -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                            <h3 class="font-bold text-slate-900">Usuarios & Acceso Remoto</h3>
                            <i class="fas fa-users text-slate-400"></i>
                        </div>
                        <div class="divide-y divide-slate-50">
                            @foreach($empresa->users as $user)
                                <div class="p-6 flex items-center justify-between group">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-900">{{ $user->name }}</p>
                                            <div class="flex items-center gap-3">
                                                <span class="text-xs text-slate-500">{{ $user->email }}</span>
                                                <span
                                                    class="text-[10px] bg-blue-50 text-blue-600 px-2 py-0.5 rounded-full font-bold uppercase">{{ $user->roles->first()?->name ?? 'No Role' }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <form action="{{ route('root.impersonate', $user) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-600 rounded-lg text-xs font-bold hover:bg-blue-600 hover:text-white transition-all">
                                            <i class="fas fa-user-secret"></i>
                                            Intervenir Cuenta
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection