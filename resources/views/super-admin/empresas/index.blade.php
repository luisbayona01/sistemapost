@extends('layouts.app')

@section('title', 'Gestión de Empresas - Modo Dios')

@section('content')
    <div class="min-h-screen bg-slate-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Gestión de Empresas</h1>
                    <p class="text-slate-600 mt-1">Administra todos los tenants del sistema</p>
                </div>
                <a href="{{ route('root.empresas.create') }}"
                    class="inline-flex items-center justify-center px-5 py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-200">
                    <i class="fas fa-plus mr-2 text-sm"></i>
                    Nueva Empresa
                </a>
            </div>

            <!-- Success/Error Alerts -->
            @if(session('success'))
                <div
                    class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center gap-3 text-emerald-800 animate-fade-in">
                    <i class="fas fa-check-circle text-emerald-500"></i>
                    <p class="text-sm font-medium">{{ session('success') }}</p>
                </div>
            @endif

            <!-- Table Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 border-b border-slate-100">
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Empresa /
                                    Propietario</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Plan /
                                    Moneda</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Métricas
                                </th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Estado</th>
                                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest text-right">
                                    Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($empresas as $empresa)
                                <tr class="hover:bg-slate-50/50 transition-colors group">
                                    <td class="px-6 py-5">
                                        <div class="flex items-center gap-4">
                                            <div
                                                class="w-12 h-12 rounded-xl bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center text-slate-600 font-bold group-hover:from-blue-500 group-hover:to-blue-600 group-hover:text-white transition-all duration-300">
                                                {{ substr($empresa->nombre, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-slate-900">{{ $empresa->nombre }}</p>
                                                <p class="text-xs text-slate-500 mt-0.5">{{ $empresa->propietario }}</p>
                                                <div class="flex items-center gap-2 mt-1">
                                                    <span
                                                        class="text-[10px] bg-slate-100 px-1.5 py-0.5 rounded text-slate-500 font-mono">{{ $empresa->slug }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex flex-col gap-1">
                                            <span
                                                class="text-sm font-semibold text-slate-700">{{ $empresa->plan?->nombre ?? 'N/A' }}</span>
                                            <span class="text-xs text-slate-500">{{ $empresa->moneda?->nombre }}
                                                ({{ $empresa->moneda?->simbolo }})</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex gap-4">
                                            <div class="flex flex-col">
                                                <span class="text-xs text-slate-400 font-medium">Usuarios</span>
                                                <span
                                                    class="text-sm font-bold text-slate-700">{{ $empresa->users_count }}</span>
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-xs text-slate-400 font-medium">Ventas</span>
                                                <span
                                                    class="text-sm font-bold text-slate-700">{{ $empresa->ventas_count }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex flex-col gap-1.5">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $empresa->estado === 'activa' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                                {{ $empresa->estado }}
                                            </span>
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $empresa->estado_suscripcion === 'active' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700' }}">
                                                {{ $empresa->estado_suscripcion }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <form action="{{ route('root.impersonate-empresa', $empresa) }}" method="POST"
                                                class="inline">
                                                @csrf
                                                <button type="submit"
                                                    class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 text-blue-600 rounded-lg text-[10px] font-black uppercase hover:bg-blue-600 hover:text-white transition-all shadow-sm"
                                                    title="Entrar como administrador de {{ $empresa->nombre }}">
                                                    <i class="fas fa-user-secret"></i>
                                                    Entrar
                                                </button>
                                            </form>

                                            <a href="{{ route('root.empresas.show', $empresa) }}"
                                                class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all"
                                                title="Ver Detalle">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            @if($empresa->estado === 'activa')
                                                <form action="{{ route('root.empresas.suspend', $empresa) }}" method="POST"
                                                    class="inline">
                                                    @csrf
                                                    <button type="submit"
                                                        class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all"
                                                        title="Suspender">
                                                        <i class="fas fa-ban"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('root.empresas.activate', $empresa) }}" method="POST"
                                                    class="inline">
                                                    @csrf
                                                    <button type="submit"
                                                        class="p-2 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all"
                                                        title="Activar">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                        <div class="flex flex-col items-center gap-3">
                                            <i class="fas fa-building text-4xl opacity-20"></i>
                                            <p class="font-medium text-lg">No hay empresas registradas</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($empresas->hasPages())
                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">
                        {{ $empresas->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection