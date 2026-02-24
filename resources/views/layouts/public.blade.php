<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Cartelera') - {{ $tenant->nombre }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        :root {
            --primary:
                {{ $tenant->primary_color ?? '#000000' }}
            ;
            --secondary:
                {{ $tenant->secondary_color ?? '#ffffff' }}
            ;
        }

        body {
            font-family: 'Outfit', sans-serif;
        }

        .bg-primary {
            background-color: var(--primary);
        }

        .text-primary {
            color: var(--primary);
        }

        .border-primary {
            border-color: var(--primary);
        }

        {!! $tenant->custom_css !!}
    </style>
    @stack('css')
</head>

<body class="bg-slate-50 min-h-screen">

    <!-- Header -->
    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <div class="flex-shrink-0 flex items-center gap-3">
                    <img class="h-12 w-auto" src="{{ $tenant->logo_url }}" alt="{{ $tenant->nombre }}">
                    <span class="text-xl font-black tracking-tight text-slate-800">{{ $tenant->nombre }}</span>
                </div>
                <div class="hidden md:flex items-center gap-6">
                    <a href="{{ route('public.cartelera') }}"
                        class="text-sm font-bold text-slate-600 hover:text-primary transition-colors">Cartelera</a>
                    <a href="#promos"
                        class="text-sm font-bold text-slate-600 hover:text-primary transition-colors">Promociones</a>
                    <a href="{{ route('login.index') }}"
                        class="bg-primary text-white px-6 py-2.5 rounded-full text-sm font-bold shadow-lg shadow-blue-500/20 hover:scale-105 transition-all">Ingresar</a>
                </div>
            </div>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <footer class="bg-slate-900 text-white py-12 mt-20">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <img class="h-10 w-auto mx-auto mb-6 opacity-50 grayscale" src="{{ $tenant->logo_url }}" alt="Logo">
            <p class="text-slate-500 text-sm">© {{ date('Y') }} {{ $tenant->nombre }}. Potenciado por CinemaSaaS.</p>
        </div>
    </footer>

    @stack('js')
</body>

</html>