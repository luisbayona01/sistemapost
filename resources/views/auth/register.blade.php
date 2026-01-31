@extends('layouts.app')

@section('title', 'Registrar Empresa - CinemaPOS')

@section('content')
<div class="min-h-screen bg-gray-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl w-full bg-white rounded-lg shadow-lg p-8">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Crear tu Empresa</h1>
            <p class="text-gray-600">Completa el formulario para comenzar tu período de prueba gratuito</p>
        </div>

        @if ($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
            <p class="font-semibold mb-2">Errores en el formulario:</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('register.store') }}" class="space-y-6">
            @csrf

            <!-- Plan Selection -->
            <div>
                <label for="plan_id" class="block text-sm font-medium text-gray-700 mb-2">Plan *</label>
                <select id="plan_id" name="plan_id" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('plan_id') border-red-500 @enderror">
                    <option value="">-- Selecciona un plan --</option>
                    @foreach($planes as $plan)
                        <option value="{{ $plan->id }}" {{ $planSelected == $plan->id || old('plan_id') == $plan->id ? 'selected' : '' }}>
                            {{ $plan->nombre }} - ${{ number_format($plan->precio_mensual_cop, 0, '', '.') }}/mes
                        </option>
                    @endforeach
                </select>
                @error('plan_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Empresa Section -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Información de la Empresa</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Empresa Nombre -->
                    <div class="md:col-span-2">
                        <label for="empresa_nombre" class="block text-sm font-medium text-gray-700 mb-2">Nombre de la Empresa *</label>
                        <input type="text" id="empresa_nombre" name="empresa_nombre"
                               value="{{ old('empresa_nombre') }}" required
                               placeholder="Cine Central S.A."
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('empresa_nombre') border-red-500 @enderror">
                        @error('empresa_nombre')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- NIT -->
                    <div>
                        <label for="nit" class="block text-sm font-medium text-gray-700 mb-2">NIT / Documento *</label>
                        <input type="text" id="nit" name="nit"
                               value="{{ old('nit') }}" required
                               placeholder="900123456"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nit') border-red-500 @enderror">
                        @error('nit')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Moneda -->
                    <div>
                        <label for="moneda_id" class="block text-sm font-medium text-gray-700 mb-2">Moneda *</label>
                        <select id="moneda_id" name="moneda_id" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('moneda_id') border-red-500 @enderror">
                            <option value="">-- Selecciona una moneda --</option>
                            @foreach($monedas as $moneda)
                                <option value="{{ $moneda->id }}" {{ old('moneda_id') == $moneda->id ? 'selected' : '' }}>
                                    {{ $moneda->nombre }} ({{ $moneda->simbolo }})
                                </option>
                            @endforeach
                        </select>
                        @error('moneda_id')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email Empresa -->
                    <div>
                        <label for="empresa_email" class="block text-sm font-medium text-gray-700 mb-2">Email de la Empresa</label>
                        <input type="email" id="empresa_email" name="empresa_email"
                               value="{{ old('empresa_email') }}"
                               placeholder="contacto@empresa.com"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('empresa_email') border-red-500 @enderror">
                        @error('empresa_email')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Teléfono -->
                    <div>
                        <label for="telefono" class="block text-sm font-medium text-gray-700 mb-2">Teléfono</label>
                        <input type="tel" id="telefono" name="telefono"
                               value="{{ old('telefono') }}"
                               placeholder="+57 1 2345678"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('telefono') border-red-500 @enderror">
                        @error('telefono')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Contact Person Section -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Información del Administrador</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Nombre Contacto -->
                    <div class="md:col-span-2">
                        <label for="nombre_contacto" class="block text-sm font-medium text-gray-700 mb-2">Nombre Completo *</label>
                        <input type="text" id="nombre_contacto" name="nombre_contacto"
                               value="{{ old('nombre_contacto') }}" required
                               placeholder="Juan Pérez García"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nombre_contacto') border-red-500 @enderror">
                        @error('nombre_contacto')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                        <input type="email" id="email" name="email"
                               value="{{ old('email') }}" required
                               placeholder="tu@email.com"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Contraseña -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Contraseña *</label>
                        <input type="password" id="password" name="password" required
                               placeholder="••••••••"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('password') border-red-500 @enderror">
                        <p class="text-gray-500 text-xs mt-1">Mínimo 8 caracteres, incluye mayúsculas, minúsculas y números</p>
                        @error('password')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirmar Contraseña -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirmar Contraseña *</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                               placeholder="••••••••"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('password_confirmation') border-red-500 @enderror">
                        @error('password_confirmation')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Terms -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-sm text-gray-700">
                    Al registrarte, aceptas nuestros
                    <a href="#" class="text-blue-600 hover:underline">Términos de Servicio</a> y
                    <a href="#" class="text-blue-600 hover:underline">Política de Privacidad</a>.
                    Incluye un período de prueba gratuito de 14 días.
                </p>
            </div>

            <!-- Submit -->
            <div class="flex gap-4">
                <button type="submit"
                        class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition duration-300">
                    Crear Empresa
                </button>
                <a href="{{ route('landing') }}"
                   class="flex-1 text-center bg-gray-200 text-gray-900 px-6 py-3 rounded-lg font-semibold hover:bg-gray-300 transition duration-300">
                    Cancelar
                </a>
            </div>

            <!-- Login Link -->
            <div class="text-center">
                <p class="text-gray-600">
                    ¿Ya tienes una cuenta?
                    <a href="{{ route('login.index') }}" class="text-blue-600 hover:underline font-semibold">Inicia sesión aquí</a>
                </p>
            </div>
        </form>
    </div>
</div>
@endsection
