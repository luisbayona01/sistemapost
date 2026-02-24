@extends('layouts.app')

@section('title', 'Control Maestro - Modo Dios')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-blue-900 text-white">
        <!-- Header Section -->
        <div class="border-b border-white/10 bg-black/20 backdrop-blur-md sticky top-0 z-40">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-3xl font-black tracking-tight">Panel Central <span class="text-blue-400">Modo
                                Dios</span></h1>
                        <p class="text-slate-400 text-sm mt-1">Supervisión infraestructura SaaS CinemaPOS</p>
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ route('root.empresas.create') }}"
                            class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-bold transition-all shadow-lg shadow-blue-600/20 flex items-center gap-2">
                            <i class="fas fa-plus-circle"></i>
                            Nuevo Tenant
                        </a>
                        <a href="/"
                            class="px-5 py-2.5 bg-white/10 hover:bg-white/20 text-white rounded-xl font-bold transition-all backdrop-blur-sm">
                            Sitio Público
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

            <!-- SaaS Global Metrics -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Tenans -->
                <div
                    class="bg-white/5 border border-white/10 p-6 rounded-3xl backdrop-blur-xl group hover:border-blue-500/50 transition-all cursor-default">
                    <div class="flex justify-between items-start mb-4">
                        <span class="text-slate-400 text-xs font-bold uppercase tracking-widest">Total Clientes</span>
                        <div
                            class="w-10 h-10 rounded-xl bg-blue-500/20 text-blue-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i class="fas fa-building"></i>
                        </div>
                    </div>
                    <div class="flex items-end gap-2">
                        <span class="text-4xl font-black">{{ $totalEmpresas }}</span>
                        <span class="text-emerald-400 text-xs font-bold mb-1"><i
                                class="fas fa-arrow-up mr-1"></i>+{{ $ultimasEmpresas->count() }}</span>
                    </div>
                </div>

                <!-- Active Subs -->
                <div
                    class="bg-white/5 border border-white/10 p-6 rounded-3xl backdrop-blur-xl group hover:border-emerald-500/50 transition-all cursor-default">
                    <div class="flex justify-between items-start mb-4">
                        <span class="text-slate-400 text-xs font-bold uppercase tracking-widest">Suscripciones</span>
                        <div
                            class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i class="fas fa-check-double"></i>
                        </div>
                    </div>
                    <div class="flex items-end gap-2">
                        <span class="text-4xl font-black">{{ $empresasActivas }}</span>
                        <span class="text-slate-400 text-xs font-bold mb-1">Activas</span>
                    </div>
                </div>

                <!-- Trialing -->
                <div
                    class="bg-white/5 border border-white/10 p-6 rounded-3xl backdrop-blur-xl group hover:border-amber-500/50 transition-all cursor-default">
                    <div class="flex justify-between items-start mb-4">
                        <span class="text-slate-400 text-xs font-bold uppercase tracking-widest">En Trial</span>
                        <div
                            class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i class="fas fa-hourglass-half"></i>
                        </div>
                    </div>
                    <div class="flex items-end gap-2">
                        <span class="text-4xl font-black">{{ $empresasEnTrial }}</span>
                        <span class="text-amber-400 text-xs font-bold mb-1">Pendientes</span>
                    </div>
                </div>

                <!-- Total Sales -->
                <div
                    class="bg-white/5 border border-white/10 p-6 rounded-3xl backdrop-blur-xl group hover:border-violet-500/50 transition-all cursor-default">
                    <div class="flex justify-between items-start mb-4">
                        <span class="text-slate-400 text-xs font-bold uppercase tracking-widest">Ventas Globales</span>
                        <div
                            class="w-10 h-10 rounded-xl bg-violet-500/20 text-violet-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i class="fas fa-wallet"></i>
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-2xl font-black">${{ number_format($ventasTotales, 0, ',', '.') }}</span>
                        <span class="text-xs text-slate-500 mt-1">Transacciones procesadas</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Latest Tenants List -->
                <div class="lg:col-span-2 bg-white/5 border border-white/10 rounded-3xl backdrop-blur-xl overflow-hidden">
                    <div class="px-8 py-6 border-b border-white/5 flex items-center justify-between">
                        <h2 class="text-xl font-bold">Inscripciones Recientes</h2>
                        <a href="{{ route('root.empresas.index') }}"
                            class="text-blue-400 text-sm font-bold hover:underline">Ver todas</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-white/5 text-slate-400 text-[10px] uppercase font-bold tracking-widest">
                                <tr>
                                    <th class="px-8 py-4">Empresa</th>
                                    <th class="px-8 py-4">Propietario</th>
                                    <th class="px-8 py-4 text-center">Estado</th>
                                    <th class="px-8 py-4 text-right">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5 text-sm">
                                @foreach($ultimasEmpresas as $empresa)
                                    <tr class="hover:bg-white/5 transition-colors">
                                        <td class="px-8 py-5">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center font-bold text-xs">
                                                    {{ substr($empresa->nombre, 0, 1) }}
                                                </div>
                                                <span class="font-bold">{{ $empresa->nombre }}</span>
                                            </div>
                                        </td>
                                        <td class="px-8 py-5 text-slate-400">
                                            {{ $empresa->propietario }}
                                        </td>
                                        <td class="px-8 py-5 text-center">
                                            <span
                                                class="px-2 py-1 rounded-md text-[10px] font-black uppercase {{ $empresa->estado === 'activa' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-red-500/20 text-red-400' }}">
                                                {{ $empresa->estado }}
                                            </span>
                                        </td>
                                        <td class="px-8 py-5 text-right">
                                            <a href="{{ route('root.empresas.show', $empresa) }}"
                                                class="p-2 hover:bg-white/10 rounded-lg transition-all inline-block">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Quick Actions / System Health -->
                <div class="space-y-6">
                    <!-- Health Card -->
                    <div
                        class="bg-gradient-to-br from-emerald-600 to-teal-700 p-8 rounded-3xl shadow-xl shadow-emerald-900/20 relative overflow-hidden group">
                        <div class="relative z-10">
                            <h3 class="text-lg font-black mb-2 flex items-center gap-2">
                                <span class="w-3 h-3 bg-white rounded-full animate-pulse"></span>
                                System Health
                            </h3>
                            <p class="text-white/80 text-xs mb-6 font-medium">Todos los servicios operando normalmente en
                                AWS/GCP.</p>
                            <div class="space-y-3">
                                <div class="flex justify-between text-[10px] font-bold uppercase">
                                    <span>Database Latency</span>
                                    <span>12ms</span>
                                </div>
                                <div class="h-1.5 bg-black/20 rounded-full overflow-hidden">
                                    <div class="h-full w-4/5 bg-white rounded-full"></div>
                                </div>
                            </div>
                        </div>
                        <i
                            class="fas fa-heartbeat absolute -bottom-4 -right-4 text-7xl text-black/10 group-hover:scale-110 transition-transform"></i>
                    </div>

                    <!-- Shortcut List -->
                    <div class="bg-white/5 border border-white/10 rounded-3xl p-6">
                        <h4
                            class="text-slate-400 text-[10px] font-black uppercase tracking-widest mb-6 border-b border-white/5 pb-3">
                            Herramientas Pro</h4>
                        <div class="grid grid-cols-2 gap-3">
                            <a href="{{ route('root.activity-log.index') }}"
                                class="p-4 bg-white/5 rounded-2xl hover:bg-white/10 transition-all flex flex-col items-center gap-2 border border-white/5">
                                <i class="fas fa-clipboard-list text-blue-400"></i>
                                <span class="text-[10px] font-bold">Logs</span>
                            </a>
                            <a href="{{ route('root.audit.index') }}"
                                class="p-4 bg-white/5 rounded-2xl hover:bg-white/10 transition-all flex flex-col items-center gap-2 border border-white/5">
                                <i class="fas fa-vault text-violet-400"></i>
                                <span class="text-[10px] font-bold">Audit Vault</span>
                            </a>
                            <form action="{{ route('root.backup.run') }}" method="GET" class="contents">
                                <button type="submit"
                                    class="p-4 bg-white/5 rounded-2xl hover:bg-white/10 transition-all flex flex-col items-center gap-2 border border-white/5">
                                    <i class="fas fa-database text-emerald-400"></i>
                                    <span class="text-[10px] font-bold">Backup</span>
                                </button>
                            </form>
                            <a href="#"
                                class="p-4 bg-white/5 rounded-2xl opacity-50 cursor-not-allowed flex flex-col items-center gap-2 border border-white/5">
                                <i class="fas fa-cog text-slate-400"></i>
                                <span class="text-[10px] font-bold">Settings</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection