@extends('layouts.admin')
@section('content')
    <div class="container mx-auto px-4 py-6 max-w-4xl">
        <div class="bg-white rounded-3xl shadow-2xl p-8 border border-gray-100">

            <div class="flex justify-between items-center mb-8 border-b pb-6">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-900">↩️ Procesar Devolución</h1>
                    <p class="text-gray-500 mt-1">Selecciona los productos o asientos a reintegrar.</p>
                </div>
                <a href="{{ route('admin.devoluciones.index') }}"
                    class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-4 py-2 rounded-xl transition font-bold">←
                    Volver</a>
            </div>

            <!-- Info de la venta -->
            <div class="bg-blue-50 border-2 border-blue-100 rounded-2xl p-6 mb-8">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="font-black text-blue-900 text-3xl">Venta #{{ $venta->id }}</p>
                        <div class="flex gap-4 mt-2">
                            <span
                                class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">
                                Canal: {{ $venta->canal }}
                            </span>
                            <span class="text-gray-500 text-sm font-medium">
                                🕒 {{ $venta->created_at->format('d/m/Y H:i') }}
                            </span>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-black text-blue-400 uppercase tracking-widest">Total Cobrado</p>
                        <p class="text-4xl font-black text-blue-900">${{ number_format($venta->total, 0) }}</p>
                    </div>
                </div>
            </div>

            <!-- Alerta si es devolución excepcional -->
            @if(!$esDelMismoDia && $esRoot)
                <div class="bg-amber-50 border-2 border-amber-200 rounded-2xl p-6 mb-8 flex items-start gap-4">
                    <div class="bg-amber-100 p-3 rounded-full text-amber-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-black text-amber-900 uppercase tracking-wide">⚠️ DEVOLUCIÓN EXCEPCIONAL (ROOT)</p>
                        <p class="text-sm text-amber-700 leading-relaxed">
                            Esta venta pertenece a un día anterior. Estás ejecutando una acción administrativa especial.
                            El sistema registrará esta autorización como un movimiento de auditoría crítico.
                        </p>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.devoluciones.procesar') }}" id="formDevolucion">
                @csrf
                <input type="hidden" name="venta_id" value="{{ $venta->id }}">

                <!-- Boletos a devolver -->
                @if($venta->funcionAsientos && $venta->count() > 0)
                    <div class="mb-8">
                        <h3 class="text-xl font-black text-gray-800 mb-4 flex items-center gap-2">
                            <span
                                class="bg-indigo-100 text-indigo-600 w-8 h-8 rounded-lg flex items-center justify-center text-sm">🎟️</span>
                            Boletos de Cine
                        </h3>
                        <div class="grid grid-cols-1 gap-3">
                            @foreach($venta->funcionAsientos as $asiento)
                                <label
                                    class="group flex items-center gap-4 p-4 border-2 border-gray-100 rounded-2xl cursor-pointer hover:border-blue-400 hover:bg-blue-50/30 transition shadow-sm">
                                    <input type="checkbox" name="items_boletos[]" value="{{ $asiento->id }}"
                                        class="w-6 h-6 rounded-lg text-blue-600 border-gray-300 focus:ring-0">
                                    <div class="flex-1">
                                        <p class="font-bold text-gray-900">
                                            {{ $asiento->funcion->pelicula->titulo ?? 'Sin título' }}
                                        </p>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span
                                                class="text-xs bg-gray-100 text-gray-500 px-2 py-1 rounded-md font-bold uppercase">
                                                {{ $asiento->funcion->sala->nombre ?? '' }}
                                            </span>
                                            <span class="text-xs text-gray-400 font-medium"> Asiento:
                                                {{ $asiento->codigo ?? $asiento->fila . $asiento->numero }} </span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-lg font-black text-green-600">
                                            ${{ number_format($asiento->funcion->precio_base ?? 0, 0) }}</p>
                                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Reembolsable</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Productos a devolver -->
                @if($venta->productos && $venta->productos->count() > 0)
                    <div class="mb-8">
                        <h3 class="text-xl font-black text-gray-800 mb-4 flex items-center gap-2">
                            <span
                                class="bg-amber-100 text-amber-600 w-8 h-8 rounded-lg flex items-center justify-center text-sm">🍿</span>
                            Productos / Confitería
                        </h3>
                        <div class="grid grid-cols-1 gap-3">
                            @foreach($venta->productos as $producto)
                                <div class="flex items-center gap-4 p-4 border-2 border-gray-100 rounded-2xl shadow-sm">
                                    <div class="flex-1">
                                        <p class="font-bold text-gray-900">{{ $producto->nombre }}</p>
                                        <p class="text-xs text-gray-400 mt-1">
                                            Comprado: <span class="font-bold text-gray-600">{{ $producto->pivot->cantidad }}</span>
                                            pack(s) • ${{ number_format($producto->pivot->precio_unitario, 0) }} c/u
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-3 bg-gray-50 px-4 py-2 rounded-xl border border-gray-100">
                                        <label class="text-xs font-black text-gray-500 uppercase tracking-tighter">Devolver:</label>
                                        <input type="number" name="items_productos[{{ $producto->id }}]" min="0"
                                            max="{{ $producto->pivot->cantidad }}" value="0"
                                            class="w-16 bg-white border-2 border-gray-200 rounded-lg py-1 px-2 text-center font-black text-blue-600 focus:border-blue-400 focus:ring-0 transition">
                                        <span class="text-xs text-gray-400 font-bold">/ {{ $producto->pivot->cantidad }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Motivo y Autorización -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
                    <div class="space-y-2">
                        <label
                            class="block text-sm font-black text-gray-700 uppercase tracking-widest flex items-center gap-2">
                            <span>📝 Motivo de devolución</span>
                            <span class="text-red-500 text-lg">*</span>
                        </label>
                        <textarea name="motivo" rows="3" required minlength="5"
                            class="w-full border-2 border-gray-100 rounded-2xl px-4 py-3 focus:border-blue-400 focus:ring-0 transition bg-gray-50"
                            placeholder="Ej: Cliente se retiró por mala atención / Error en cobro..."></textarea>
                    </div>

                    @if(!$esDelMismoDia && $esRoot)
                        <div class="space-y-2">
                            <label
                                class="block text-sm font-black text-amber-700 uppercase tracking-widest flex items-center gap-2">
                                <span>🔐 Nota de Autorización</span>
                                <span class="text-red-500 text-lg">*</span>
                            </label>
                            <textarea name="autorizacion_nota" rows="3" required
                                class="w-full border-2 border-amber-100 rounded-2xl px-4 py-3 focus:border-amber-400 focus:ring-0 transition bg-amber-50"
                                placeholder="Explica la resolución administrativa para este caso fuera de fecha..."></textarea>
                        </div>
                    @endif
                </div>

                <!-- Botones de Acción -->
                <div class="flex flex-col md:flex-row gap-4">
                    <button type="submit"
                        class="flex-1 bg-red-600 hover:bg-red-700 text-white py-6 rounded-3xl font-black text-2xl shadow-xl shadow-red-200 transition transform hover:-translate-y-1 active:scale-95 flex items-center justify-center gap-3">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 11l3-3m0 0l3 3m-3-3v8m0-13a9 9 0 110 18 9 9 0 010-18z"></path>
                        </svg>
                        PROCESAR DEVOLUCIÓN
                    </button>
                    <a href="{{ route('admin.devoluciones.index') }}"
                        class="md:w-1/3 bg-gray-100 hover:bg-gray-200 text-gray-500 py-6 rounded-3xl font-black text-xl transition flex items-center justify-center">
                        Me arrepentí
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Pequeña validación extra antes de enviar
        document.getElementById('formDevolucion').addEventListener('submit', function (e) {
            const checkedBoletos = document.querySelectorAll('input[name="items_boletos[]"]:checked').length;
            const productsQty = Array.from(document.querySelectorAll('input[name^="items_productos"]'))
                .reduce((acc, input) => acc + parseInt(input.value || 0), 0);

            if (checkedBoletos === 0 && productsQty === 0) {
                e.preventDefault();
                alert('⚠️ Debes seleccionar al menos un asiento o un producto para devolver.');
            } else {
                if (!confirm('¿Estás seguro de procesar esta devolución? Se reintegrará el dinero a la caja y el stock al inventario.')) {
                    e.preventDefault();
                }
            }
        });
    </script>
@endsection