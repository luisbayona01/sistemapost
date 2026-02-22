@extends('layouts.app')

@section('title', 'Selección de Asientos')

@push('css')
    <style>
        .cinema-container {
            perspective: 1000px;
            background: #0f172a;
            padding: 4rem 2rem;
            border-radius: 1.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            color: white;
            will-change: transform;
            /* Performance hint */
        }

        .screen-container {
            text-align: center;
            margin-bottom: 4rem;
        }

        .screen {
            background: linear-gradient(to bottom, #e2e8f0, #94a3b8);
            height: 8px;
            width: 70%;
            margin: 0 auto;
            border-radius: 50%;
            transform: rotateX(-15deg) scaleX(1.1);
            box-shadow: 0 20px 40px rgba(255, 255, 255, 0.1);
            /* Reduced shadow opacity */
            position: relative;
        }

        .screen::after {
            content: 'PANTALLA';
            position: absolute;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            color: #64748b;
            font-size: 0.75rem;
            letter-spacing: 0.5rem;
            font-weight: 700;
        }

        .seats-grid {
            display: grid;
            gap: 0.5rem;
            /* Reduced gap for tighter packing */
            justify-content: center;
            padding: 1rem;
            /* Contain layout recalculations */
            contain: layout paint;
        }

        .seat-wrapper {
            width: 2.5rem;
            height: 2.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            /* Optimized transitions */
            transition: transform 0.2s ease-out;
            position: relative;
        }

        .seat {
            width: 80%;
            height: 70%;
            background: #334155;
            border-radius: 0.5rem 0.5rem 0.25rem 0.25rem;
            position: absolute;
            bottom: 5px;
            pointer-events: none;
        }

        .seat::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 5%;
            width: 90%;
            height: 5px;
            background: #1e293b;
            border-radius: 0 0 0.5rem 0.5rem;
            pointer-events: none;
        }

        /* Hover only when not occupied/blocked for performance */
        .seat-wrapper:hover:not(.occupied):not(.blocked) {
            transform: scale(1.1);
        }

        .seat-wrapper:hover:not(.occupied):not(.blocked) .seat {
            background: #6366f1;
        }

        .seat-wrapper.selected .seat {
            background: #10b981 !important;
            box-shadow: 0 0 10px rgba(16, 185, 129, 0.4);
            /* Lighter shadow */
        }

        .seat-wrapper.blocked .seat {
            background: #f59e0b;
            cursor: not-allowed;
        }

        .seat-wrapper.occupied .seat {
            background: #ef4444;
            cursor: not-allowed;
        }

        .seat-wrapper.pasillo {
            cursor: default;
            visibility: hidden;
            pointer-events: none;
            /* Prevent clicks on pasillo */
        }

        .seat-id {
            font-size: 0.65rem;
            font-weight: 700;
            z-index: 10;
            user-select: none;
            pointer-events: none;
            color: #94a3b8;
        }

        .legend {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-top: 3rem;
            padding: 1rem;
            background: rgba(30, 41, 59, 0.5);
            border-radius: 1rem;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
        }

        .legend-box {
            width: 1.25rem;
            height: 1.25rem;
            border-radius: 0.25rem;
        }
    </style>
@endpush

