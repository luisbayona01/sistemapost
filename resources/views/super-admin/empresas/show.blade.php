@extends('layouts.app')

@section('title', $empresa->razon_social . ' - Detalle')

@section('content')
<div class="container-fluid px-6 py-10 max-w-7xl mx-auto">
    <!-- Encabezado -->
    <div class="mb-8">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $empresa->razon_social }}</h1>
                <p class="text-gray-600 mt-1">NIT: {{ $empresa->nit }}</p>
            </div>
            <a href="{{ route('super-admin.empresas.index') }}" class="text-blue-600 hover:underline">
                ← Volver al listado
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded">
        {{ session('success') }}
    </div>
    @endif

    <!-- Grid de información -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Información General -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Información General</h3>
            <dl class="space-y-3 text-sm">
                <div>
                    <dt class="text-gray-600 font-medium">Razón Social</dt>
                    <dd class="text-gray-900">{{ $empresa->razon_social }}</dd>
                </div>
                <div>
                    <dt class="text-gray-600 font-medium">Nombre Comercial</dt>
                    <dd class="text-gray-900">{{ $empresa->nombre_comercial }}</dd>
                </div>
                <div>
                    <dt class="text-gray-600 font-medium">NIT</dt>
                    <dd class="text-gray-900">{{ $empresa->nit }}</dd>
                </div>
                <div>
                    <dt class="text-gray-600 font-medium">Email</dt>
                    <dd class="text-gray-900">{{ $empresa->email }}</dd>
                </div>
                <div>
                    <dt class="text-gray-600 font-medium">Teléfono</dt>
                    <dd class="text-gray-900">{{ $empresa->telefono ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-600 font-medium">Estado</dt>
                    <dd>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $empresa->estado === 'activa' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ ucfirst($empresa->estado) }}
                        </span>
                    </dd>
                </div>
            </dl>
        </div>

        <!-- Suscripción -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Suscripción</h3>
            <dl class="space-y-3 text-sm">
                <div>
                    <dt class="text-gray-600 font-medium">Plan</dt>
                    <dd class="text-gray-900">{{ $empresa->plan?->nombre ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-600 font-medium">Estado</dt>
                    <dd>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{
                            $empresa->estado_suscripcion === 'active' ? 'bg-green-100 text-green-800' :
                            ($empresa->estado_suscripcion === 'trial' ? 'bg-blue-100 text-blue-800' :
                            'bg-red-100 text-red-800')
                        }}">
                            {{ ucfirst($empresa->estado_suscripcion) }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-gray-600 font-medium">Próximo Pago</dt>
                    <dd class="text-gray-900">{{ $empresa->fecha_proximo_pago?->format('d/m/Y') ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-600 font-medium">Vencimiento</dt>
                    <dd class="text-gray-900">{{ $empresa->fecha_vencimiento_suscripcion?->format('d/m/Y') ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-600 font-medium">Customer Stripe</dt>
                    <dd class="text-gray-900 font-mono text-xs">{{ $empresa->stripe_customer_id ? substr($empresa->stripe_customer_id, 0, 15) . '...' : 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-600 font-medium">Subscription ID</dt>
                    <dd class="text-gray-900 font-mono text-xs">{{ $empresa->stripe_subscription_id ? substr($empresa->stripe_subscription_id, 0, 15) . '...' : 'N/A' }}</dd>
                </div>
            </dl>
        </div>

        <!-- Tarifas -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Tarifas y Cobros</h3>
            <dl class="space-y-3">
                <div>
                    <dt class="text-gray-600 font-medium text-sm">Porcentaje de Tarifa</dt>
                    <dd class="text-2xl font-bold text-gray-900">{{ number_format($empresa->tarifa_servicio_porcentaje, 2) }}%</dd>
                </div>
                <div>
                    <dt class="text-gray-600 font-medium text-sm">Monto Acumulado</dt>
                    <dd class="text-2xl font-bold text-green-600">${{ number_format($empresa->tarifa_servicio_monto, 2, ',', '.') }}</dd>
                </div>
                <div>
                    <dt class="text-gray-600 font-medium text-sm">Moneda</dt>
                    <dd class="text-gray-900">{{ $empresa->moneda?->nombre ?? 'N/A' }} ({{ $empresa->moneda?->simbolo ?? 'N/A' }})</dd>
                </div>
                <div>
                    <dt class="text-gray-600 font-medium text-sm">Impuesto</dt>
                    <dd class="text-gray-900">{{ $empresa->porcentaje_impuesto }}% {{ $empresa->abreviatura_impuesto }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm font-medium">Total de Ventas</p>
            <p class="text-2xl font-bold text-gray-900 mt-2">${{ number_format($estadisticas['total_ventas'], 2, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm font-medium">Número de Ventas</p>
            <p class="text-2xl font-bold text-gray-900 mt-2">{{ $estadisticas['numero_ventas'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm font-medium">Usuarios</p>
            <p class="text-2xl font-bold text-gray-900 mt-2">{{ $estadisticas['usuarios'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm font-medium">Tarifa Acumulada</p>
            <p class="text-2xl font-bold text-green-600 mt-2">${{ number_format($estadisticas['tarifa_acumulada'], 2, ',', '.') }}</p>
        </div>
    </div>

    <!-- Acciones -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Acciones</h3>
        <div class="flex gap-4 flex-wrap">
            @if($empresa->estado === 'activa')
                <form method="POST" action="{{ route('super-admin.empresas.suspend', $empresa) }}"
                      style="display: inline;" onsubmit="return confirm('¿Está seguro? Los usuarios no podrán acceder.')">
                    @csrf
                    <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition">
                        Suspender Empresa
                    </button>
                </form>
            @else
                <form method="POST" action="{{ route('super-admin.empresas.activate', $empresa)  }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition">
                        Activar Empresa
                    </button>
                </form>
            @endif
            <a href="{{ route('super-admin.empresas.index') }}" class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700 transition">
                Volver
            </a>
        </div>
    </div>

    <!-- Usuarios -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Usuarios de la Empresa</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr class="border-b">
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Nombre</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Email</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Rol</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($empresa->users as $user)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $user->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $user->email }}</td>
                        <td class="px-6 py-4 text-sm">
                            @foreach($user->roles as $role)
                                <span class="px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $role->name }}
                                </span>
                            @endforeach
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <span class="px-2 py-1 rounded text-xs font-medium {{ $user->estado === 'activo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($user->estado) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-600 text-sm">
                            No hay usuarios registrados
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
