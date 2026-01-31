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
    @stack('css')
</head>

<body class="bg-gray-50 text-gray-900 antialiased">
    <div class="flex flex-col min-h-screen">
        <!-- Header -->
        @include('layouts.include.navigation-header')

        <div class="flex flex-1 overflow-hidden">
            <!-- Sidebar -->
            @include('layouts.include.navigation-menu')

            <!-- Main Content -->
            <main class="flex-1 flex flex-col md:ml-64 overflow-auto">
                <!-- Alerts -->
                @include('layouts.partials.alert')

                <!-- Page Content -->
                <div class="flex-1 overflow-auto">
                    @yield('content')
                </div>

                <!-- Footer -->
                @include('layouts.include.footer')
            </main>
        </div>
    </div>

    <script src="{{ asset('js/scripts.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const notificationIcon = document.getElementById('notificationsDropdown');

            if (notificationIcon) {
                notificationIcon.addEventListener('click', function() {
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

            // Sidebar Toggle
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('layoutSidenav_nav');

            if (sidebarToggle && sidebar) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('hidden');
                });
            }
        });
    </script>

    @stack('js')
</body>

</html>