@section('content')
    <div class="p-6">
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold">{{ $funcion->pelicula?->titulo ?? 'Sin título' }}</h1>
                <p class="text-gray-500">{{ $sala->nombre }} | {{ $funcion->fecha_hora->format('d/m/Y H:i') }}</p>
            </div>
            <a href="{{ route('cinema.funciones.index') }}"
                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                Volver a funciones
            </a>
        </div>

        <div class="cinema-container">
            <div class="screen-container">
                <div class="screen"></div>
            </div>

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
                            elseif ($estado === 'BLOQUEADO' && !$dbAsiento->isAvailable())
                                $statusClass = 'blocked';
                        }
                        if ($item['tipo'] === 'pasillo')
                            $statusClass = 'pasillo';
                    @endphp

                    <div class="seat-wrapper {{ $statusClass }}" data-asiento="{{ $asientoId }}"
                        title="{{ $item['tipo'] === 'asiento' ? 'Asiento ' . $asientoId : 'Pasillo' }}"
                        style="grid-column: {{ $item['col'] }};">
                        <div class="seat"></div>
                        @if($item['tipo'] === 'asiento')
                            <span class="seat-id">{{ $asientoId }}</span>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="legend">
                <div class="legend-item">
                    <div class="legend-box bg-slate-600"></div>
                    <span>Disponible</span>
                </div>
                <div class="legend-item">
                    <div class="legend-box bg-emerald-500"></div>
                    <span>Seleccionado</span>
                </div>
                <div class="legend-item">
                    <div class="legend-box bg-amber-500"></div>
                    <span>Bloqueado</span>
                </div>
                <div class="legend-item">
                    <div class="legend-box bg-red-500"></div>
                    <span>Vendido</span>
                </div>
            </div>

            <div class="mt-8 text-center" id="actionPanel" style="display: none;">
                <div
                    class="bg-indigo-900/30 p-8 rounded-3xl border border-indigo-500/30 inline-block max-w-lg w-full shadow-2xl backdrop-blur-md">
                    <div class="flex justify-between items-center mb-6">
                        <p class="text-xl text-white">Asientos: <span id="selectedSeatLabel"
                                class="font-bold text-emerald-400"></span></p>
                        <div class="text-right">
                            <p class="text-xs text-indigo-300 uppercase font-bold">Total a Pagar</p>
                            <p id="totalLabel" class="text-3xl font-black text-white">$0</p>
                            <p class="text-[10px] font-bold text-green-400 mt-1">
                                Precio: ${{ number_format($funcion->precio_base ?? 30000, 0) }} por boleto
                            </p>
                        </div>
                    </div>

                    <div class="mb-6 text-left">
                        <label class="block text-gray-400 text-sm font-bold mb-2">TIPO DE ENTRADA</label>
                        <select id="precioEntrada"
                            class="w-full bg-slate-800 border border-slate-700 text-white rounded-xl p-3 focus:ring-2 focus:ring-emerald-500 outline-none">
                            @foreach($precios as $p)
                                <option value="{{ $p->id }}" data-precio="{{ $p->precio }}">
                                    {{ $p->nombre }} - ${{ number_format($p->precio, 0) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex justify-center">
                        <button onclick="venderAhora()"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white px-10 py-5 rounded-2xl font-black uppercase tracking-widest transition shadow-xl shadow-emerald-900/40 flex items-center gap-3">
                            <span>Confirmar Selección</span>
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let selectedSeats = [];

        // EVENT DELEGATION: Efficient handling of many seat elements
        document.getElementById('seatsGrid').addEventListener('click', function (e) {
            const wrapper = e.target.closest('.seat-wrapper');
            // Check if clicked element is a seat wrapper and not disabled
            if (wrapper && !wrapper.classList.contains('pasillo')) {
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
                // Deseleccionar
                selectedSeats.splice(index, 1);
                element.classList.remove('selected');
            } else {
                // Limit selection allows safer memory usage
                if (selectedSeats.length >= 10) {
                    Swal.fire('Límite alcanzado', 'Puedes seleccionar máximo 10 asientos por transacción', 'warning');
                    return;
                }

                // Seleccionar
                selectedSeats.push(seatId);
                element.classList.add('selected');
            }

            updateActionPanel();
        }

        function updateActionPanel() {
            const panel = document.getElementById('actionPanel');
            const label = document.getElementById('selectedSeatLabel');
            const totalLabel = document.getElementById('totalLabel');
            const precioSelect = document.getElementById('precioEntrada');

            if (selectedSeats.length > 0) {
                panel.style.display = 'block';
                label.innerText = selectedSeats.join(', ');

                // Obtener precio desde el data attribute
                const selectedOption = precioSelect.options[precioSelect.selectedIndex];
                const precioBase = parseFloat(selectedOption.getAttribute('data-precio')) || 0;

                const cant = selectedSeats.length;
                const tarifaUnitaria = 0; // ANULADO TEMPORALMENTE (Antes 4000)
                const total = (precioBase + tarifaUnitaria) * cant;

                if (totalLabel) {
                    totalLabel.innerText = '$' + total.toLocaleString();
                }
            } else {
                panel.style.display = 'none';
            }
        }

        // Listener para cambiar el total cuando se cambie el tipo de entrada
        document.getElementById('precioEntrada').addEventListener('change', updateActionPanel);

        function reservarAsiento() {
            if (selectedSeats.length === 0) return;

            Swal.fire({
                title: 'Procesando reserva...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch("{{ route('cinema.reservar', [], false) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    funcion_id: {{ $funcion->id }},
                    asientos: selectedSeats
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Reservado!',
                            text: 'Los asientos han sido bloqueados por 5 minutos.',
                            timer: 2000
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: data.message });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'No se pudo procesar la solicitud', 'error');
                });
        }

        function venderAhora() {
            if (selectedSeats.length === 0) return;

            const precioId = document.getElementById('precioEntrada').value;

            Swal.fire({
                title: 'Agregando al Carrito...',
                text: 'Preparando tus entradas',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            fetch("{{ route('pos.agregar.boleto') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    funcion_id: {{ $funcion->id }},
                    asientos: selectedSeats,
                    precio_id: precioId
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Agregado!',
                            text: 'Asientos agregados al carrito POS.',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = "{{ route('pos.index') }}";
                        });
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'No se pudo agregar al carrito', 'error');
                });
        }

                                                    // 📡 REAL-TIME CORE (Laravel Echo)
                                                    // Descomentar cuando Pusher/Soketi esté configurado
                                                    /*
                                                    if (typeof Echo !== 'undefined') {
                                                        Echo.channel('cinema.{{ $funcion->id }}')
            .listen('AsientoBloqueado', (e) => {
                const element = document.querySelector(`.seat-wrapper[data-asiento="${e.codigo_asiento}"]`);
                if (element && e.estado === 'bloqueado') {
                    if (element.classList.contains('selected')) {
                        const index = selectedSeats.indexOf(e.codigo_asiento);
                        if (index > -1) selectedSeats.splice(index, 1);
                        updateActionPanel();
                        Swal.fire('Aviso', 'Este asiento acaba de ser reservado por otro usuario.', 'info');
                    }
                    element.classList.add('blocked');
                    element.style.pointerEvents = 'none'; // Lock interaction
                }
            });
                                                    }
                                                    */
    </script>
@endpush
