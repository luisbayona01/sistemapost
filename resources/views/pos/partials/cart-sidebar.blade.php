<div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
    <h2 class="text-xl font-black text-slate-900 uppercase tracking-tighter">🛒 Mi Orden</h2>
    <span class="bg-slate-200 text-slate-600 px-2 py-0.5 rounded-full text-[10px] font-black">
        {{ count($carrito['boletos']) + count($carrito['productos']) }} Items
    </span>
</div>

<!-- Lista de Items en el Carrito -->
<div class="flex-1 overflow-y-auto p-6 space-y-4">

    <!-- Boletos -->
    @if(count($carrito['boletos']) > 0)
        <div class="space-y-3">
            <p class="text-[8px] font-black text-blue-500 uppercase tracking-[0.2em]">Entradas</p>
            @foreach($carrito['boletos'] as $index => $boleto)
                <div
                    class="bg-blue-50/50 p-4 rounded-2xl border border-blue-100 relative group transition-all hover:bg-white hover:shadow-md">
                    <button type="button" @click="quitarBoleto({{ $index }})"
                        class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity w-6 h-6 bg-rose-500 text-white rounded-lg flex items-center justify-center text-[10px] hover:scale-110 transition-transform">✕</button>
                    <p class="font-black text-slate-900 text-[10px] uppercase truncate pr-6">
                        {{ $boleto['pelicula'] }}
                    </p>
                    <p class="text-[8px] font-bold text-slate-400 uppercase mt-1">Sala: {{ $boleto['sala'] }} |
                        Asiento: <span class="text-blue-600 font-black">{{ $boleto['asiento'] }}</span></p>
                    <p class="text-sm font-black text-blue-700 mt-2">${{ number_format($boleto['precio'], 0) }}
                    </p>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Confitería -->
    @if(count($carrito['productos']) > 0)
        <div class="space-y-3 mt-6">
            <p class="text-[8px] font-black text-emerald-500 uppercase tracking-[0.2em]">Snacks & Bebidas
            </p>
            @foreach($carrito['productos'] as $index => $item)
                <div
                    class="bg-emerald-50/50 p-4 rounded-2xl border border-emerald-100 relative group transition-all hover:bg-white hover:shadow-md">
                    <button type="button" @click="quitarProducto({{ $index }})"
                        class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity w-6 h-6 bg-rose-500 text-white rounded-lg flex items-center justify-center text-[10px] hover:scale-110 transition-transform">✕</button>
                    <p class="font-black text-slate-900 text-[10px] uppercase truncate pr-6">
                        {{ $item['nombre'] }}
                    </p>
                    <p class="text-[8px] font-bold text-slate-400 uppercase mt-1">Cantidad:
                        {{ $item['cantidad'] }} × ${{ number_format($item['precio'], 0) }}
                    </p>
                    <p class="text-sm font-black text-emerald-700 mt-2">
                        ${{ number_format($item['precio'] * $item['cantidad'], 0) }}</p>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Estado Vacío -->
    @if(count($carrito['boletos']) == 0 && count($carrito['productos']) == 0)
        <div
            class="h-64 flex flex-col items-center justify-center border-2 border-dashed border-slate-100 rounded-[2.5rem] bg-slate-50/50">
            <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mb-4 shadow-sm">
                <i class="fas fa-shopping-basket text-4xl text-slate-100"></i>
            </div>
            <p class="font-black text-[10px] uppercase tracking-widest text-slate-300">Orden vacía</p>
        </div>
    @endif
</div>

