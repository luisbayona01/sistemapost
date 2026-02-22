<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vista Ejecutiva Móvil</title>
    <!-- Tailwind CSS desde CDN para garantizar vista mobile sin sidebars -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen pb-10">

    <!-- Header Móvil -->
    <div class="bg-white px-4 py-3 shadow-sm flex justify-between items-center sticky top-0 z-50">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-lg font-bold text-gray-800">Cinema Paraíso</h1>
                <a href="?refresh=1" class="text-gray-400 hover:text-blue-500 transition px-1">↻</a>
            </div>
            <p class="text-xs text-gray-500">{{ \Carbon\Carbon::now()->locale('es')->isoFormat('dddd, D MMMM') }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.dashboard.index') }}" class="text-xs bg-gray-100 text-gray-600 px-3 py-1.5 rounded-full font-semibold">
                🖥️ Desktop
            </a>
        </div>
    </div>

    <div class="px-4 py-4 space-y-6">

        <!-- 1. KPIs HOY -->
        <div class="grid grid-cols-2 gap-3">
            <!-- Ventas Totales -->
            <div class="col-span-2 bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs text-gray-500 font-semibold uppercase tracking-wide">Ventas Totales</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">${{ number_format($kpis['total'], 0) }}</p>
                    </div>
                    <div class="text-right">
                        <span class="text-xs font-bold {{ $kpis['diff_color'] }} bg-gray-50 px-2 py-1 rounded">
                            {{ $kpis['diff_signo'] }}{{ $kpis['diff_porcentaje'] }}%
                        </span>
                        <p class="text-[10px] text-gray-400 mt-1">vs ayer</p>
                    </div>
                </div>
            </div>

            <!-- Boletería -->
            <div class="bg-white rounded-xl p-3 shadow-sm border border-gray-100">
                <p class="text-[10px] text-gray-500 font-bold uppercase">🎟️ Entradas</p>
                <p class="text-xl font-bold text-gray-800 mt-1">${{ number_format($kpis['boleteria'], 0) }}</p>
            </div>

            <!-- Confitería -->
            <div class="bg-white rounded-xl p-3 shadow-sm border border-gray-100">
                <p class="text-[10px] text-gray-500 font-bold uppercase">🍿 Dulcería</p>
                <p class="text-xl font-bold text-gray-800 mt-1">${{ number_format($kpis['confiteria'], 0) }}</p>
            </div>
            
             <!-- Transacciones -->
             <div class="col-span-2 bg-white rounded-xl p-3 shadow-sm border border-gray-100 flex justify-between items-center">
                <span class="text-xs text-gray-500 font-bold uppercase">📦 Operaciones Hoy</span>
                <span class="text-lg font-bold text-gray-800">{{ $kpis['transacciones'] }}</span>
            </div>
        </div>

        <!-- 2. ESTADO DE CAJA -->
        <div>
            <h2 class="text-sm font-bold text-gray-400 mb-2 uppercase tracking-wide">Situación de Caja</h2>
            
            @if($caja)
                <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 {{ $caja->estado == 'ABIERTA' ? 'border-green-500' : 'border-gray-500' }}">
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-xs font-bold px-2 py-0.5 rounded {{ $caja->estado == 'ABIERTA' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ $caja->estado }}
                        </span>
                        <span class="text-xs text-gray-400">#{{ $caja->id }}</span>
                    </div>
                    
                    @if($caja->estado == 'CERRADA')
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Resultado:</span>
                            @if($caja->diferencia == 0)
                                <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded font-bold">✅ Cuadra Perfecto</span>
                            @else
                                <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded font-bold">
                                    ⚠️ {{ $caja->diferencia > 0 ? '+' : '' }}{{ number_format($caja->diferencia, 0) }}
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-400 mt-2 text-right">Cerrada: {{ $caja->updated_at->format('H:i') }}</p>
                    @else
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Apertura:</span>
                            <span class="font-bold">${{ number_format($caja->monto_apertura, 0) }}</span>
                        </div>
                        <p class="text-xs text-gray-400 mt-2 text-right">Abierta: {{ $caja->created_at->format('H:i') }}</p>
                    @endif
                </div>
            @else
                <div class="bg-gray-50 rounded-xl p-4 text-center text-gray-500 text-sm">
                    No hay registros de caja recientes.
                </div>
            @endif
        </div>

        <!-- 3. ALERTAS CRÍTICAS -->
        @if($alertas->isNotEmpty())
        <div>
            <div class="flex justify-between items-end mb-2">
                <h2 class="text-sm font-bold text-gray-400 uppercase tracking-wide">⚠️ Alertas Activas</h2>
                <a href="{{ route('admin.alertas.index') }}" class="text-xs text-blue-600">Ver todas</a>
            </div>
            
            <div class="space-y-2">
                @foreach($alertas as $alerta)
                <div class="bg-white rounded-xl p-3 shadow-sm border-l-4 {{ $alerta->tipo == 'CRITICA' ? 'border-red-500' : 'border-yellow-500' }}">
                    <div class="flex justify-between">
                        <p class="text-sm font-bold text-gray-800 truncate">{{ $alerta->titulo }}</p>
                        <span class="text-[10px] text-gray-400">{{ $alerta->created_at->format('H:i') }}</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $alerta->mensaje }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- 4. TOP DEL DÍA -->
        <div>
            <h2 class="text-sm font-bold text-gray-400 mb-2 uppercase tracking-wide">🏆 Top del Día</h2>
            <div class="grid grid-cols-1 gap-3">
                @if($top['pelicula'])
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl p-4 text-white shadow-sm">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-[10px] uppercase opacity-75 font-semibold">Película #1</p>
                            <p class="font-bold truncate max-w-[200px]">{{ $top['pelicula']->titulo }}</p>
                        </div>
                        <p class="font-bold text-lg">${{ number_format($top['pelicula']->total, 0) }}</p>
                    </div>
                </div>
                @endif
                
                @if($top['producto'])
                <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-4 text-white shadow-sm">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-[10px] uppercase opacity-75 font-semibold">Producto #1</p>
                            <p class="font-bold truncate max-w-[200px]">{{ $top['producto']->nombre }}</p>
                        </div>
                        <p class="font-bold text-lg">${{ number_format($top['producto']->total, 0) }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
        
        <div class="pt-4 text-center">
            <p class="text-[10px] text-gray-400">Sistema POS v4.0 • Vista Ejecutiva</p>
        </div>

    </div>

</body>
</html>
