@extends('layouts.app')

@section('title', 'Dashboard Super Admin - CinemaPOS')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-slate-100">
    <!-- Header Section -->
    <div class="border-b border-slate-200 bg-white/80 backdrop-blur-sm sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900">Dashboard Super Admin</h1>
                    <p class="text-sm text-slate-600 mt-1">Métricas globales y control de empresas</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Success Alert -->
        @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-lg flex items-start gap-3">
            <svg class="w-5 h-5 text-emerald-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <p class="text-sm text-emerald-800">{{ session('success') }}</p>
        </div>
        @endif

        <!-- Key Metrics Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
            <!-- Total Empresas -->
            <div class="group relative bg-white rounded-xl shadow-sm border border-slate-200 p-6 hover:shadow-md transition-all duration-300 hover:border-blue-200">
                <div class="flex items-center justify-between mb-4">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Total Empresas</p>
                    <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                </div>
                <p class="text-4xl font-bold text-slate-900">{{ $totalEmpresas }}</p>
                <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 to-blue-300 rounded-b-xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
            </div>

            <!-- Empresas Activas -->
            <div class="group relative bg-white rounded-xl shadow-sm border border-slate-200 p-6 hover:shadow-md transition-all duration-300 hover:border-emerald-200">
                <div class="flex items-center justify-between mb-4">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Activas</p>
                    <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center group-hover:bg-emerald-200 transition-colors">
                        <svg class="w-5 h-5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
                <p class="text-4xl font-bold text-slate-900">{{ $empresasActivas }}</p>
                <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-emerald-500 to-emerald-300 rounded-b-xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
            </div>

            <!-- En Trial -->
            <div class="group relative bg-white rounded-xl shadow-sm border border-slate-200 p-6 hover:shadow-md transition-all duration-300 hover:border-amber-200">
                <div class="flex items-center justify-between mb-4">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">En Trial</p>
                    <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center group-hover:bg-amber-200 transition-colors">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <p class="text-4xl font-bold text-slate-900">{{ $empresasEnTrial }}</p>
                <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-amber-500 to-amber-300 rounded-b-xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
            </div>

            <!-- Suspendidas -->
            <div class="group relative bg-white rounded-xl shadow-sm border border-slate-200 p-6 hover:shadow-md transition-all duration-300 hover:border-red-200">
                <div class="flex items-center justify-between mb-4">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Suspendidas</p>
                    <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center group-hover:bg-red-200 transition-colors">
                        <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 2.526a6 6 0 008.367 8.368zM17.5 11a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
                <p class="text-4xl font-bold text-slate-900">{{ $empresasSuspendidas }}</p>
                <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-red-500 to-red-300 rounded-b-xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
            </div>

            <!-- Suscripción Vencida -->
            <div class="group relative bg-white rounded-xl shadow-sm border border-slate-200 p-6 hover:shadow-md transition-all duration-300 hover:border-violet-200">
                <div class="flex items-center justify-between mb-4">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Vencidas</p>
                    <div class="w-10 h-10 rounded-lg bg-violet-100 flex items-center justify-center group-hover:bg-violet-200 transition-colors">
                        <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <p class="text-4xl font-bold text-slate-900">{{ $suscripcionesVencidas }}</p>
                <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-violet-500 to-violet-300 rounded-b-xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
            </div>
        </div>

        <!-- Revenue Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Ingresos por Tarifas -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 hover:shadow-lg transition-all duration-300">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-600 uppercase tracking-wide">Ingresos por Tarifas</h3>
                        <p class="text-xs text-slate-500 mt-1">Acumulado por transacciones</p>
                    </div>
                    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-emerald-100 to-emerald-50 flex items-center justify-center">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <p class="text-4xl font-bold text-slate-900">
                    ${{ number_format($ingresosTotales, 2, ',', '.') }}
                </p>
                <div class="mt-4 flex items-center gap-2">
                    <div class="flex-1 h-2 bg-emerald-100 rounded-full overflow-hidden">
                        <div class="h-full w-3/4 bg-gradient-to-r from-emerald-500 to-emerald-400 rounded-full"></div>
                    </div>
                    <span class="text-xs text-slate-600 font-medium">75%</span>
                </div>
            </div>

            <!-- Ventas Totales -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 hover:shadow-lg transition-all duration-300">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-600 uppercase tracking-wide">Ventas Totales en Sistema</h3>
                        <p class="text-xs text-slate-500 mt-1">De todas las empresas</p>
                    </div>
                    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-blue-100 to-blue-50 flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                </div>
                <p class="text-4xl font-bold text-slate-900">
                    ${{ number_format($ventasTotales, 2, ',', '.') }}
                </p>
                <div class="mt-4 flex items-center gap-2">
                    <div class="flex-1 h-2 bg-blue-100 rounded-full overflow-hidden">
                        <div class="h-full w-4/5 bg-gradient-to-r from-blue-500 to-blue-400 rounded-full"></div>
                    </div>
                    <span class="text-xs text-slate-600 font-medium">80%</span>
                </div>
            </div>
        </div>

        <!-- Recent Companies Section -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-200">
                <h2 class="text-lg font-bold text-slate-900">Últimas Empresas Registradas</h2>
                <p class="text-sm text-slate-600 mt-1">Control y monitoreo de nuevos registros</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Empresa</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Plan</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Suscripción</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-700 uppercase tracking-wider">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($ultimasEmpresas as $empresa)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-bold text-sm">
                                        {{ substr($empresa->razon_social, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-slate-900">{{ $empresa->razon_social }}</p>
                                        <p class="text-xs text-slate-500">ID: {{ $empresa->id }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-medium text-slate-900">{{ $empresa->plan?->nombre ?? '—' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $empresa->estado === 'activa' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                    <span class="w-2 h-2 rounded-full mr-2 {{ $empresa->estado === 'activa' ? 'bg-emerald-600' : 'bg-red-600' }}"></span>
                                    {{ ucfirst($empresa->estado) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $empresa->estado_suscripcion === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    <span class="w-2 h-2 rounded-full mr-2 {{ $empresa->estado_suscripcion === 'active' ? 'bg-emerald-600' : 'bg-amber-600' }}"></span>
                                    {{ ucfirst($empresa->estado_suscripcion) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('super-admin.empresas.show', $empresa) }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium text-blue-600 hover:bg-blue-50 transition-colors">
                                    Ver
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                    <p class="text-slate-600 font-medium">No hay empresas registradas aún</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-8 flex flex-col sm:flex-row gap-4">
            <a href="{{ route('super-admin.empresas.index') }}" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold rounded-lg hover:shadow-lg hover:shadow-blue-500/25 transition-all duration-300 transform hover:-translate-y-0.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                Ver Todas las Empresas
            </a>
            <a href="{{ route('panel') }}" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-6 py-3 bg-slate-100 text-slate-900 font-semibold rounded-lg hover:bg-slate-200 transition-all duration-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Volver al Panel
            </a>
        </div>
    </div>
</div>
@endsection
