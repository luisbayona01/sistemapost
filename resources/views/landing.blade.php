@extends('layouts.landing')

@section('title', 'CinemaPOS - Software Profesional para Cines y Eventos')

@section('content')
    <!-- Hero Section -->
    <div
        class="min-h-screen bg-gradient-to-b from-blue-600 to-blue-800 flex items-center justify-center px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto text-center">
            <div class="mb-8">
                <h1 class="text-5xl sm:text-6xl font-bold text-white mb-6 leading-tight">
                    CinemaPOS
                </h1>
                <p class="text-xl sm:text-2xl text-blue-100 mb-4">
                    Software Profesional para Cines y Eventos
                </p>
                <p class="text-lg text-blue-100 mb-8 max-w-2xl mx-auto">
                    Solución completa de punto de venta multiempresa con soporte para múltiples cajas,
                    integración Stripe y auditoría empresarial.
                </p>
            </div>

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register.create', ['plan' => 'basic']) }}"
                    class="inline-block bg-white text-blue-600 px-8 py-4 rounded-lg font-semibold hover:bg-blue-50 transition duration-300 transform hover:scale-105">
                    Comenzar Ahora
                </a>
                <a href="#features"
                    class="inline-block bg-transparent border-2 border-white text-white px-8 py-4 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition duration-300">
                    Conocer Más
                </a>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <section id="features" class="py-16 sm:py-24 bg-gray-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Características Principales</h2>
                <p class="text-lg text-gray-600">Todo lo que necesitas para administrar tu negocio</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-white rounded-lg shadow-lg p-8 hover:shadow-xl transition duration-300">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">POS Especializado</h3>
                    <p class="text-gray-600">Sistema de punto de venta optimizado para cines y eventos con interfaz
                        intuitiva y rápida.</p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-white rounded-lg shadow-lg p-8 hover:shadow-xl transition duration-300">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Caja Auditada</h3>
                    <p class="text-gray-600">Sistema completo de cajas con auditoría, apertura/cierre y control total de
                        movimientos.</p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-white rounded-lg shadow-lg p-8 hover:shadow-xl transition duration-300">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M7 15h10M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Pagos Stripe</h3>
                    <p class="text-gray-600">Integración segura con Stripe para procesar pagos con tarjeta en tiempo real.
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="bg-white rounded-lg shadow-lg p-8 hover:shadow-xl transition duration-300">
                    <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 12H9m6 0H9m6 0a12 12 0 01-12 0M9 12a12 12 0 0112 0"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Multiempresa</h3>
                    <p class="text-gray-600">Administra múltiples sucursales desde una sola plataforma con datos totalmente
                        segregados.</p>
                </div>

                <!-- Feature 5 -->
                <div class="bg-white rounded-lg shadow-lg p-8 hover:shadow-xl transition duration-300">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Reportes Completos</h3>
                    <p class="text-gray-600">Acceso a reportes detallados de ventas, inventario, cajas y métricas en tiempo
                        real.</p>
                </div>

                <!-- Feature 6 -->
                <div class="bg-white rounded-lg shadow-lg p-8 hover:shadow-xl transition duration-300">
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Seguridad Empresarial</h3>
                    <p class="text-gray-600">Roles y permisos granulares, auditoría completa y cifrado de datos sensibles.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section class="py-16 sm:py-24 bg-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Planes Sencillos y Transparentes</h2>
                <p class="text-lg text-gray-600">Elige el plan perfecto para tu negocio. Sin sorpresas ocultas.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                @foreach($planes as $plan)
                    <div
                        class="border-2 {{ $plan->nombre === 'Profesional' ? 'border-blue-600' : 'border-gray-200' }} rounded-lg p-8">
                        <!-- Badge de recomendación -->
                        @if($plan->nombre === 'Profesional')
                            <div class="mb-4">
                                <span class="bg-blue-600 text-white px-3 py-1 rounded-full text-sm font-semibold">Recomendado</span>
                            </div>
                        @endif

                        <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $plan->nombre }}</h3>
                        <p class="text-gray-600 text-sm mb-6">{{ $plan->descripcion }}</p>

                        <div class="mb-6">
                            <span class="text-4xl font-bold text-gray-900">
                                ${{ number_format($plan->precio_mensual_cop, 0, '', '.') }}
                            </span>
                            <span class="text-gray-600">/mes</span>
                        </div>

                        <p class="text-sm text-gray-600 mb-6">
                            Trial de <strong>{{ $plan->dias_trial }} días</strong> gratis
                        </p>

                        <ul class="space-y-3 mb-8">
                            @foreach($plan->getCaracteristicasArray() as $caracteristica)
                                <li class="flex items-center text-gray-700">
                                    <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $caracteristica }}
                                </li>
                            @endforeach
                        </ul>

                        <a href="{{ route('register.create', ['plan' => $plan->id]) }}"
                            class="w-full inline-block text-center {{ $plan->nombre === 'Profesional' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-900 hover:bg-gray-200' }} px-6 py-3 rounded-lg font-semibold transition duration-300">
                            Comenzar Ahora
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 sm:py-24 bg-blue-600">
        <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
            <h2 class="text-4xl font-bold text-white mb-4">¿Listo para Transformar tu Negocio?</h2>
            <p class="text-lg text-blue-100 mb-8">
                Únete a cientos de empresas que ya confían en CinemaPOS para administrar sus ventas.
            </p>
            <a href="{{ route('register.create') }}"
                class="inline-block bg-white text-blue-600 px-8 py-4 rounded-lg font-semibold hover:bg-blue-50 transition duration-300 transform hover:scale-105">
                Registrate Gratis Hoy
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <div>
                    <h4 class="font-semibold mb-4">CinemaPOS</h4>
                    <p class="text-gray-400">Software profesional para punto de venta.</p>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Producto</h4>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li><a href="#features" class="hover:text-white">Características</a></li>
                        <li><a href="#" class="hover:text-white">Planes</a></li>
                        <li><a href="#" class="hover:text-white">Seguridad</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Legal</h4>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li><a href="#" class="hover:text-white">Términos</a></li>
                        <li><a href="#" class="hover:text-white">Privacidad</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Contacto</h4>
                    <p class="text-gray-400 text-sm">soporte@cinemapos.com</p>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-8 text-center text-gray-400 text-sm">
                <p>&copy; {{ now()->year }} CinemaPOS. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>
@endsection