<!-- Total y acciones -->
@if(count($carrito['boletos']) > 0 || count($carrito['productos']) > 0)
    <div class="p-6 bg-gray-50 border-t-2 border-slate-100">
        @php
            // Calcular total de boletos
            $totalBoletos = 0;
            foreach ($carrito['boletos'] as $boleto) {
                $totalBoletos += floatval($boleto['precio'] ?? 0);
            }

            // Calcular total de productos
            $totalProductos = 0;
            foreach ($carrito['productos'] as $producto) {
                $precio = floatval($producto['precio'] ?? 0);
                $cantidad = intval($producto['cantidad'] ?? 0);
                $totalProductos += ($precio * $cantidad);
            }

            // Total consolidado
            $total = $totalBoletos + $totalProductos;

            // DEBUG
            \Log::info('Carrito Debug', [
                'boletos' => $carrito['boletos'],
                'productos' => $carrito['productos'],
                'totalBoletos' => $totalBoletos,
                'totalProductos' => $totalProductos,
                'total' => $total,
            ]);
        @endphp

        <!-- Desglose visual -->
        <div class="mb-4 space-y-2 bg-white p-4 rounded-3xl border border-slate-100 shadow-sm text-xs">
            @if($totalBoletos > 0)
                <div class="flex justify-between items-center text-blue-600">
                    <span class="font-black uppercase tracking-widest">🎟️ Boletos ({{ count($carrito['boletos']) }}):</span>
                    <span class="font-black">${{ number_format($totalBoletos, 0) }}</span>
                </div>
            @endif

            @if($totalProductos > 0)
                <div class="flex justify-between items-center text-emerald-600">
                    <span class="font-black uppercase tracking-widest">🍿 Confitería:</span>
                    <span class="font-black">${{ number_format($totalProductos, 0) }}</span>
                </div>
            @endif
        </div>

        <!-- SECCIÓN FISCAL (DIAN FASE 5) -->
        <div class="mb-4 p-4 bg-blue-50/50 rounded-3xl border border-blue-100 border-dashed">
            <div class="flex items-center justify-between mb-2">
                <label class="flex items-center gap-3 cursor-pointer group">
                    <div class="relative">
                        <input type="checkbox" x-model="solicitaFactura" class="sr-only peer">
                        <div
                            class="w-10 h-5 bg-slate-200 rounded-full peer peer-focus:ring-2 peer-focus:ring-blue-300 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600">
                        </div>
                    </div>
                    <span
                        class="text-[9px] font-black text-slate-600 uppercase tracking-widest group-hover:text-blue-600 transition-colors">Factura
                        Electrónica</span>
                </label>
                <div x-show="solicitaFactura" x-cloak class="animate-pulse">
                    <span class="w-2 h-2 bg-blue-500 rounded-full inline-block"></span>
                </div>
            </div>

            <div x-show="solicitaFactura" x-cloak class="space-y-3 mt-4 fade-in-up">
                @include('pos.partials.cliente-fiscal')
            </div>
        </div>

        <!-- Total principal -->
        <div
            class="bg-gradient-to-r from-blue-600 to-green-600 text-white p-6 rounded-[2.5rem] mb-4 shadow-xl shadow-blue-500/20">
            <p class="text-[8px] font-black uppercase tracking-[0.3em] opacity-80 mb-1">TOTAL A PAGAR</p>
            <p class="text-4xl font-black tracking-tighter">$<span id="total-sidebar">{{ number_format($total, 0) }}</span>
            </p>
        </div>

        <form @submit.prevent="if(validarDatosFiscales()) finalizarVenta()">
            @csrf
            <div class="grid grid-cols-2 gap-2 mb-4">
                <button type="button" @click="metodoPago = 'EFECTIVO'"
                    :class="metodoPago === 'EFECTIVO' ? 'bg-emerald-500 text-white border-emerald-500' : 'bg-white text-slate-600 border-slate-200'"
                    class="py-3 rounded-xl font-black text-[8px] uppercase tracking-widest border transition-all">Efectivo</button>
                <button type="button" @click="metodoPago = 'TRANSFERENCIA'"
                    :class="metodoPago === 'TRANSFERENCIA' ? 'bg-emerald-500 text-white border-emerald-500' : 'bg-white text-slate-600 border-slate-200'"
                    class="py-3 rounded-xl font-black text-[8px] uppercase tracking-widest border transition-all">Tarjeta/Transf</button>
            </div>
            <input type="hidden" name="metodo_pago" :value="metodoPago">
            <input type="hidden" name="monto_recibido" value="{{ $total }}">

            <button type="submit" :disabled="isProcessing"
                class="w-full bg-emerald-600 hover:bg-emerald-500 text-white py-5 rounded-2xl text-xl font-black uppercase tracking-widest transition-all shadow-xl shadow-emerald-500/20 active:scale-95 flex items-center justify-center gap-3 disabled:opacity-50">
                <span x-show="!isProcessing">$ COBRAR</span>
                <span x-show="isProcessing" x-cloak>PROCESANDO...</span>
                <i x-show="!isProcessing" class="fas fa-check-circle"></i>
                <i x-show="isProcessing" x-cloak class="fas fa-spinner fa-spin"></i>
            </button>
        </form>

        <form method="POST" action="{{ route('pos.vaciar') }}" class="mt-4">
            @csrf
            <button type="submit"
                class="w-full text-[10px] font-black text-rose-400 uppercase tracking-widest hover:text-rose-600 transition-colors">
                🗑️ Anular Todo
            </button>
        </form>
    </div>
@endif