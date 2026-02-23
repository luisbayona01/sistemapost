@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">

        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold">📊 Dashboard Ejecutivo</h1>
                @php
                    $accountingService = app(\App\Services\AccountingService::class);
                    $fechaOperativa = $accountingService->getActiveDay(auth()->user()->empresa_id);
                @endphp
                <p class="text-sm text-gray-500 font-bold uppercase tracking-tight">
                    DÍA OPERATIVO: <span class="text-emerald-600">{{ $fechaOperativa->format('d/m/Y') }}</span>
                    <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 animate-pulse ml-1"></span>
                </p>
            </div>

            <div class="flex items-center gap-4">
                @if(auth()->user()->hasRole(['Root', 'Gerente', 'administrador']))
                    <button onclick="confirmarCierreDia()"
                        class="bg-red-50 text-red-700 px-4 py-2 rounded-xl text-xs font-black uppercase hover:bg-red-600 hover:text-white transition-all border border-red-100 shadow-sm">
                        Cerrar Día Operativo
                    </button>
                @endif
                <a href="{{ route('admin.alertas.index') }}"
                    class="relative bg-white border rounded-full p-2 hover:bg-gray-50 shadow-sm">
                    🔔
                    @if($alertasActivas->count() > 0)
                        <span
                            class="absolute -top-1 -right-1 bg-red-500 text-white text-xs w-5 h-5 flex items-center justify-center rounded-full animate-pulse border-2 border-white">
                            {{ $alertasActivas->count() }}
                        </span>
                    @endif
                </a>
            </div>
        </div>

        @if($alertasActivas->count() > 0)
            <!-- WIDGET DE ALERTAS CRÍTICAS -->
            <div class="mb-8 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg shadow-sm">
                <h3 class="font-bold text-red-800 mb-3 flex items-center">
                    ⚠️ Atención Requerida ({{ $alertasActivas->count() }})
                    <a href="{{ route('admin.alertas.index') }}" class="text-xs ml-auto text-red-600 hover:underline">Ver todas
                        →</a>
                </h3>
                <div class="space-y-2">
                    @foreach($alertasActivas as $alerta)
                        <div class="bg-white p-3 rounded shadow-sm flex justify-between items-center">
                            <div class="flex items-center gap-2">
                                <span class="text-xl">
                                    {{ $alerta->categoria == 'INVENTARIO' ? '🍿' : ($alerta->categoria == 'OCUPACION' ? '🎬' : ($alerta->categoria == 'CAJA' ? '💰' : 'ℹ️')) }}
                                </span>
                                <div>
                                    <p class="text-sm font-bold text-gray-800">{{ $alerta->titulo }}</p>
                                    <p class="text-xs text-gray-600 truncate max-w-md">{{ $alerta->mensaje }}</p>
                                </div>
                            </div>

                            @if(!$alerta->resuelta)
                                <form action="{{ route('admin.alertas.resolver', $alerta->id) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded hover:bg-green-200">
                                        ✓ Resolver
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- KPIs de HOY -->
        <div class="mb-8">
            <h2 class="text-xl font-bold mb-4 flex items-center">
                <span class="bg-blue-600 text-white px-3 py-1 rounded mr-3">HOY</span>
                {{ \Carbon\Carbon::today()->locale('es')->isoFormat('dddd, D [de] MMMM') }}
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Ingresos -->
                <div class="bg-white rounded-lg shadow p-6">
                    <p class="text-gray-600 text-sm mb-2">💰 Ingresos Totales</p>
                    <p class="text-3xl font-bold text-green-600">${{ number_format($hoy['ingreso_total'], 0) }}</p>
                    @php
                        $cambioIngreso = $ayer['ingreso_total'] > 0
                            ? (($hoy['ingreso_total'] - $ayer['ingreso_total']) / $ayer['ingreso_total']) * 100
                            : 0;
                    @endphp
                    <p class="text-sm mt-2 {{ $cambioIngreso >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $cambioIngreso >= 0 ? '↗' : '↘' }} {{ number_format(abs($cambioIngreso), 1) }}% vs ayer
                    </p>
                </div>

                <!-- Tickets -->
                <div class="bg-white rounded-lg shadow p-6">
                    <p class="text-gray-600 text-sm mb-2">🎟️ Tickets Vendidos</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $hoy['tickets_vendidos'] }}</p>
                    @php
                        $cambioTickets = $ayer['tickets_vendidos'] > 0
                            ? (($hoy['tickets_vendidos'] - $ayer['tickets_vendidos']) / $ayer['tickets_vendidos']) * 100
                            : 0;
                    @endphp
                    <p class="text-sm mt-2 {{ $cambioTickets >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $cambioTickets >= 0 ? '↗' : '↘' }} {{ number_format(abs($cambioTickets), 1) }}% vs ayer
                    </p>
                </div>

                <!-- Asistencia -->
                <div class="bg-white rounded-lg shadow p-6">
                    <p class="text-gray-600 text-sm mb-2">📈 Asistencia Media</p>
                    <p class="text-3xl font-bold text-purple-600">{{ $hoy['ocupacion_promedio'] }}%</p>
                    <p class="text-sm mt-2 text-gray-500">
                        Ayer: {{ $ayer['ocupacion_promedio'] }}%
                    </p>
                </div>

                <!-- Dulcería -->
                <div class="bg-white rounded-lg shadow p-6">
                    <p class="text-gray-600 text-sm mb-2">🍿 Dulcería (Ventas)</p>
                    <p class="text-3xl font-bold text-orange-600">{{ $hoy['productos_vendidos'] }}</p>
                    @php
                        $cambioProductos = $ayer['productos_vendidos'] > 0
                            ? (($hoy['productos_vendidos'] - $ayer['productos_vendidos']) / $ayer['productos_vendidos']) * 100
                            : 0;
                    @endphp
                    <p class="text-sm mt-2 {{ $cambioProductos >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $cambioProductos >= 0 ? '↗' : '↘' }} {{ number_format(abs($cambioProductos), 1) }}% vs
                        ayer
                    </p>
                </div>
            </div>
        </div>

        <!-- TENDENCIAS HISTÓRICAS (NUEVO) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

            <!-- Gráfico Ventas 30 Días (CSS Puro) -->
            <div class="bg-white rounded-lg shadow p-6 lg:col-span-2">
                <div class="flex justify-between items-end mb-4">
                    <h3 class="text-lg font-bold">📊 Ventas últimos 30 días</h3>
                    <span class="text-xs text-gray-500">Escala Automática</span>
                </div>

                <div class="h-48 flex items-end gap-1 w-full">
                    @php
                        $maxVenta = $tendenciaVentas->max('total');
                        if ($maxVenta == 0)
                            $maxVenta = 1; // Evitar div por cero
                    @endphp

                    @foreach($tendenciaVentas as $dato)
                        @php $altura = ($dato['total'] / $maxVenta) * 100; @endphp
                        <div class="flex-1 flex flex-col justify-end group relative">
                            <!-- Tooltip -->
                            <div
                                class="opacity-0 group-hover:opacity-100 absolute bottom-full mb-2 left-1/2 transform -translate-x-1/2 bg-black text-white text-xs rounded py-1 px-2 pointer-events-none z-10 w-max">
                                {{ $dato['fecha'] }}: ${{ number_format($dato['total'], 0) }}
                            </div>
                            <!-- Barra -->
                            <div class="bg-blue-500 rounded-t w-full hover:bg-blue-600 transition-all cursor-pointer"
                                style="height: {{ $altura }}%"></div>
                        </div>
                    @endforeach
                </div>
                <!-- Eje X simplificado -->
                <div class="flex justify-between mt-2 text-xs text-gray-400">
                    <span>{{ $tendenciaVentas->first()['fecha'] ?? '' }}</span>
                    <span>{{ $tendenciaVentas->last()['fecha'] ?? '' }}</span>
                </div>
            </div>

            <!-- Ocupación Semanal (Barras Horizontales) -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold mb-4">📆 Asistencia por Día</h3>
                <div class="space-y-3">
                    @foreach($tendenciaOcupacion as $dia => $porcentaje)
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="font-medium">{{ $dia }}</span>
                                <span class="font-bold text-gray-700">{{ $porcentaje }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-purple-600 h-2.5 rounded-full" style="width: {{ $porcentaje }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

        <!-- Comparativos KPI -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

            <!-- Esta Semana -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold mb-4 border-b pb-2">📅 Esta Semana</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Ingresos:</span>
                        <span
                            class="font-bold border-b border-gray-100 w-32 text-right">${{ number_format($estaSemana['ingreso_total'], 0) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tickets:</span>
                        <span
                            class="font-bold border-b border-gray-100 w-32 text-right">{{ $estaSemana['tickets_vendidos'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Asistencia:</span>
                        <span
                            class="font-bold border-b border-gray-100 w-32 text-right">{{ $estaSemana['ocupacion_promedio'] }}%</span>
                    </div>
                    @php
                        $cambioSemanaIngreso = $semanaAnterior['ingreso_total'] > 0
                            ? (($estaSemana['ingreso_total'] - $semanaAnterior['ingreso_total']) / $semanaAnterior['ingreso_total']) * 100
                            : 0;
                    @endphp
                    <div class="pt-2 border-t">
                        <p class="text-sm {{ $cambioSemanaIngreso >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $cambioSemanaIngreso >= 0 ? '↗' : '↘' }}
                            {{ number_format(abs($cambioSemanaIngreso), 1) }}%
                            vs semana anterior
                        </p>
                    </div>
                </div>
            </div>

            <!-- Este Mes -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold mb-4 border-b pb-2">📆 Este Mes</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Ingresos:</span>
                        <span
                            class="font-bold border-b border-gray-100 w-32 text-right">${{ number_format($esteMes['ingreso_total'], 0) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tickets:</span>
                        <span
                            class="font-bold border-b border-gray-100 w-32 text-right">{{ $esteMes['tickets_vendidos'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Asistencia:</span>
                        <span
                            class="font-bold border-b border-gray-100 w-32 text-right">{{ $esteMes['ocupacion_promedio'] }}%</span>
                    </div>
                    @php
                        $cambioMesIngreso = $mesAnterior['ingreso_total'] > 0
                            ? (($esteMes['ingreso_total'] - $mesAnterior['ingreso_total']) / $mesAnterior['ingreso_total']) * 100
                            : 0;
                    @endphp
                    <div class="pt-2 border-t">
                        <p class="text-sm {{ $cambioMesIngreso >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $cambioMesIngreso >= 0 ? '↗' : '↘' }} {{ number_format(abs($cambioMesIngreso), 1) }}%
                            vs mes
                            anterior
                        </p>
                    </div>
                </div>
            </div>

        </div>

        <!-- Accesos Rápidos -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('admin.dashboard.top-performers') }}"
                class="bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-lg p-6 hover:shadow-lg transition transform hover:-translate-y-1">
                <p class="text-3xl mb-2">🏆</p>
                <p class="font-bold text-lg">Ranking</p>
                <p class="text-sm opacity-90">Mejores productos y horarios</p>
            </a>

            <a href="{{ route('admin.dashboard.ocupacion') }}"
                class="bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg p-6 hover:shadow-lg transition transform hover:-translate-y-1">
                <p class="text-3xl mb-2">📊</p>
                <p class="font-bold text-lg">Asistencia</p>
                <p class="text-sm opacity-90">Análisis detallado</p>
            </a>

            <a href="{{ route('admin.dashboard.confiteria') }}"
                class="bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg p-6 hover:shadow-lg transition transform hover:-translate-y-1">
                <p class="text-3xl mb-2">🍿</p>
                <p class="font-bold text-lg">Dulcería</p>
                <p class="text-sm opacity-90">Rentabilidad y Stock</p>
            </a>

            <!-- NUEVO LINK: REPORTE DIARIO -->
            <a href="{{ route('admin.ventas.diarias') }}"
                class="bg-gradient-to-r from-slate-700 to-slate-800 text-white rounded-lg p-6 hover:shadow-lg transition transform hover:-translate-y-1">
                <p class="text-3xl mb-2">📅</p>
                <p class="font-bold text-lg">Reporte Diario</p>
                <p class="text-sm opacity-90">Detalle de Ventas Hoy</p>
            </a>

            <!-- NUEVO LINK: REPORTE POR PELICULA -->
            <a href="{{ route('admin.reportes.peliculas') }}"
                class="bg-gradient-to-r from-amber-500 to-orange-600 text-white rounded-lg p-6 hover:shadow-lg transition transform hover:-translate-y-1">
                <p class="text-3xl mb-2">🎬</p>
                <p class="font-bold text-lg">Ventas por Película</p>
                <p class="text-sm opacity-90">Análisis de Taquilla (Mes)</p>
            </a>
        </div>

    </div>
    <script>
        async function confirmarCierreDia() {
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
    </script>
@endsection