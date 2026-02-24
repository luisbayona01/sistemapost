@extends('layouts.app')

@section('title', 'Nueva Empresa - Modo Dios')

@section('content')
    <div class="min-h-screen bg-slate-50 py-8">
        <div class="max-w-4xl mx-auto px-4">
            <!-- Header -->
            <div class="mb-8">
                <a href="{{ route('root.empresas.index') }}"
                    class="text-sm font-bold text-blue-600 hover:text-blue-800 transition-colors flex items-center gap-2 mb-4">
                    <i class="fas fa-arrow-left"></i>
                    Volver al listado
                </a>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight">Nueva Empresa (Tenant)</h1>
                <p class="text-slate-500 font-medium">Crea un nuevo cliente y su cuenta administradora en un solo paso.</p>
            </div>

            @if(session('error'))
                <div
                    class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm font-medium flex items-center gap-3">
                    <i class="fas fa-exclamation-circle text-red-500"></i>
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('root.empresas.store') }}" method="POST">
                @csrf

                <div class="space-y-6">
                    <!-- Empresa Info -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center">
                                <i class="fas fa-building text-sm"></i>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900">Configuración del Negocio</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="col-span-1">
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Nombre
                                    Comercial</label>
                                <input type="text" name="nombre" value="{{ old('nombre') }}"
                                    class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none text-sm"
                                    placeholder="Ej: Cinepolis, Mascotas Peluditas..." required>
                                @error('nombre') <p class="text-red-500 text-[10px] mt-1 uppercase font-bold">{{ $message }}
                                </p> @enderror
                            </div>

                            <div class="col-span-1">
                                <label
                                    class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Identificador
                                    (Slug)</label>
                                <input type="text" name="slug" value="{{ old('slug') }}"
                                    class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none text-sm font-mono"
                                    placeholder="ej-identificador" required>
                                <p class="text-[10px] text-slate-400 mt-1 uppercase font-semibold">Usado para subdominios y
                                    URLs únicas</p>
                            </div>

                            <div class="col-span-1">
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">NIT /
                                    RUC / ID Fiscal</label>
                                <input type="text" name="ruc" value="{{ old('ruc') }}"
                                    class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none text-sm"
                                    placeholder="900.000.000-1" required>
                            </div>

                            <div class="col-span-1">
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Correo
                                    de Facturación</label>
                                <input type="email" name="correo_empresa" value="{{ old('correo_empresa') }}"
                                    class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none text-sm"
                                    placeholder="facturacion@empresa.com" required>
                            </div>

                            <div class="col-span-1">
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Plan
                                    SaaS</label>
                                <select name="plan_id"
                                    class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none text-sm appearance-none bg-white">
                                    @foreach($planes as $plan)
                                        <option value="{{ $plan->id }}">{{ $plan->nombre }} -
                                            ${{ number_format($plan->precio_mensual_cop, 0) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-span-1">
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Moneda
                                    por Defecto</label>
                                <select name="moneda_id"
                                    class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none text-sm appearance-none bg-white">
                                    @foreach($monedas as $moneda)
                                        <option value="{{ $moneda->id }}" @selected($moneda->codigo === 'COP')>
                                            {{ $moneda->nombre }} ({{ $moneda->simbolo }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Admin User Info -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center">
                                <i class="fas fa-user-shield text-sm"></i>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900">Cuenta Administradora Maestro</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Nombre
                                    Completo del Admin</label>
                                <input type="text" name="admin_name" value="{{ old('admin_name') }}"
                                    class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none text-sm"
                                    placeholder="Ej: Juan Pérez" required>
                            </div>

                            <div class="col-span-1">
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Nombre
                                    de Usuario (Login)</label>
                                <input type="text" name="admin_username" value="{{ old('admin_username') }}"
                                    class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none text-sm"
                                    placeholder="admin_negocio" required>
                            </div>

                            <div class="col-span-1">
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Correo
                                    Personal Admin</label>
                                <input type="email" name="admin_email" value="{{ old('admin_email') }}"
                                    class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none text-sm"
                                    placeholder="admin@correo.com" required>
                            </div>

                            <div class="col-span-2">
                                <label
                                    class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2">Contraseña
                                    Inicial</label>
                                <div class="relative">
                                    <input type="password" name="admin_password" id="admin_password"
                                        class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none text-sm pr-12"
                                        placeholder="••••••••" required>
                                    <button type="button" onclick="togglePass()"
                                        class="absolute right-4 top-3 text-slate-400 hover:text-blue-600 transition-colors">
                                        <i class="fas fa-eye" id="eye-icon"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit -->
                    <button type="submit"
                        class="w-full py-4 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-black rounded-2xl hover:shadow-2xl hover:shadow-blue-500/40 transition-all duration-300 transform hover:-translate-y-1">
                        CREAR EMPRESA Y ACTIVAR ACCESO
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function togglePass() {
            const input = document.getElementById('admin_password');
            const icon = document.getElementById('eye-icon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>

@endsection