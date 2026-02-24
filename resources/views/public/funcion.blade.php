@extends('layouts.public')

@section('title', 'Reservar: ' . $funcion->pelicula->nombre)

@push('css')
    <style>
        .cinema-container {
            perspective: 1500px;
        }

        .screen {
            height: 10px;
            background: linear-gradient(to bottom, #cbd5e1, #94a3b8);
            width: 80%;
            margin: 0 auto;
            border-radius: 100%;
            transform: rotateX(-10deg) scaleX(1.1);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .screen::after {
            content: 'PANTALLA';
            position: absolute;
            top: 25px;
            left: 50%;
            transform: translateX(-50%);
            color: #94a3b8;
            font-size: 0.65rem;
            letter-spacing: 0.8em;
            font-weight: 800;
        }

        .seats-grid {
            display: grid;
            gap: 0.6rem;
            justify-content: center;
            padding: 2rem;
            contain: layout paint;
        }

        .seat-wrapper {
            width: 2.25rem;
            height: 2.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }

        .seat {
            width: 85%;
            height: 75%;
            background: #e2e8f0;
            border-radius: 0.6rem 0.6rem 0.3rem 0.3rem;
            position: absolute;
            bottom: 4px;
            pointer-events: none;
            transition: background 0.2s;
        }

        .seat::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 10%;
            width: 80%;
            height: 4px;
            background: #cbd5e1;
            border-radius: 0 0 0.5rem 0.5rem;
        }

        .seat-wrapper:hover:not(.occupied):not(.blocked) {
            transform: translateY(-4px) scale(1.1);
        }

        .seat-wrapper:hover:not(.occupied):not(.blocked) .seat {
            background: #94a3b8;
        }

        .seat-wrapper.selected .seat {
            background: var(--primary) !important;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .seat-wrapper.selected .seat-id {
            color: white;
        }

        .seat-wrapper.occupied .seat {
            background: #ef4444;
            cursor: not-allowed;
            opacity: 0.8;
        }

        .seat-wrapper.blocked .seat {
            background: #f59e0b;
            cursor: not-allowed;
        }

        .seat-wrapper.pasillo {
            cursor: default;
            visibility: hidden;
            pointer-events: none;
        }

        .seat-id {
            font-size: 0.6rem;
            font-weight: 800;
            z-index: 10;
            user-select: none;
            color: #64748b;
            transition: color 0.2s;
        }

        @media (max-width: 640px) {
            .seat-wrapper {
                width: 1.75rem;
                height: 1.75rem;
            }

            .seat-id {
                font-size: 0.5rem;
            }
        }
    </style>
@endpush

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Movie Header -->
        <div class="flex flex-col md:flex-row gap-10 items-start mb-16">
            <div class="w-full md:w-72 h-[450px] rounded-[2rem] overflow-hidden shadow-2xl flex-shrink-0 group relative">
                <img src="{{ asset($funcion->pelicula->img_path ?? 'assets/img/poster-default.jpg') }}"
                    alt="{{ $funcion->pelicula->nombre }}"
                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
            </div>
            <div class="flex-grow pt-4">
                <div class="flex items-center gap-3 mb-4">
                    <span
                        class="bg-primary text-white text-[10px] font-black px-4 py-1.5 rounded-full uppercase tracking-widest">{{ $funcion->pelicula->genero ?? 'Cine' }}</span>
                    <span
                        class="bg-slate-100 text-slate-500 text-[10px] font-black px-4 py-1.5 rounded-full uppercase tracking-widest">{{ $funcion->pelicula->clasificacion ?? 'TP' }}</span>
                </div>
                <h1 class="text-6xl font-black text-slate-900 tracking-tighter uppercase leading-[0.9] mb-8">
                    {{ $funcion->pelicula->nombre }}
                </h1>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                    <div class="flex flex-col">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Fecha</span>
                        <span
                            class="text-xl font-extrabold text-slate-800">{{ \Carbon\Carbon::parse($funcion->fecha)->translatedFormat('d \d\e F') }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Hora</span>
                        <span
                            class="text-xl font-extrabold text-slate-800">{{ \Carbon\Carbon::parse($funcion->hora_inicio)->format('h:i A') }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Sala</span>
                        <span class="text-xl font-extrabold text-slate-800">{{ $funcion->sala->nombre }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Duración</span>
                        <span
                            class="text-xl font-extrabold text-slate-800">{{ $funcion->pelicula->duracion ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            <!-- Seating Area -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-[3rem] shadow-xl shadow-slate-200/50 border border-slate-100 p-8 md:p-16">
                    <div class="cinema-container">
                        <div class="screen mb-32"></div>

                        <div class="seats-grid" id="seatsGrid">
                            @php
                                $maxCol = collect($mapa)->max('col');
                                $gridStyle = "grid-template-columns: repeat($maxCol, min-content);";
                            @endphp

                            @foreach($mapa as $item)
                                @php
                                    $asientoId = $item['fila'] . ($item['num'] ?? '');
                                    $dbAsiento = $funcion->asientos->where('codigo_asiento', $asientoId)->first();
                                    $statusClass = '';
                                    if ($dbAsiento) {
                                        $estado = strtoupper($dbAsiento->estado);
                                        if ($estado === 'VENDIDO')
                                            $statusClass = 'occupied';
                                        elseif ($estado === 'RESERVADO' && !$dbAsiento->isAvailable())
                                            $statusClass = 'blocked';
                                    }
                                    if ($item['tipo'] === 'pasillo')
                                        $statusClass = 'pasillo';
                                @endphp

                                <div class="seat-wrapper {{ $statusClass }}" data-asiento="{{ $asientoId }}"
                                    data-type="{{ $item['tipo'] }}"
                                    title="{{ $item['tipo'] === 'asiento' ? 'Asiento ' . $asientoId : 'Pasillo' }}"
                                    style="grid-column: {{ $item['col'] }}; grid-row: {{ ord(strtoupper($item['fila'])) - 64 }};">

                                    <div class="seat"></div>
                                    @if($item['tipo'] === 'asiento')
                                        <span class="seat-id">{{ $asientoId }}</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <!-- Legend -->
                        <div class="flex flex-wrap justify-center gap-6 mt-16 pt-8 border-t border-slate-50">
                            <div class="flex items-center gap-2">
                                <div class="w-4 h-4 rounded-full bg-slate-200"></div>
                                <span class="text-xs font-bold text-slate-500 uppercase">Disponible</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-4 h-4 rounded-full bg-primary"></div>
                                <span class="text-xs font-bold text-slate-500 uppercase">Seleccionado</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-4 h-4 rounded-full bg-amber-500"></div>
                                <span class="text-xs font-bold text-slate-500 uppercase">Reservado</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-4 h-4 rounded-full bg-red-500"></div>
                                <span class="text-xs font-bold text-slate-500 uppercase">Vendido</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Booking Sidebar -->
            <div class="lg:col-span-1">
                <div
                    class="bg-white rounded-[3rem] shadow-xl shadow-slate-200/50 border border-slate-100 p-8 sticky top-28">
                    <h3 class="text-xl font-black text-slate-900 uppercase tracking-tighter mb-8">Tu Reserva</h3>

                    <div class="space-y-6 mb-8">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Tipo
                                de Entrada</label>
                            <select id="precioSelect"
                                class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary outline-none transition-all">
                                @foreach($funcion->precios as $precio)
                                    <option value="{{ $precio->id }}" data-precio="{{ $precio->precio }}">
                                        {{ $precio->nombre }} - ${{ number_format($precio->precio, 0) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="bg-slate-50 rounded-3xl p-6">
                            <div class="flex justify-between items-center mb-4">
                                <span
                                    class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Asientos</span>
                                <span id="selectedSeatsList" class="text-sm font-black text-slate-900">Ninguno</span>
                            </div>
                            <div class="flex justify-between items-center mb-1">
                                <span
                                    class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Subtotal</span>
                                <span id="subtotalLabel" class="text-sm font-black text-slate-900">$0</span>
                            </div>
                            <div class="flex justify-between items-center border-t border-slate-200 mt-4 pt-4">
                                <span class="text-xs font-black text-slate-900 uppercase tracking-widest">Total</span>
                                <span id="totalLabel" class="text-3xl font-black text-primary">$0</span>
                            </div>
                        </div>
                    </div>

                    <button id="btnConfirmar" disabled onclick="procesarReserva()"
                        class="w-full bg-slate-200 text-slate-400 py-6 rounded-3xl font-black uppercase tracking-widest transition-all duration-300 transform active:scale-95 disabled:cursor-not-allowed">
                        Confirmar Selección
                    </button>

                    <p class="text-center text-[10px] text-slate-400 mt-6 px-4">
                        Al confirmar, tus asientos se bloquearán por 5 minutos para que completes el pago.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let selectedSeats = [];
        const precioSelect = document.getElementById('precioSelect');
        const selectedSeatsList = document.getElementById('selectedSeatsList');
        const subtotalLabel = document.getElementById('subtotalLabel');
        const totalLabel = document.getElementById('totalLabel');
        const btnConfirmar = document.getElementById('btnConfirmar');

        document.getElementById('seatsGrid').addEventListener('click', function (e) {
            const wrapper = e.target.closest('.seat-wrapper');
            if (wrapper && wrapper.dataset.type === 'asiento') {
                toggleSeat(wrapper);
            }
        });

        function toggleSeat(element) {
            if (element.classList.contains('occupied') || element.classList.contains('blocked')) {
                return;
            }

            const seatId = element.dataset.asiento;
            const index = selectedSeats.indexOf(seatId);

            if (index > -1) {
                selectedSeats.splice(index, 1);
                element.classList.remove('selected');
            } else {
                if (selectedSeats.length >= 8) {
                    Swal.fire({
                        title: '¡Límite alcanzado!',
                        text: 'Puedes seleccionar hasta 8 asientos por reserva.',
                        icon: 'warning',
                        confirmButtonColor: 'var(--primary)'
                    });
                    return;
                }
                selectedSeats.push(seatId);
                element.classList.add('selected');
            }

            updateSummary();
        }

        function updateSummary() {
            const selectedOption = precioSelect.options[precioSelect.selectedIndex];
            const precioUnitario = parseFloat(selectedOption.dataset.precio);
            const count = selectedSeats.length;
            const total = precioUnitario * count;

            selectedSeatsList.innerText = count > 0 ? selectedSeats.join(', ') : 'Ninguno';
            subtotalLabel.innerText = '$' + total.toLocaleString();
            totalLabel.innerText = '$' + total.toLocaleString();

            if (count > 0) {
                btnConfirmar.disabled = false;
                btnConfirmar.classList.remove('bg-slate-200', 'text-slate-400');
                btnConfirmar.classList.add('bg-primary', 'text-white', 'shadow-xl', 'shadow-blue-500/20');
            } else {
                btnConfirmar.disabled = true;
                btnConfirmar.classList.add('bg-slate-200', 'text-slate-400');
                btnConfirmar.classList.remove('bg-primary', 'text-white', 'shadow-xl', 'shadow-blue-500/20');
            }
        }

        precioSelect.addEventListener('change', updateSummary);

        function procesarReserva() {
            if (selectedSeats.length === 0) return;

            Swal.fire({
                title: 'Bloqueando asientos...',
                text: 'Por favor espera un momento',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            fetch("{{ route('public.reserva.store') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    funcion_id: {{ $funcion->id }},
                    asientos: selectedSeats,
                    precio_id: precioSelect.value
                })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Asientos Bloqueados!',
                            text: data.message,
                            confirmButtonText: 'Ir a Pagar',
                            confirmButtonColor: 'var(--primary)'
                        }).then(() => {
                            // Próxima fase: Redirigir a checkout con Stripe
                            // window.location.href = "{{ route('public.checkout') }}";
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message,
                            confirmButtonColor: 'var(--primary)'
                        });
                    }
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire('Error', 'No se pudo procesar la reserva', 'error');
                });
        }
    </script>
@endpush