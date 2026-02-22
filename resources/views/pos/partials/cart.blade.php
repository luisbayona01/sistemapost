<div class="p-6 border-b border-gray-50 flex items-center justify-between bg-slate-50/50">
    <h2 class="text-sm font-black text-slate-900 uppercase tracking-widest flex items-center gap-2">
        <i class="fas fa-shopping-basket text-emerald-500"></i>
        Detalle de Venta
    </h2>
    <span class="bg-slate-900 text-white text-[10px] font-black px-2 py-1 rounded-lg">
        {{ count($carrito['boletos']) + count($carrito['productos']) }}
    </span>
</div>

<div class="flex-1 overflow-y-auto p-6 space-y-6">
    @if(count($carrito['boletos']) > 0)
        <div class="space-y-3">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4 border-b pb-2">
                Entradas</p>
            @foreach($carrito['boletos'] as $boleto)
                <div class="bg-slate-50 border border-slate-100 p-4 rounded-2xl flex justify-between items-center group">
                    <div>
                        <h4 class="font-black text-slate-900 text-sm">General</h4>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">Asiento:
                            {{ $boleto['asiento_id'] }}
                        </p>
                    </div>
                    <span class="text-sm font-black text-slate-900">${{ number_format($boleto['precio'], 0) }}</span>
                </div>
            @endforeach
        </div>
    @endif

    @if(count($carrito['productos']) > 0)
        <div class="space-y-3">
            <p
                class="text-[10px] font-black text-emerald-500 uppercase tracking-[0.2em] mb-4 border-b border-emerald-100 pb-2">
                Confitería</p>
            @foreach($carrito['productos'] as $item)
                <div class="bg-emerald-50/30 border border-emerald-50 p-4 rounded-2xl flex justify-between items-center group">
                    <div class="min-w-0 flex-1 pr-2">
                        <h4 class="font-black text-slate-900 text-sm truncate">{{ $item['nombre'] }}</h4>
                        <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest">Cant:
                            {{ $item['cantidad'] }}
                        </p>
                    </div>
                    <span
                        class="text-sm font-black text-slate-900 whitespace-nowrap">${{ number_format($item['precio'] * $item['cantidad'], 0) }}</span>
                </div>
            @endforeach
        </div>
    @endif

    @if(count($carrito['boletos']) == 0 && count($carrito['productos']) == 0)
        <div class="h-full flex flex-col items-center justify-center text-center py-20 opacity-20">
            <i class="fas fa-shopping-cart text-6xl mb-4 text-slate-300"></i>
            <p class="text-slate-500 font-black text-xs uppercase tracking-widest">Caja Vacía</p>
        </div>
    @endif
</div>

@if(count($carrito['boletos']) > 0 || count($carrito['productos']) > 0)
    <div class="p-6 bg-slate-50 border-t border-gray-100 space-y-4">
        @php
            $totalBoletos = array_sum(array_column($carrito['boletos'], 'precio'));
            $totalProductos = array_reduce($carrito['productos'], fn($carry, $item) => $carry + ($item['precio'] * $item['cantidad']), 0);
            $total = $totalBoletos + $totalProductos;
        @endphp

        <div class="flex justify-between items-end">
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Total</span>
            <span class="text-4xl font-black text-slate-900">${{ number_format($total, 0) }}</span>
        </div>

        <div class="space-y-2">
            <form method="POST" action="{{ route('pos.finalizar') }}">
                @csrf
                <button type="submit"
                    class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-4 rounded-2xl text-lg font-black shadow-lg shadow-emerald-200 transition-all flex items-center justify-center gap-3">
                    <i class="fas fa-money-bill-wave"></i>
                    <span>COBRAR</span>
                </button>
            </form>
            <form method="POST" action="{{ route('pos.vaciar') }}">
                @csrf
                <button type="submit"
                    class="w-full py-2 text-[10px] font-black text-slate-400 hover:text-rose-500 uppercase tracking-widest transition-colors">
                    <i class="fas fa-trash-alt mr-1"></i> Vaciar Carrito
                </button>
            </form>
        </div>
    </div>
@endif
