<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Sistema de punto de venta profesional" />
    <meta name="author" content="Punto de Venta" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Sistema de ventas - @yield('title')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('css-datatable')
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('css')
</head>

<body class="bg-gray-50 text-gray-900 antialiased" x-data="{ sidebarOpen: false }">
    <div class="flex flex-col min-h-screen">
        <!-- Header -->
        @include('layouts.include.navigation-header')

        <div class="flex flex-1 relative overflow-hidden">
            <!-- Sidebar -->
            @include('layouts.include.navigation-menu')

            <!-- Main Content -->
            <main class="flex-1 transition-all duration-300 min-w-0 overflow-x-hidden ml-0"
                :class="sidebarOpen ? 'md:ml-64' : 'ml-0'">
                <!-- Alerts -->
                @include('layouts.partials.alert')

                <!-- Page Content -->
                <div class="p-0">
                    @yield('content')
                </div>

                <!-- Footer -->
                @include('layouts.include.footer')
            </main>
        </div>

        {{-- Botón flotante de Volver (UX mejorada) --}}
        @if(!request()->routeIs('panel'))
            <button onclick="window.history.back()"
                class="fixed bottom-6 left-6 z-40 inline-flex items-center gap-2 px-4 py-3 bg-slate-800 hover:bg-slate-700 text-white font-medium rounded-full shadow-lg hover:shadow-xl transition-all duration-200 group"
                title="Volver a la página anterior">
                <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform duration-200"></i>
                <span class="hidden md:inline">Volver</span>
            </button>
        @endif
    </div>

    <script src="{{ asset('js/scripts.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const notificationIcon = document.getElementById('notificationsDropdown');

            if (notificationIcon) {
                notificationIcon.addEventListener('click', function () {
                    fetch("{{ route('notifications.markAsRead') }}", {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify({})
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const badge = notificationIcon.querySelector('.bg-red-500');
                                if (badge) badge.remove();
                            }
                        })
                        .catch(error => console.error('Error al marcar notificaciones:', error));
                });
            }
        });
    </script>

    @stack('js')
</body>

</html>