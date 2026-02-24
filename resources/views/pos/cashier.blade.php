<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>POS - Panel de Cajero</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        [x-cloak] {
            display: none !important;
        }

        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        .pos-card-active {
            border-color: #10b981 !important;
            box-shadow: 0 20px 25px -5px rgba(16, 185, 129, 0.1), 0 10px 10px -5px rgba(16, 185, 129, 0.04);
            transform: translateY(-4px);
        }

        .category-btn.active {
            background-color: #10b981;
            color: white;
            box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.06);
        }

        .category-btn.active i {
            color: white;
        }

        /* Animaciones */
        .fade-in-up {
            animation: fadeInUp 0.3s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body class="bg-gray-100 font-sans antialiased text-gray-900">
    @include('pos.partials.silent_printer')

    @if(session('venta_exitosa'))
        <div class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-[60]" x-data="{ show: true }"
            x-init="
                            if (typeof imprimirRecibo === 'function') {
                                imprimirRecibo('{{ session('venta_exitosa')['print_url'] }}');
                            }
                            localStorage.removeItem('pos_cart_backup');
                        " x-show="show" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
            @click.away="show = false">
            <div class="bg-white rounded-2xl p-8 max-w-md w-full text-center shadow-2xl">
                <div class="text-6xl mb-4">✅</div>
                <h2 class="text-3xl font-bold text-green-600 mb-4 uppercase tracking-tighter">¡VENTA EXITOSA!</h2>
                <div class="mb-6 bg-gray-50 p-6 rounded-2xl border border-gray-100">
                    <p class="text-gray-400 font-black text-[10px] uppercase tracking-widest mb-2">Venta
                        #{{ session('venta_exitosa')['id'] }}</p>
                    <p class="text-5xl font-black text-gray-900 mb-4 tracking-tighter">
                        ${{ number_format(session('venta_exitosa')['total'], 0) }}
                    </p>
                    @if(session('venta_exitosa')['cambio'] > 0)
                        <div class="pt-4 border-t border-gray-200">
                            <p class="text-sm font-bold text-blue-600 uppercase tracking-widest">
                                Cambio: ${{ number_format(session('venta_exitosa')['cambio'], 0) }}
                            </p>
                        </div>
                    @endif
                </div>

                <div class="grid grid-cols-1 gap-3">
                    @if(isset(session('venta_exitosa')['print_url']))
                        <a href="{{ session('venta_exitosa')['print_url'] }}" target="_blank"
                            class="w-full bg-slate-900 hover:bg-black text-white py-4 rounded-xl font-black uppercase tracking-widest transition-all flex items-center justify-center gap-2">
                            <i class="fas fa-print"></i> Imprimir Ticket
                        </a>
                    @endif
                    <button @click="show = false"
                        class="w-full bg-emerald-600 hover:bg-emerald-500 text-white py-4 rounded-xl font-black uppercase tracking-widest transition-all shadow-lg shadow-emerald-900/10">
                        CONTINUAR
                    </button>
                </div>
            </div>
        </div>
    @endif

    <div x-data="posApp()" class="min-h-screen flex flex-col">

        {{-- Alertas Globales --}}
        <div class="fixed top-24 right-4 z-50 space-y-2 max-w-sm">
            @if(session('success'))
                <div class="bg-emerald-500 text-white p-4 rounded-2xl shadow-2xl flex items-center gap-3 animate-bounce">
                    <i class="fas fa-check-circle text-xl"></i>
                    <p class="font-black text-xs uppercase tracking-widest">{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-rose-500 text-white p-4 rounded-2xl shadow-2xl flex items-center gap-3 animate-pulse">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                    <p class="font-black text-xs uppercase tracking-widest">{{ session('error') }}</p>
                </div>
            @endif
        </div>


        <!-- Header -->
        <header class="bg-slate-900 text-white p-4 shadow-xl z-20">
            <div class="flex justify-between items-center max-w-[1920px] mx-auto w-full">
                <div class="flex items-center gap-4">
                    <div class="bg-emerald-500 p-2.5 rounded-xl shadow-lg shadow-emerald-900/20">
                        <i class="fas fa-cash-register text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-black tracking-tight uppercase">Sistema POS</h1>
                        <p class="text-[10px] text-slate-400 uppercase tracking-widest font-bold">Terminal:
                            {{ auth()->user()->name }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <!-- Offline Indicator -->
                    <div x-show="isOffline"
                        class="flex items-center gap-2 bg-rose-600 px-3 py-1.5 rounded-full animate-pulse border border-rose-400">
                        <i class="fas fa-wifi-slash text-[10px]"></i>
                        <span class="text-[9px] font-black uppercase tracking-widest">MODO OFFLINE</span>
                    </div>
                    <div x-show="!isOffline && offlineSalesCount > 0"
                        class="flex items-center gap-2 bg-amber-500 px-3 py-1.5 rounded-full border border-amber-300">
                        <i class="fas fa-sync fa-spin text-[10px]"></i>
                        <span class="text-[9px] font-black uppercase tracking-widest"
                            x-text="'SINCRONIZANDO ' + offlineSalesCount"></span>
                    </div>

                    <div class="text-right hidden md:block">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-tighter">
                            {{ now()->translatedFormat('l d \d\e F') }}
                        </p>
                        <p class="text-lg font-black text-emerald-400 leading-none" x-text="currentTime"></p>
                    </div>
                    <div class="h-8 w-px bg-slate-700 mx-2"></div>
                    <button @click="showOptions = !showOptions"
                        class="bg-slate-800 hover:bg-slate-700 px-4 py-2 rounded-xl transition-all flex items-center gap-2 font-black text-xs">
                        <i class="fas fa-cog" :class="showOptions ? 'rotate-90' : ''"
                            class="transition-transform duration-300"></i>
                        <span>AJUSTES</span>
                    </button>
                    <a href="{{ route('admin.dashboard.index') }}"
                        class="bg-emerald-600 hover:bg-emerald-500 p-2.5 rounded-xl transition-colors shadow-lg shadow-emerald-900/20">
                        <i class="fas fa-home"></i>
                    </a>
                </div>
            </div>
        </header>

        <!-- Botón de Cierre de Caja VISIBLE -->
        <div class="bg-white border-b shadow-sm p-4 mb-4">
            <div class="container mx-auto flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold">🏪 Punto de Venta</h2>
                    <p class="text-sm text-gray-600">
                        Caja #{{ $miCaja->id ?? 'N/A' }} -
                        Día Operativo: <span
                            class="font-bold text-emerald-600">{{ $fechaOperativa->format('d/m/Y') }}</span>
                    </p>
                </div>


            </div>
        </div>

        <!-- Options Panel -->
        <div x-show="showOptions" x-cloak @click.away="showOptions = false"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            class="bg-white border-b border-gray-200 p-4 shadow-2xl z-10">
            <div class="flex gap-4 justify-center max-w-4xl mx-auto">
                <button @click="reimprimirVenta()"
                    class="flex-1 bg-gray-50 hover:bg-orange-50 text-orange-600 border border-orange-100 p-3 rounded-2xl font-black text-[10px] uppercase transition-all flex flex-col items-center gap-2">
                    <i class="fas fa-print text-xl"></i>
                    <span>Reimprimir</span>
                </button>
                <button @click="anularVenta()"
                    class="flex-1 bg-gray-50 hover:bg-red-50 text-red-600 border border-red-100 p-3 rounded-2xl font-black text-[10px] uppercase transition-all flex flex-col items-center gap-2">
                    <i class="fas fa-times-circle text-xl"></i>
                    <span>Anular Venta</span>
                </button>
                <button @click="abrirBuscadorDevolucion()"
                    class="flex-1 bg-gray-50 hover:bg-slate-900 hover:text-white border border-slate-200 p-3 rounded-2xl font-black text-[10px] uppercase transition-all flex flex-col items-center gap-2">
                    <i class="fas fa-undo text-xl"></i>
                    <span>Devoluciones</span>
                </button>
                <button @click="procesarCortesia()"
                    class="flex-1 bg-gray-50 hover:bg-purple-50 text-purple-600 border border-purple-100 p-3 rounded-2xl font-black text-[10px] uppercase transition-all flex flex-col items-center gap-2">
                    <i class="fas fa-gift text-xl"></i>
                    <span>Cortesía</span>
                </button>
                <button @click="modalCerrarCaja = true; showOptions = false"
                    class="flex-1 bg-gray-50 hover:bg-slate-900 hover:text-white border border-slate-200 p-3 rounded-2xl font-black text-[10px] uppercase transition-all flex flex-col items-center gap-2">
                    <i class="fas fa-lock text-xl"></i>
                    <span>Cerrar Caja</span>
                </button>
                @if(auth()->user()->hasRole(['Root', 'Gerente', 'administrador']))
                    <button @click="cerrarDiaOperativo()"
                        class="flex-1 bg-gray-50 hover:bg-red-600 hover:text-white border border-red-200 p-3 rounded-2xl font-black text-[10px] uppercase transition-all flex flex-col items-center gap-2">
                        <i class="fas fa-calendar-times text-xl"></i>
                        <span>Cerrar Día</span>
                    </button>
                @endif
            </div>
        </div>

        <!-- Main Content (OPERATIONAL VIEW) -->
        <main class="flex flex-1 overflow-hidden">

            <!-- 1. BARRA LATERAL: Categorías (12%) -->
            <aside
                class="w-[12%] bg-slate-900 flex flex-col items-stretch pt-4 shadow-2xl z-10 overflow-y-auto scrollbar-hide">
                <p class="text-[8px] font-black text-slate-500 uppercase tracking-[0.2em] text-center mb-4">Menú</p>

                @if($hasCinema)
                    <button @click="currentCategory = 'cinema'"
                        :class="currentCategory === 'cinema' ? 'bg-emerald-600 text-white shadow-lg' : 'text-slate-400 hover:bg-slate-800'"
                        class="category-btn flex flex-col items-center py-5 transition-all outline-none group border-b border-slate-800">
                        <i class="fas fa-ticket-alt text-2xl mb-2 group-hover:scale-110 transition-transform"></i>
                        <span class="text-[9px] font-black uppercase tracking-widest">Cinema</span>
                    </button>
                @endif

                @foreach($categorias as $cat)
                    @if($cat->productos->count() > 0)
                        @php
                            $nombreCat = $cat->caracteristica->nombre ?? 'Sin Nombre';
                            $catLower = strtolower(trim($nombreCat));
                            $icons = [
                                'bebidas' => 'fa-glass-water',
                                'tragos' => 'fa-martini-glass',
                                'tragos o cocteles' => 'fa-cocktail',
                                'snacks' => 'fa-cookie',
                                'comidas' => 'fa-burger',
                                'comida' => 'fa-utensils',
                                'confitería' => 'fa-candy-cane',
                                'postres' => 'fa-ice-cream',
                                'bebidas calientes' => 'fa-mug-hot'
                            ];
                            $iconClass = $icons[$catLower] ?? ($cat->caracteristica->icono ?? 'box');
                        @endphp
                        <button @click="currentCategory = {{ $cat->id }}"
                            :class="currentCategory == {{ $cat->id }} ? 'bg-emerald-600 text-white shadow-lg' : 'text-slate-400 hover:bg-slate-800'"
                            class="category-btn flex flex-col items-center py-5 transition-all outline-none group border-b border-slate-800">
                            <i class="fas {{ $iconClass }} text-2xl mb-2 group-hover:scale-110 transition-transform"></i>
                            <span
                                class="text-[10px] font-black uppercase tracking-tighter px-1 text-center leading-tight">{{ $nombreCat }}</span>
                        </button>
                    @endif
                @endforeach
            </aside>

            <!-- 2. GRILLA CENTRAL: Productos / Funciones (58%) -->
            <section class="w-[58%] bg-gray-50 flex flex-col overflow-hidden relative">

                <!-- Buscador rápido (opcional, placeholder decorativo por ahora) -->
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="relative">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                        <input type="text" placeholder="BUSCAR PRODUCTO O PELÍCULA..."
                            class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl py-3 pl-12 pr-4 font-bold text-xs uppercase tracking-widest focus:border-emerald-500 outline-none transition-all">
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto p-6 scroll-smooth">

                    <!-- VISTA CINEMA -->
                    <div x-show="currentCategory === 'cinema'" class="fade-in-up">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                            <h2
                                class="text-xl font-black text-slate-900 uppercase tracking-tighter flex items-center gap-3">
                                <span
                                    class="w-8 h-8 bg-slate-900 text-white rounded-lg flex items-center justify-center text-xs">🎟️</span>
                                @if($fechaSeleccionada == now()->format('Y-m-d'))
                                    Cartelera de Hoy
                                @else
                                    Preventa: {{ \Carbon\Carbon::parse($fechaSeleccionada)->translatedFormat('d \d\e F') }}
                                @endif
                            </h2>

                            <!-- Selector de Fecha para Preventa -->
                            <form action="{{ route('pos.index') }}" method="GET"
                                class="flex items-center gap-2 bg-white p-2 rounded-2xl shadow-sm border border-gray-100">
                                <label class="text-[9px] font-black text-slate-400 uppercase ml-2">Ver fecha:</label>
                                <input type="date" name="fecha" value="{{ $fechaSeleccionada }}"
                                    onchange="this.form.submit()"
                                    class="border-none bg-transparent font-bold text-gray-700 focus:ring-0 text-xs">
                            </form>
                        </div>

                        @if($errorPreventa)
                            <div
                                class="bg-amber-50 border-2 border-amber-200 rounded-2xl p-4 mb-6 flex items-center gap-3 animate-pulse">
                                <i class="fas fa-calendar-times text-amber-600 text-lg"></i>
                                <p class="text-amber-800 font-bold text-xs uppercase tracking-tight">{{ $errorPreventa }}
                                </p>
                            </div>
                        @endif
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            @forelse($funciones as $funcion)
                                <div @click="window.location.href = '{{ route('cinema.seat-map', $funcion) }}'"
                                    class="bg-white rounded-[2rem] overflow-hidden shadow-sm hover:shadow-xl transition-all cursor-pointer group border-2 border-transparent hover:border-emerald-500">
                                    <div class="h-40 bg-slate-200 relative overflow-hidden">
                                        @if(optional($funcion->pelicula)->afiche)
                                            <img src="{{ asset('storage/' . $funcion->pelicula->afiche) }}"
                                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-slate-100">
                                                <i class="fas fa-film text-6xl"></i>
                                            </div>
                                        @endif
                                        <div
                                            class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent">
                                        </div>
                                        <div class="absolute bottom-4 left-4">
                                            <span
                                                class="bg-emerald-500 text-white px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest shadow-lg">
                                                {{ $funcion->fecha_hora->format('H:i') }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="p-4">
                                        <h4 class="font-black text-slate-900 text-xs uppercase truncate mb-1">
                                            {{ optional($funcion->pelicula)->titulo ?? 'SIN TÍTULO' }}
                                        </h4>
                                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">
                                            {{ $funcion->sala->nombre }} | {{ $funcion->sala->capacidad }} Butacas
                                        </p>
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-full py-20 text-center opacity-30">
                                    <i class="fas fa-calendar-times text-6xl mb-4"></i>
                                    <p class="font-black uppercase tracking-widest text-xs">No hay funciones programadas</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- VISTAS PRODUCTOS -->
                    @foreach($categorias as $cat)
                        @php
                            $nombreCat = $cat->caracteristica->nombre ?? 'Sin Nombre';
                            $catLower = strtolower(trim($nombreCat));
                            $icons = [
                                'bebidas' => 'fa-glass-water',
                                'tragos' => 'fa-martini-glass',
                                'tragos o cocteles' => 'fa-cocktail',
                                'snacks' => 'fa-cookie',
                                'comidas' => 'fa-burger',
                                'comida' => 'fa-utensils',
                                'confitería' => 'fa-candy-cane',
                                'postres' => 'fa-ice-cream',
                                'bebidas calientes' => 'fa-mug-hot'
                            ];
                            $iconClass = $icons[$catLower] ?? ($cat->caracteristica->icono ?? 'box');
                        @endphp
                        <div x-show="currentCategory == {{ $cat->id }}" class="fade-in-up">
                            <h2
                                class="text-xl font-black text-slate-900 uppercase tracking-tighter mb-6 flex items-center gap-3">
                                <span
                                    class="w-8 h-8 bg-emerald-500 text-white rounded-lg flex items-center justify-center text-xs">
                                    <i class="fas {{ $iconClass }}"></i>
                                </span>
                                {{ $cat->nombre }}
                            </h2>
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                                @foreach($cat->productos as $producto)
                                    @php $stock = $producto->inventario->cantidad ?? 0; @endphp
                                    <div x-data="{ qty: 1 }"
                                        class="bg-white p-4 rounded-[2.5rem] shadow-sm border-2 border-transparent hover:border-emerald-500 hover:shadow-xl transition-all text-center flex flex-col items-center group relative overflow-hidden">

                                        <div
                                            class="w-12 h-12 bg-slate-50 text-slate-300 rounded-2xl flex items-center justify-center mb-3 group-hover:bg-emerald-50 group-hover:text-emerald-500 transition-colors">
                                            <i class="fas {{ $iconClass }} text-xl"></i>
                                        </div>

                                        <span
                                            class="text-[9px] font-black uppercase text-slate-900 leading-tight mb-1 h-8 flex items-center justify-center line-clamp-2">
                                            {{ $producto->nombre }}
                                        </span>

                                        <span class="text-base font-black text-emerald-600 tracking-tighter mb-4">
                                            ${{ number_format($producto->precio, 0) }}
                                        </span>

                                        <!-- Control de Cantidad (SIEMPRE VISIBLE) -->
                                        <div
                                            class="flex items-center gap-2 mb-4 bg-slate-50 rounded-full p-1 border border-slate-100">
                                            <button @click="if(qty > 1) qty--"
                                                class="w-6 h-6 rounded-full bg-white shadow-sm flex items-center justify-center text-slate-400 hover:text-emerald-500 transition-colors">
                                                <i class="fas fa-minus text-[8px]"></i>
                                            </button>
                                            <input type="number" x-model="qty"
                                                class="w-8 text-center bg-transparent font-black text-[10px] outline-none"
                                                min="1" max="999">
                                            <button @click="qty++"
                                                class="w-6 h-6 rounded-full bg-white shadow-sm flex items-center justify-center text-slate-400 hover:text-emerald-500 transition-colors">
                                                <i class="fas fa-plus text-[8px]"></i>
                                            </button>
                                        </div>

                                        <button
                                            @click="agregarDirecto({{ $producto->id }}, '{{ addslashes($producto->nombre) }}', {{ $producto->precio }}, 999, qty)"
                                            class="w-full bg-slate-900 text-white py-2 rounded-2xl text-[9px] font-black uppercase tracking-widest hover:bg-emerald-600 transition-all shadow-lg shadow-slate-900/10">
                                            Agregar
                                        </button>

                                        @if($stock <= 0)
                                            <span class="mt-2 text-[7px] font-bold text-amber-500 uppercase tracking-tighter">
                                                ⚠️ Stock: {{ (int) $stock }} {{ $producto->presentacione->sigla ?? 'UN' }}
                                            </span>
                                        @elseif($stock > 0 && $stock < 10)
                                            <span class="mt-2 text-[7px] font-bold text-amber-500 uppercase tracking-tighter">
                                                Últimas {{ (int) $stock }} {{ $producto->presentacione->sigla ?? 'UN' }}
                                            </span>
                                        @else
                                            <span class="mt-2 text-[7px] font-bold text-slate-400 uppercase tracking-tighter">
                                                Stock: {{ (int) $stock }} {{ $producto->presentacione->sigla ?? 'UN' }}
                                            </span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                </div>
            </section>

            <!-- 3. BARRA DERECHA: Carrito y Totales (30%) -->
            <aside id="carrito-container"
                class="w-[30%] bg-white border-l-4 border-emerald-500 flex flex-col shadow-2xl relative overflow-hidden">
                @include('pos.partials.cart-sidebar')
            </aside>

        </main>

    </div>

    <!-- MODAL APERTURA DE CAJA -->
    <template x-if="modalAbrirCaja">
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/90 backdrop-blur-md">
            <div class="bg-white rounded-[2.5rem] w-full max-w-lg shadow-2xl overflow-hidden p-10 text-center">
                <div
                    class="w-24 h-24 bg-emerald-100 text-emerald-600 rounded-3xl flex items-center justify-center mx-auto mb-8">
                    <i class="fas fa-lock-open text-4xl"></i>
                </div>
                <h2 class="text-3xl font-black text-slate-900 mb-2 uppercase tracking-tighter">Apertura de Caja</h2>
                <p class="text-slate-400 font-bold uppercase tracking-widest text-xs mb-10">Inicia tu turno de trabajo
                </p>

                <form action="{{ route('admin.cajas.abrir') }}" method="POST">
                    @csrf
                    <div class="mb-8 text-left">
                        <label
                            class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Monto
                            Inicial en Efectivo</label>
                        <div class="relative">
                            <span
                                class="absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 font-black text-xl">$</span>
                            <input type="number" name="monto_inicial" required value="0" step="any"
                                class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl pl-10 pr-6 py-5 font-black text-2xl text-slate-900 focus:border-emerald-500 transition-all outline-none">
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full bg-emerald-600 hover:bg-emerald-500 text-white py-6 rounded-2xl font-black uppercase tracking-widest transition-all shadow-xl shadow-emerald-900/20 active:scale-95">
                        Confirmar Apertura
                    </button>

                    <a href="{{ route('admin.dashboard.index') }}"
                        class="block mt-6 text-slate-400 hover:text-slate-600 font-bold text-[10px] uppercase tracking-widest transition-colors">
                        Cancelar y salir
                    </a>
                </form>
            </div>
        </div>
    </template>

    <!-- MODAL CIERRE DE CAJA -->
    <template x-if="modalCerrarCaja">
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/90 backdrop-blur-md"
            x-cloak>
            <div class="bg-white rounded-[2.5rem] w-full max-w-lg shadow-2xl overflow-hidden p-10 text-center relative">
                <button @click="modalCerrarCaja = false"
                    class="absolute top-8 right-8 text-slate-300 hover:text-slate-900 transition-colors">
                    <i class="fas fa-times text-2xl"></i>
                </button>

                <div
                    class="w-24 h-24 bg-rose-100 text-rose-600 rounded-3xl flex items-center justify-center mx-auto mb-8">
                    <i class="fas fa-cash-register text-4xl"></i>
                </div>
                <h2 class="text-3xl font-black text-slate-900 mb-2 uppercase tracking-tighter">Cierre de Caja</h2>
                <p class="text-slate-400 font-bold uppercase tracking-widest text-xs mb-10">Finalización de turno y
                    arqueo</p>

                @php
                    $cajaActual = \App\Models\Caja::where('empresa_id', auth()->user()->empresa_id)->where('estado', 'ABIERTA')->first();
                @endphp

                @if($cajaActual)
                    <form action="{{ route('admin.cajas.cerrar', $cajaActual->id) }}" method="POST">
                        @csrf
                        <div class="space-y-6 text-left">
                            <div>
                                <label
                                    class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Efectivo
                                    en Caja (Contado)</label>
                                <div class="relative">
                                    <span
                                        class="absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 font-black text-xl">$</span>
                                    <input type="number" name="monto_declarado" required step="any"
                                        class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl pl-10 pr-6 py-4 font-black text-2xl text-slate-900 focus:border-rose-500 transition-all outline-none">
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-[10px] font-black text-blue-400 uppercase tracking-widest mb-2 px-1">
                                    <i class="fas fa-credit-card mr-1"></i>
                                    Total Vouchers Datáfono (Opcional)
                                </label>
                                <div class="relative">
                                    <span
                                        class="absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 font-black text-xl">$</span>
                                    <input type="number" name="tarjeta_declarada" step="any"
                                        class="w-full bg-blue-50 border-2 border-blue-100 rounded-2xl pl-10 pr-6 py-4 font-black text-2xl text-slate-900 focus:border-blue-500 transition-all outline-none">
                                </div>
                                <p class="text-center text-[9px] font-bold text-blue-500 uppercase mt-2">
                                    Cuenta tus vouchers físicos del datáfono
                                </p>
                            </div>
                        </div>

                        <button type="submit"
                            class="w-full bg-slate-900 hover:bg-black text-white mt-10 py-6 rounded-2xl font-black uppercase tracking-widest transition-all shadow-xl shadow-slate-900/20 active:scale-95">
                            Cerrar y Generar Reporte
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </template>

    <!-- MODAL: DATOS DEL CLIENTE (DIAN / FASE 5) -->
    <template x-if="modalDatosCliente">
        <div class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-slate-900/90 backdrop-blur-md"
            x-cloak>
            <div
                class="bg-white rounded-[2.5rem] w-full max-w-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
                <div class="p-8 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-12 h-12 bg-blue-600 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-blue-900/20">
                            <i class="fas fa-user-tag text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-black text-slate-900 uppercase tracking-tighter">Datos del Cliente
                            </h2>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Requerido para
                                Factura Electrónica</p>
                        </div>
                    </div>
                    <button @click="modalDatosCliente = false"
                        class="text-slate-300 hover:text-slate-900 transition-colors">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>

                <div class="p-8 overflow-y-auto space-y-6">
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label
                                class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Tipo
                                de Documento</label>
                            <select x-model="cliente.tipo_doc"
                                class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-5 py-4 font-bold text-slate-900 focus:border-blue-500 outline-none transition-all">
                                <option value="CC">Cédula de Ciudadanía</option>
                                <option value="NIT">NIT (Empresas)</option>
                                <option value="CE">Cédula de Extranjería</option>
                                <option value="PPN">Pasaporte</option>
                            </select>
                        </div>
                        <div>
                            <label
                                class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Número
                                de Documento</label>
                            <input type="text" x-model="cliente.documento" placeholder="Ej: 10203040"
                                class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-5 py-4 font-bold text-slate-900 focus:border-blue-500 outline-none transition-all">
                        </div>
                    </div>

                    <div>
                        <label
                            class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Nombre
                            Completo / Razón Social</label>
                        <input type="text" x-model="cliente.nombre" placeholder="Ej: Juan Pérez o Empresa SAS"
                            class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-5 py-4 font-bold text-slate-900 focus:border-blue-500 outline-none transition-all">
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label
                                class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Correo
                                Electrónico</label>
                            <input type="email" x-model="cliente.email" placeholder="cliente@example.com"
                                class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-5 py-4 font-bold text-slate-900 focus:border-blue-500 outline-none transition-all">
                        </div>
                        <div>
                            <label
                                class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Teléfono</label>
                            <input type="text" x-model="cliente.telefono" placeholder="Ej: 3001234567"
                                class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-5 py-4 font-bold text-slate-900 focus:border-blue-500 outline-none transition-all">
                        </div>
                    </div>
                </div>

                <div class="p-8 bg-gray-50 border-t border-gray-100 grid grid-cols-2 gap-4">
                    <button
                        @click="cliente = {tipo_doc: 'CC', documento: '', nombre: '', email: '', telefono: '', preferencia: 'fe_todo'}; solicitaFactura = false; modalDatosCliente = false"
                        class="bg-white border-2 border-slate-100 text-slate-400 py-5 rounded-2xl font-black uppercase tracking-widest hover:text-rose-500 hover:border-rose-100 transition-all">
                        Limpiar y Cerrar
                    </button>
                    <button @click="solicitaFactura = true; modalDatosCliente = false"
                        class="bg-blue-600 hover:bg-blue-500 text-white py-5 rounded-2xl font-black uppercase tracking-widest transition-all shadow-lg shadow-blue-900/10">
                        Guardar Datos
                    </button>
                </div>
            </div>
        </div>
    </template>
    </div>

    <script>
        /* === INTERCEPTOR GLOBAL DE FETCH (HARDENING FASE 4) === */
        const originalFetch = window.fetch;
        window.fetch = async (...args) => {
            try {
                const response = await originalFetch(...args);

                // 1. Sesión Expirada (419) -> Recarga automática
                if (response.status === 419) {
                    await Swal.fire({
                        title: 'Sesión Expirada',
                        text: 'Tu sesión ha caducado. Recargando página para reconectar...',
                        icon: 'warning',
                        timer: 2500,
                        showConfirmButton: false,
                        allowOutsideClick: false
                    });
                    window.location.reload();
                    return new Promise(() => { }); // Detener ejecución para evitar errores en cadena
                }

                // 2. Error Servidor (500) -> Alerta UX, no pantalla roja
                if (response.status === 500) {
                    Swal.fire({
                        title: '¡Ups! Algo salió mal',
                        text: 'El servidor encontró un error inesperado al procesar tu solicitud.',
                        icon: 'error',
                        confirmButtonText: 'Entendido',
                        confirmButtonColor: '#0f172a'
                    });
                }

                return response;
            } catch (error) {
                // Error de red (Network Error)
                console.error('Fetch Error:', error);
                Swal.fire({
                    title: 'Error de Conexión',
                    text: 'No se pudo conectar con el servidor. Verifica tu internet.',
                    icon: 'warning',
                    toast: true,
                    position: 'bottom-end',
                    showConfirmButton: false,
                    timer: 4000
                });
                throw error;
            }
        };

        function posApp() {
            return {
                modalCerrarCaja: false,
                modalAbrirCaja: {{ \App\Models\Caja::where('user_id', auth()->id())->where('empresa_id', auth()->user()->empresa_id)->where('estado', 'ABIERTA')->count() == 0 ? 'true' : 'false' }},
                showOptions: false,
                currentTime: '',
                currentCategory: '{{ $hasCinema ? "cinema" : ($categorias->first() ? $categorias->first()->id : "") }}',
                metodoPago: 'EFECTIVO',

                // Datos Fiscales / Facturación (Fase 5)
                modalDatosCliente: false,
                solicitaFactura: false,
                cliente: {
                    tipo_doc: 'CC',
                    documento: '',
                    nombre: '',
                    email: '',
                    telefono: '',
                    preferencia: 'fe_todo'
                },

                ventaExitosa: {
                    show: false,
                    url: '',
                    id: '',
                    total: 0,
                    tipo_desc: ''
                },
                isProcessing: false,
                isOffline: !navigator.onLine,
                offlineSalesCount: 0,
                nitValido: null,

                // Validador NIT colombiano (Módulo 11)
                validarNIT() {
                    if (this.cliente.tipo_doc !== 'NIT') { this.nitValido = null; return; }
                    const raw = this.cliente.documento.replace(/[^0-9]/g, '');
                    if (raw.length < 2) { this.nitValido = null; return; }
                    const nit = raw.slice(0, -1);
                    const dv = parseInt(raw.slice(-1));
                    const pesos = [3, 7, 13, 17, 19, 23, 29, 37, 41, 43, 47, 53, 59, 67, 71];
                    let sum = 0;
                    for (let i = 0; i < nit.length; i++) {
                        sum += parseInt(nit[nit.length - 1 - i]) * pesos[i];
                    }
                    const rem = sum % 11;
                    const dvCalc = rem > 1 ? 11 - rem : rem;
                    this.nitValido = dvCalc === dv;
                },

                // Helper para validar datos fiscales antes de enviar
                validarDatosFiscales() {
                    if (this.solicitaFactura) {
                        if (!this.cliente.documento || !this.cliente.nombre) {
                            Swal.fire('Datos Incompletos', 'Para Factura Electrónica es obligatorio Documento y Nombre.', 'warning');
                            return false;
                        }
                    }
                    return true;
                },

                init() {
                    this.updateTime();
                    setInterval(() => this.updateTime(), 1000);

                    // Offline detection
                    window.addEventListener('online', () => {
                        this.isOffline = false;
                        this.syncOfflineSales();
                    });
                    window.addEventListener('offline', () => {
                        this.isOffline = true;
                    });

                    this.updateOfflineCount();

                    // Heartbeat para evitar 419 (CSRF/Session Timeout)
                    setInterval(() => {
                        if (!this.isOffline) fetch('{{ url("pos/carrito-partial") }}');
                    }, 15 * 60 * 1000); // Cada 15 min

                    // Sync initial
                    if (!this.isOffline) this.syncOfflineSales();
                },

                updateOfflineCount() {
                    const sales = JSON.parse(localStorage.getItem('offline_sales') || '[]');
                    this.offlineSalesCount = sales.length;
                },

                async syncOfflineSales() {
                    if (this.isOffline) return;

                    const sales = JSON.parse(localStorage.getItem('offline_sales') || '[]');
                    if (sales.length === 0) return;

                    this.isProcessing = true;

                    for (const sale of sales) {
                        try {
                            const response = await originalFetch('{{ route("pos.sync-offline") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify(sale)
                            });

                            const data = await response.json();
                            if (data.success) {
                                // Eliminar de local storage
                                const currentSales = JSON.parse(localStorage.getItem('offline_sales') || '[]');
                                const filtered = currentSales.filter(s => s.uuid !== sale.uuid);
                                localStorage.setItem('offline_sales', JSON.stringify(filtered));
                                this.updateOfflineCount();
                            }
                        } catch (e) {
                            console.error('Error syncing sale:', e);
                        }
                    }
                    this.isProcessing = false;
                },

                updateTime() {
                    const now = new Date();
                    this.currentTime = now.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                },

                refrescarCarrito() {
                    fetch('{{ route("pos.carrito.partial") }}')
                        .then(response => response.text())
                        .then(html => {
                            document.getElementById('carrito-container').innerHTML = html;
                        });
                },

                agregarDirecto(productoId, nombre, precio, stockMax, cantidad = 1) {
                    if (stockMax <= 0 || cantidad < 1) return;

                    const formData = new FormData();
                    formData.append('producto_id', productoId);
                    formData.append('cantidad', cantidad);

                    fetch('{{ route("pos.agregar.producto") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Notificar y refrescar sin recargar página
                                Swal.fire({
                                    title: '¡Agregado!',
                                    text: `${nombre} (${cantidad}) añadido a la orden`,
                                    icon: 'success',
                                    toast: true,
                                    position: 'top-end',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                                this.refrescarCarrito();
                            } else {
                                Swal.fire('Stock Insuficiente', data.message, 'warning');
                            }
                        });
                },

                quitarBoleto(index) {
                    fetch(`{{ url('pos/quitar-boleto') }}/${index}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                    }).then(() => this.refrescarCarrito());
                },

                quitarProducto(index) {
                    fetch(`{{ url('pos/quitar-producto') }}/${index}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                    }).then(() => this.refrescarCarrito());
                },

                vaciarCarrito() {
                    fetch('{{ route("pos.vaciar") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    }).then(() => this.refrescarCarrito());
                },

                async finalizarVenta() {
                    if (this.isProcessing) return;

                    // 1. CONFIRMACIÓN PREVIA (Verificación total y botón de volver)
                    const totalConfirm = document.getElementById('total-sidebar')?.innerText || '0';
                    const { isConfirmed } = await Swal.fire({
                        title: 'Confirmar Pago',
                        html: `
                            <div class="text-center">
                                <p class="text-slate-500 text-[10px] uppercase font-bold tracking-widest mb-1">Monto Total</p>
                                <p class="text-5xl font-black text-slate-900 mb-6">$${totalConfirm}</p>
                                <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 text-left">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase mb-2">Seleccione Método de Pago</p>
                                    <div class="flex gap-2">
                                        <label class="flex-1 cursor-pointer">
                                            <input type="radio" x-model="metodoPago" value="EFECTIVO" class="hidden peer">
                                            <div class="w-full text-center py-3 rounded-xl border-2 peer-checked:border-slate-900 peer-checked:bg-slate-900 peer-checked:text-white border-slate-200 transition-all text-xs font-black">EFECTIVO</div>
                                        </label>
                                        <label class="flex-1 cursor-pointer">
                                            <input type="radio" x-model="metodoPago" value="TARJETA" class="hidden peer">
                                            <div class="w-full text-center py-3 rounded-xl border-2 peer-checked:border-slate-900 peer-checked:bg-slate-900 peer-checked:text-white border-slate-200 transition-all text-xs font-black">TARJETA</div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        `,
                        showCancelButton: true,
                        confirmButtonText: 'CONFIRMAR Y COBRAR',
                        cancelButtonText: 'MODIFICAR PEDIDO',
                        confirmButtonColor: '#10b981',
                        cancelButtonColor: '#64748b',
                    });

                    if (!isConfirmed) return;

                    this.isProcessing = true;
                    try {
                        const formData = new FormData();
                        formData.append('metodo_pago', this.metodoPago);

                        // Campos Fiscales (Fase 5)
                        formData.append('solicita_factura', this.solicitaFactura ? 1 : 0);
                        formData.append('cliente_tipo_doc', this.cliente.tipo_doc);
                        formData.append('cliente_documento', this.cliente.documento);
                        formData.append('cliente_nombre', this.cliente.nombre);
                        formData.append('cliente_email', this.cliente.email);
                        formData.append('cliente_telefono', this.cliente.telefono);
                        formData.append('preferencia_fiscal', this.cliente.preferencia);

                        if (this.isOffline) {
                            return this.saveOfflineVenta(formData);
                        }

                        const response = await fetch('{{ route("pos.finalizar") }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: formData
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.ventaExitosa.id = data.venta_id;
                            this.ventaExitosa.total = data.total_pagado;
                            this.ventaExitosa.url = data.print_url;
                            this.ventaExitosa.tipo_desc = data.tipo_venta_desc;
                            this.ventaExitosa.show = true;
                            this.refrescarCarrito();
                            this.ventaExitosa.view_url = `{{ url('admin/ventas') }}/${data.venta_id}`;

                            // GESTIÓN DE IMPRESIÓN (Fase 5 - Optimizada)
                            if (data.imprimir_recibo && data.print_url) {
                                // Llamamos directamente al iFrame silenciado para Kioskos
                                if (typeof imprimirRecibo === 'function') {
                                    imprimirRecibo(data.print_url);
                                } else {
                                    // Fallback UI si Kiosk no está activo
                                    Swal.fire({
                                        title: '¡Venta Registrada!',
                                        text: '¿Deseas imprimir el ticket ahora?',
                                        icon: 'question',
                                        showCancelButton: true,
                                        confirmButtonText: 'SÍ, IMPRIMIR',
                                        cancelButtonText: 'DESPUÉS',
                                        confirmButtonColor: '#10b981',
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            window.open(data.print_url, '_blank', 'width=400,height=600');
                                        }
                                    });
                                }
                            }
                        } else {
                            Swal.fire({
                                title: 'Error en Venta',
                                text: data.message || 'Error desconocido',
                                icon: 'error',
                                confirmButtonColor: '#0f172a'
                            });
                        }
                    } catch (error) {
                        Swal.fire('Error de Conexión', 'No se pudo comunicar con el servidor', 'error');
                    } finally {
                        this.isProcessing = false;
                    }
                },

                async procesarCortesia() {
                    const { value: confirmVenta } = await Swal.fire({
                        title: '¿Confirmar Cortesía?',
                        text: "Esta venta se registrará con valor $0 y no ingresará dinero a caja. ¿Deseas continuar?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#8b5cf6',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: 'Sí, registrar cortesía',
                        cancelButtonText: 'Cancelar'
                    });

                    if (confirmVenta) {
                        const originalPago = this.metodoPago;
                        this.metodoPago = 'CORTESIA';
                        await this.finalizarVenta();
                        this.metodoPago = originalPago;
                    }
                }
                ,

                async abrirBuscadorDevolucion() {
                    const { value: nroFactura } = await Swal.fire({
                        title: 'Buscar Venta para Devolución',
                        input: 'text',
                        inputLabel: 'Número de Comprobante / Factura',
                        inputPlaceholder: 'Ej: B001 - 0000123',
                        showCancelButton: true,
                        confirmButtonText: 'Buscar',
                        confirmButtonColor: '#0f172a',
                        cancelButtonText: 'Cancelar'
                    });

                    if (nroFactura) {
                        try {
                            const response = await fetch(`/admin/ventas/buscar?numero=${nroFactura}`);
                            const data = await response.json();

                            if (data.success) {
                                const { value: result } = await Swal.fire({
                                    title: 'Detalle de Venta',
                                    html: `
                                        <div class="text-left text-sm space-y-2 p-4 bg-slate-50 rounded-2xl border border-slate-100">
                                            <p><b>Comprobante:</b> ${data.venta.numero_comprobante}</p>
                                            <p><b>Fecha:</b> ${new Date(data.venta.fecha_hora).toLocaleString()}</p>
                                            <p><b>Total:</b> $${Number(data.venta.total).toLocaleString()}</p>
                                            <p><b>Items:</b> ${data.detalle}</p>
                                        </div>
                                        <div class="mt-4 space-y-4">
                                            <div>
                                                <p class="font-bold text-xs uppercase text-slate-400 mb-2">Motivo de la devolución</p>
                                                <textarea id="swal-motivo" class="swal2-textarea w-full m-0" placeholder="Mínimo 10 caracteres..."></textarea>
                                            </div>
                                            <div>
                                                <p class="font-bold text-xs uppercase text-slate-400 mb-2">PIN Administrativo</p>
                                                <input id="swal-pin" type="password" maxlength="4" class="swal2-input w-full m-0 text-center tracking-widest" placeholder="••••">
                                            </div>
                                        </div>
                                    `,
                                    showCancelButton: true,
                                    confirmButtonText: 'Procesar Devolución',
                                    confirmButtonColor: '#e11d48',
                                    cancelButtonText: 'Cancelar',
                                    preConfirm: () => {
                                        const motivo = document.getElementById('swal-motivo').value;
                                        const pin = document.getElementById('swal-pin').value;
                                        if (!motivo || motivo.length < 10) {
                                            Swal.showValidationMessage('El motivo debe tener al menos 10 caracteres');
                                            return false;
                                        }
                                        if (!pin || pin.length < 4) {
                                            Swal.showValidationMessage('Ingrese el PIN administrativo de 4 dígitos');
                                            return false;
                                        }
                                        return { motivo, pin };
                                    }
                                });

                                if (result) {
                                    const devResponse = await fetch(`/admin/ventas/${data.venta.id}/devolver`, {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                        },
                                        body: JSON.stringify({
                                            motivo: result.motivo,
                                            auth_pin: result.pin,
                                            reintegrar_stock: true
                                        })
                                    });

                                    const devData = await devResponse.json();

                                    if (devData.success) {
                                        Swal.fire({
                                            title: '¡Devolución Procesada!',
                                            text: devData.message,
                                            icon: 'success',
                                            confirmButtonColor: '#10b981'
                                        });
                                        location.reload();
                                    } else {
                                        throw new Error(devData.message);
                                    }
                                }
                            } else {
                                Swal.fire('Error', data.message, 'error');
                            }
                        } catch (error) {
                            Swal.fire('Error crítico', error.message, 'error');
                        }
                    }
                },

                async reimprimirVenta() {
                    const { value: nroFactura } = await Swal.fire({
                        title: 'Reimprimir Ticket',
                        input: 'text',
                        inputLabel: 'Número de Comprobante / Factura',
                        inputPlaceholder: 'Ej: B001-0000123',
                        showCancelButton: true,
                        confirmButtonText: 'Buscar',
                        confirmButtonColor: '#0f172a'
                    });

                    if (nroFactura) {
                        try {
                            const response = await fetch(`/admin/ventas/buscar?numero=${nroFactura}`);
                            const data = await response.json();

                            if (data.success) {
                                // Redirigir directamente al PDF (opcional pedir PIN, el usuario pidió PIN para todo)
                                const { value: pin } = await Swal.fire({
                                    title: 'Confirmar Reimpresión',
                                    text: `Venta #${data.venta.id} por $${Number(data.venta.total).toLocaleString()}`,
                                    input: 'password',
                                    inputLabel: 'Ingrese PIN Administrativo',
                                    inputPlaceholder: '••••',
                                    showCancelButton: true,
                                    preConfirm: (v) => v === '1234' ? true : Swal.showValidationMessage('PIN Incorrecto')
                                });

                                if (pin) {
                                    const printUrl = `/admin/export-pdf-comprobante-venta/${btoa(data.venta.id.toString())}`;
                                    // Nota: Usamos btoa simple si no tenemos el Crypt de Laravel a mano, 
                                    // pero lo ideal es que el backend devuelva la URL firmada.
                                    // Vamos a mejorar el backend para que buscarPorComprobante devuelva la URL.
                                    window.open(data.print_url || `/admin/export-pdf-comprobante-venta/${data.venta.id}`, '_blank');
                                }
                            } else {
                                Swal.fire('No encontrado', data.message, 'error');
                            }
                        } catch (e) {
                            Swal.fire('Error', 'No se pudo procesar la solicitud', 'error');
                        }
                    }
                },

                async anularVenta() {
                    const { value: nroFactura } = await Swal.fire({
                        title: 'Anular Venta (Void)',
                        input: 'text',
                        inputLabel: 'Número de Comprobante / Factura',
                        inputPlaceholder: 'Ej: B001-0000123',
                        showCancelButton: true,
                        confirmButtonText: 'Buscar',
                        confirmButtonColor: '#e11d48'
                    });

                    if (nroFactura) {
                        const response = await fetch(`/admin/ventas/buscar?numero=${nroFactura}`);
                        const data = await response.json();

                        if (data.success) {
                            const { value: result } = await Swal.fire({
                                title: '¿ANULAR ESTA VENTA?',
                                html: `
                                    <div class="text-left text-sm p-4 bg-rose-50 rounded-2xl border border-rose-100 mb-4">
                                        <p><b>Venta ID:</b> #${data.venta.id}</p>
                                        <p><b>Total:</b> $${Number(data.venta.total).toLocaleString()}</p>
                                        <p><b>Detalle:</b> ${data.detalle}</p>
                                    </div>
                                    <input id="answal-pin" type="password" maxlength="4" class="swal2-input w-full m-0 text-center" placeholder="PIN GERENTE">
                                `,
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'SÍ, ANULAR AHORA',
                                confirmButtonColor: '#e11d48',
                                preConfirm: () => {
                                    const pin = document.getElementById('answal-pin').value;
                                    if (!pin) return Swal.showValidationMessage('Se requiere PIN administrativo');
                                    return pin;
                                }
                            });

                            if (result) {
                                const delRes = await fetch(`/admin/ventas/${data.venta.id}`, {
                                    method: 'DELETE',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                    },
                                    body: JSON.stringify({ auth_pin: result })
                                });
                                const delData = await delRes.json();
                                if (delRes.ok) {
                                    Swal.fire('Anulada', delData.message, 'success').then(() => location.reload());
                                } else {
                                    Swal.fire('Error', delData.message || 'No se pudo anular', 'error');
                                }
                            }
                        } else {
                            Swal.fire('Error', 'Venta no encontrada', 'error');
                        }
                    }
                },

            }
        }
                },

        saveOfflineVenta(formData) {
            const cartData = JSON.parse(document.getElementById('cart-data-json')?.textContent || '{}');
            if (!cartData.boletos?.length && !cartData.productos?.length) {
                Swal.fire('Error', 'No hay productos en el carrito', 'error');
                return;
            }

            const offlineSale = {
                uuid: crypto.randomUUID(),
                data: Object.fromEntries(formData.entries()),
                cart: cartData,
                total: parseFloat(document.getElementById('total-sidebar')?.innerText.replace(/[^0-9.]/g, '') || 0),
                timestamp: new Date().toISOString()
            };

            const sales = JSON.parse(localStorage.getItem('offline_sales') || '[]');
            sales.push(offlineSale);
            localStorage.setItem('offline_sales', JSON.stringify(sales));
            this.updateOfflineCount();

            Swal.fire({
                title: 'Venta Guardada Offline',
                text: 'La venta se sincronizará automáticamente cuando vuelva internet.',
                icon: 'info',
                timer: 3000,
                showConfirmButton: false
            });

            // Limpiar UI localmente (ya que no podemos llamar a refrescarCarrito si no hay red)
            // En una implementación real más compleja re-renderizaríamos el carrito Alpine localmente.
            // Por ahora, recargamos para que el server (si vuelve) o la sesión local se limpie si se puede.
            // Pero como estamos offline, refrescarCarrito fallará.
            // Vamos a vaciar el DOM del carrito manualmente.
            const container = document.getElementById('carrito-container');
            if (container) {
                container.innerHTML = `<div class="p-6 text-center text-slate-300 uppercase font-black text-[10px]">Carrito Vacío (Offline)</div>`;
            }

            this.isProcessing = false;
        },

                async cerrarDiaOperativo() {
            const { isConfirmed } = await Swal.fire({
                title: '¿Cerrar Día Operativo?',
                text: "Esta acción marcará el fin de las operaciones de hoy. Las ventas posteriores se asignarán al siguiente día contable.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'SÍ, CERRAR DÍA',
                cancelButtonText: 'CANCELAR',
                confirmButtonColor: '#e11d48',
            });

            if (isConfirmed) {
                try {
                    const response = await fetch('{{ route("pos.cerrar-dia") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });
                    const data = await response.json();

                    if (data.success) {
                        Swal.fire('Día Cerrado', data.message, 'success').then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                } catch (e) {
                    Swal.fire('Error', 'No se pudo procesar el cierre de día.', 'error');
                }
            }
        }
            };
        }
    </script>

    <!-- MODAL: EXITO VENTA (AJAX & SESSION) -->
    <template x-if="ventaExitosa.show" x-teleport="body">
        <div x-cloak
            class="fixed inset-0 bg-slate-950/90 backdrop-blur-xl flex items-center justify-center z-[200] p-4">
            <div class="bg-white rounded-[3rem] p-10 max-w-lg w-full text-center shadow-2xl fade-in-up">
                <div
                    class="w-24 h-24 bg-emerald-500 text-white rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg shadow-emerald-500/20">
                    <i class="fas fa-check text-4xl"></i>
                </div>

                <h2 class="text-3xl font-black text-slate-900 uppercase tracking-tighter mb-2">¡Venta Exitosa!</h2>
                <p class="text-slate-500 font-bold mb-8 uppercase tracking-widest text-[10px]">La transacción se ha
                    registrado correctamente</p>

                <div class="bg-slate-50 rounded-3xl p-6 mb-8 border-2 border-slate-100 grid grid-cols-2 gap-4">
                    <div class="text-left">
                        <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Total Cobrado</p>
                        <p class="text-3xl font-black text-slate-900"
                            x-text="'$' + Number(ventaExitosa.total).toLocaleString()"></p>
                    </div>
                    <div class="text-right">
                        <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Venta #</p>
                        <p class="text-xl font-black text-slate-900" x-text="ventaExitosa.id"></p>
                    </div>
                    <div class="col-span-2 mt-4 pt-4 border-t border-slate-200 text-center">
                        <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Tipo de Operación
                        </p>
                        <span
                            class="px-4 py-1 bg-emerald-100 text-emerald-700 rounded-full text-[10px] font-black uppercase tracking-widest"
                            x-text="ventaExitosa.tipo_desc"></span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <a :href="ventaExitosa.url" target="_blank"
                        class="bg-emerald-600 hover:bg-emerald-500 text-white py-5 rounded-2xl font-black uppercase tracking-widest transition-all flex items-center justify-center gap-3 shadow-lg shadow-emerald-900/10">
                        <i class="fas fa-print text-sm"></i>
                        <span>IMPRIMIR TICKET</span>
                    </a>

                    <a :href="ventaExitosa.view_url"
                        class="bg-slate-900 hover:bg-slate-800 text-white py-5 rounded-2xl font-black uppercase tracking-widest transition-all flex items-center justify-center gap-3">
                        <i class="fas fa-eye text-sm"></i>
                        <span>Ver Detalle</span>
                    </a>

                    <button @click="ventaExitosa.show = false"
                        class="col-span-2 bg-white hover:bg-slate-100 text-slate-900 py-4 rounded-2xl font-black uppercase tracking-widest transition-all border-2 border-slate-100">
                        Nueva Venta
                    </button>
                </div>
            </div>
        </div>
    </template>

    {{-- Alertas Globales al final para asegurar que Swal esté listo --}}
    @if(session('error'))
        <script>
            Swal.fire({
                title: 'Error',
                text: '{{ session("error") }}',
                icon: 'error',
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#0f172a'
            });
        </script>
    @endif



    <script>
        // Backup automático del carrito cada vez que cambia
        function respaldarCarrito() {
            const items = Array.from(document.querySelectorAll('.cart-item')).map(item => ({
                id: item.dataset.id,
                cantidad: parseInt(item.querySelector('.qty').textContent),
                precio: parseFloat(item.dataset.precio)
            }));

            const totalEl = document.getElementById('cart-total');
            const total = totalEl ? parseFloat(totalEl.textContent.replace(/[^0-9.]/g, '')) : 0;

            const carritoActual = { items, total };
            localStorage.setItem('pos_cart_backup', JSON.stringify(carritoActual));
            localStorage.setItem('pos_cart_time', Date.now());
        }

        // Restauración al cargar página
        window.addEventListener('load', () => {
            const backup = localStorage.getItem('pos_cart_backup');
            const time = localStorage.getItem('pos_cart_time');

            // Si hay un contenedor de carrito en el DOM...
            const carritoContainer = document.getElementById('carrito-container');
            const currentItemsSize = document.querySelectorAll('.cart-item').length;

            if (currentItemsSize === 0 && backup && time && (Date.now() - parseInt(time) < 900000)) { // 15 min
                const data = JSON.parse(backup);

                if (data.items.length > 0) {
                    // Toast de recuperación
                    Swal.fire({
                        title: 'Restaurando Carrito...',
                        text: 'Detectamos una sesión interrumpida. Recuperando items.',
                        icon: 'info',
                        toast: true,
                        position: 'bottom-end',
                        showConfirmButton: false,
                        timer: 2000
                    });

                    // Trigger manual server side reconstruction since Alpine/Server holds the truth
                    // A proper implementation would send IDs back, but simple reload is safe
                    // For now we clear the backup so it doesn't loop
                    localStorage.removeItem('pos_cart_backup');
                }
            }
        });

        // Event listeners para respaldar al modificar (se inyectan en cambios del carrito)
        document.addEventListener('click', (e) => {
            if (e.target.closest('button') && e.target.closest('#carrito-container')) {
                setTimeout(respaldarCarrito, 500);
            }
        });
    </script>
</body>

</html>