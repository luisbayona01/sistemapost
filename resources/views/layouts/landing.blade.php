<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description"
        content="Software profesional para cines y eventos. Punto de venta, auditoría y control total.">

    <title>@yield('title', 'CinemaPOS')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>

<body class="font-sans antialiased text-gray-900 bg-white">

    <!-- Navbar Pública -->
    <header class="fixed w-full z-50 bg-white/95 backdrop-blur-md border-b border-gray-100 transition-all duration-300"
        id="navbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Branding -->
                <div class="flex-shrink-0 flex items-center gap-3">
                    <a href="{{ route('landing') }}" class="flex items-center gap-2 group">
                        <div
                            class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center transform group-hover:rotate-6 transition-transform">
                            <i class="fas fa-film text-white text-xl"></i>
                        </div>
                        <span
                            class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-700 to-blue-500">
                            CinemaPOS
                        </span>
                    </a>
                </div>

                <!-- Desktop Navigation -->
                <nav class="hidden md:flex space-x-8 items-center">
                    <a href="#features"
                        class="text-gray-600 hover:text-blue-600 font-medium transition-colors">Características</a>
                    <a href="#planes" class="text-gray-600 hover:text-blue-600 font-medium transition-colors">Planes</a>

                    <div class="flex items-center gap-4 ml-4">
                        <a href="{{ route('login.index') }}"
                            class="text-gray-900 hover:text-blue-600 font-semibold px-4 py-2 transition-colors">
                            Iniciar Sesión
                        </a>
                        <a href="{{ route('register.create') }}"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-full font-bold shadow-lg hover:shadow-blue-500/30 transition-all transform hover:-translate-y-0.5">
                            Prueba Gratis
                        </a>
                    </div>
                </nav>

                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center">
                    <button type="button" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')"
                        class="text-gray-500 hover:text-gray-900 p-2">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-white border-b border-gray-100">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                <a href="#features"
                    class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded-md">Características</a>
                <a href="#planes"
                    class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded-md">Planes</a>
                <a href="{{ route('login.index') }}"
                    class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50 rounded-md">Iniciar
                    Sesión</a>
                <a href="{{ route('register.create') }}"
                    class="block w-full text-center mt-4 bg-blue-600 text-white px-4 py-3 rounded-lg font-bold">Empezar
                    Gratis</a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="pt-20">
        @yield('content')
    </main>

    @stack('js')
</body>

</html>
