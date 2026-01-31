@extends('layouts.app')

@section('title','Panel')

@push('css')
<link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')
<div class="flex-1 flex flex-col">
    <div class="px-6 md:px-8 py-6 md:py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900">Panel de Control</h1>
            <p class="text-gray-600 mt-2">Bienvenido de vuelta. Aquí está el resumen de tu negocio.</p>
        </div>

        <!-- Breadcrumb -->
        <x-breadcrumb.template class="mb-6">
            <x-breadcrumb.item active='true' content="Panel" />
        </x-breadcrumb.template>

        <!-- KPI Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <?php
                use App\Models\Cliente;
                use App\Models\Compra;
                use App\Models\Producto;
                use App\Models\User;

                $clientes = count(Cliente::all());
                $compras = count(Compra::all());
                $productos = count(Producto::all());
                $users = count(User::all());
            ?>

            <!-- Clientes Card -->
            <x-dashboard-stat-card
                title="Clientes"
                :value="$clientes"
                icon="fa-solid fa-users"
                color="blue"
                actionUrl="{{ route('clientes.index') }}"
                actionLabel="Ver clientes" />

            <!-- Compras Card -->
            <x-dashboard-stat-card
                title="Compras"
                :value="$compras"
                icon="fa-solid fa-shopping-cart"
                color="green"
                actionUrl="{{ route('compras.index') }}"
                actionLabel="Ver compras" />

            <!-- Productos Card -->
            <x-dashboard-stat-card
                title="Productos"
                :value="$productos"
                icon="fa-solid fa-cube"
                color="purple"
                actionUrl="{{ route('productos.index') }}"
                actionLabel="Ver productos" />

            <!-- Usuarios Card -->
            <x-dashboard-stat-card
                title="Usuarios"
                :value="$users"
                icon="fa-solid fa-user-shield"
                color="amber"
                actionUrl="{{ route('users.index') }}"
                actionLabel="Ver usuarios" />
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Stock Bajo Chart -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-amber-600"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">Stock Bajo</p>
                            <p class="text-xs text-gray-600 mt-0.5">5 productos bajo vigilancia</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <canvas id="productosChart" height="300"></canvas>
                </div>
            </div>

            <!-- Ventas Últimos 7 Días Chart -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-chart-line text-blue-600"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">Ventas</p>
                            <p class="text-xs text-gray-600 mt-0.5">Últimos 7 días</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <canvas id="ventasChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Footer Info -->
        <div class="text-center text-sm text-gray-600 py-4">
            <p>Los datos se actualizan en tiempo real</p>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" crossorigin="anonymous"></script>
<script src="{{ asset('js/datatables-simple-demo.js') }}"></script>

<script>
    // Configuración global de Chart.js
    Chart.defaults.global.defaultFontFamily = '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif';
    Chart.defaults.global.defaultFontColor = '#6B7280';

    // Gráfico de Ventas
    let datosVenta = @json($totalVentasPorDia);

    const fechas = datosVenta.map(venta => {
        const [year, month, day] = venta.fecha.split('-');
        return `${day}/${month}`;
    });
    const montos = datosVenta.map(venta => parseFloat(venta.total));

    const ventasCtx = document.getElementById('ventasChart').getContext('2d');
    new Chart(ventasCtx, {
        type: 'line',
        data: {
            labels: fechas,
            datasets: [{
                label: "Ventas",
                lineTension: 0.4,
                backgroundColor: "rgba(59, 130, 246, 0.1)",
                borderColor: "#3B82F6",
                pointRadius: 6,
                pointBackgroundColor: "#3B82F6",
                pointBorderColor: "#fff",
                pointHoverRadius: 8,
                pointHoverBackgroundColor: "#3B82F6",
                pointHitRadius: 50,
                pointBorderWidth: 2,
                data: montos,
                borderWidth: 2,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                xAxes: [{
                    gridLines: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        fontColor: '#9CA3AF'
                    }
                }],
                yAxes: [{
                    ticks: {
                        min: 0,
                        fontColor: '#9CA3AF',
                        beginAtZero: true
                    },
                    gridLines: {
                        color: "rgba(209, 213, 219, 0.3)",
                        drawBorder: false
                    }
                }],
            },
            legend: {
                display: false
            },
            tooltips: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                cornerRadius: 4,
                caretPadding: 10,
                displayColors: false,
                callbacks: {
                    label: function(tooltipItem) {
                        return 'Ventas: $' + tooltipItem.value.toFixed(2);
                    }
                }
            }
        }
    });

    // Gráfico de Stock Bajo
    let datosProductos = @json($productosStockBajo);
    const nombres = datosProductos.map(obj => obj.nombre);
    const stock = datosProductos.map(i => i.cantidad);

    const productosCtx = document.getElementById('productosChart').getContext('2d');
    new Chart(productosCtx, {
        type: 'horizontalBar',
        data: {
            labels: nombres,
            datasets: [{
                label: 'Stock',
                backgroundColor: "#F59E0B",
                borderColor: "#FBBF24",
                data: stock,
                borderWidth: 1,
                hoverBackgroundColor: "#D97706"
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                xAxes: [{
                    ticks: {
                        beginAtZero: true,
                        fontColor: '#9CA3AF'
                    },
                    gridLines: {
                        color: "rgba(209, 213, 219, 0.3)",
                        drawBorder: false
                    }
                }],
                yAxes: [{
                    gridLines: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        fontColor: '#9CA3AF'
                    }
                }]
            },
            legend: {
                display: false
            },
            tooltips: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                cornerRadius: 4,
                caretPadding: 10,
                displayColors: false,
                callbacks: {
                    label: function(tooltipItem) {
                        return 'Stock: ' + tooltipItem.value + ' unidades';
                    }
                }
            }
        }
    });
</script>
@endpush
