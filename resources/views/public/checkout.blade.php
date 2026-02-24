@extends('layouts.public')

@section('title', 'Finalizar Reserva')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="mb-12">
            <h1 class="text-4xl font-black text-slate-900 tracking-tighter uppercase">Finalizar Reserva</h1>
            <p class="text-slate-500 font-medium">Completa tus datos para recibir las entradas.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            <!-- Customer Info Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-[3rem] shadow-xl shadow-slate-200/50 border border-slate-100 p-8 md:p-12">
                    <h3 class="text-xl font-black text-slate-900 uppercase tracking-tighter mb-8">Tus Datos</h3>

                    <form id="paymentForm" class="space-y-6">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label
                                    class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Nombre
                                    Completo</label>
                                <input type="text" name="nombre" required placeholder="Ej. Juan Pérez"
                                    class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary outline-none transition-all">
                            </div>
                            <div>
                                <label
                                    class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Correo
                                    Electrónico</label>
                                <input type="email" name="email" required placeholder="juan@ejemplo.com"
                                    class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary outline-none transition-all">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label
                                    class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Teléfono
                                    (WhatsApp)</label>
                                <input type="tel" name="telefono" placeholder="+57 300 123 4567"
                                    class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary outline-none transition-all">
                            </div>
                            <div>
                                <label
                                    class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Documento
                                    (Opcional)</label>
                                <input type="text" name="documento" placeholder="C.C. / N.I.T."
                                    class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary outline-none transition-all">
                            </div>
                        </div>

                        <div class="pt-8">
                            <h3 class="text-xl font-black text-slate-900 uppercase tracking-tighter mb-8">Método de Pago
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <label
                                    class="relative flex items-center p-6 bg-slate-50 border-2 border-transparent rounded-3xl cursor-pointer hover:bg-slate-100 transition-all has-[:checked]:border-primary has-[:checked]:bg-white">
                                    <input type="radio" name="metodo_pago" value="stripe" checked class="sr-only">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="w-12 h-12 bg-indigo-500 rounded-2xl flex items-center justify-center text-white">
                                            <i class="fab fa-stripe-s text-2xl"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-black text-slate-900 uppercase">Tarjeta de Crédito/Débito
                                            </p>
                                            <p class="text-[10px] font-bold text-slate-400 uppercase">Procesado por Stripe
                                            </p>
                                        </div>
                                    </div>
                                    <div class="absolute top-4 right-4">
                                        <i class="fas fa-check-circle text-primary opacity-0 .checked-icon"></i>
                                    </div>
                                </label>

                                <label
                                    class="relative flex items-center p-6 bg-slate-50 border-2 border-transparent rounded-3xl cursor-pointer hover:bg-slate-100 transition-all opacity-50">
                                    <input type="radio" name="metodo_pago" value="nequi" disabled class="sr-only">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="w-12 h-12 bg-pink-500 rounded-2xl flex items-center justify-center text-white">
                                            <i class="fas fa-wallet text-2xl"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-black text-slate-900 uppercase">NEQUI / DAVIPLATA</p>
                                            <p class="text-[10px] font-bold text-slate-400 uppercase">Próximamente</p>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div
                    class="bg-white rounded-[3rem] shadow-xl shadow-slate-200/50 border border-slate-100 p-8 sticky top-28">
                    <h3 class="text-xl font-black text-slate-900 uppercase tracking-tighter mb-8">Resumen</h3>

                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-16 h-24 rounded-xl overflow-hidden shadow-lg flex-shrink-0">
                            <img src="{{ asset($funcion->pelicula->img_path ?? 'assets/img/poster-default.jpg') }}"
                                class="w-full h-full object-cover">
                        </div>
                        <div>
                            <h4 class="text-sm font-black text-slate-900 uppercase leading-tight">
                                {{ $funcion->pelicula->nombre }}</h4>
                            <p class="text-[10px] font-bold text-slate-400 uppercase mt-1">
                                {{ \Carbon\Carbon::parse($funcion->fecha_hora)->translatedFormat('d M, h:i A') }}</p>
                            <p class="text-[10px] font-bold text-primary uppercase">{{ $funcion->sala->nombre }}</p>
                        </div>
                    </div>

                    <div class="bg-slate-50 rounded-3xl p-6 mb-8">
                        <div class="space-y-3 mb-4">
                            @foreach($asientos as $asiento)
                                <div class="flex justify-between items-center">
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Asiento
                                        {{ $asiento->codigo_asiento }}</span>
                                    <span
                                        class="text-xs font-black text-slate-900">${{ number_format($funcion->precio, 0) }}</span>
                                </div>
                            @endforeach
                        </div>
                        <div class="border-t border-slate-200 pt-4 flex justify-between items-center">
                            <span class="text-xs font-black text-slate-900 uppercase tracking-widest">Total</span>
                            <span class="text-3xl font-black text-primary">${{ number_format($total, 0) }}</span>
                        </div>
                    </div>

                    <button onclick="procesarPago()"
                        class="w-full bg-primary text-white py-6 rounded-3xl font-black uppercase tracking-widest transition-all duration-300 transform active:scale-95 shadow-xl shadow-blue-500/20">
                        Pagar Ahora
                    </button>

                    <div class="mt-8 flex items-center justify-center gap-2 opacity-50">
                        <i class="fas fa-lock text-[10px]"></i>
                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-500">Pago 100%
                            Seguro</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function procesarPago() {
            const form = document.getElementById('paymentForm');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            Swal.fire({
                title: 'Procesando Pago...',
                text: 'Te estamos redirigiendo a la pasarela segura',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            // Simulación de envío a controlador de pago
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            fetch("{{ route('public.pagar') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            })
                .then(res => res.json())
                .then(data => {
                    // Aquí se integraría con Stripe.js
                    Swal.fire({
                        icon: 'info',
                        title: 'Próxima Integración',
                        text: 'El flujo de reserva y bloqueo de asientos está listo. La redirección a Stripe se activará una vez configuradas las API Keys del tenant.',
                        confirmButtonColor: 'var(--primary)'
                    });
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire('Error', 'No se pudo iniciar el proceso de pago', 'error');
                });
        }
    </script>
@endpush