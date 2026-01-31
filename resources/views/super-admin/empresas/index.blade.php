@extends('layouts.app')

@section('title', 'Empresas - Super Admin')

@section('content')
<div class="container-fluid px-6 py-10 max-w-7xl mx-auto">
    <!-- Encabezado -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Gestión de Empresas</h1>
                <p class="text-gray-600">Control total de empresas y sus suscripciones</p>
            </div>
            <a href="{{ route('super-admin.dashboard') }}" class="text-blue-600 hover:underline">
                ← Volver al Dashboard
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Buscar empresa</label>
                <input type="text" id="searchInput" placeholder="Nombre o NIT..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                <select id="filterEstado" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Todos --</option>
                    <option value="activa">Activa</option>
                    <option value="suspendida">Suspendida</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Suscripción</label>
                <select id="filterSuscripcion" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Todas --</option>
                    <option value="active">Activa</option>
                    <option value="trial">Trial</option>
                    <option value="past_due">Vencida</option>
                    <option value="cancelled">Cancelada</option>
                </select>
            </div>
            <div class="flex items-end">
                <button onclick="resetFilters()" class="w-full bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                    Limpiar Filtros
                </button>
            </div>
        </div>
    </div>

    <!-- Tabla de empresas -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Empresa</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Plan</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">NIT</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Estado</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Suscripción</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Próximo Pago</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($empresas as $empresa)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $empresa->razon_social }}</div>
                            <div class="text-xs text-gray-500">{{ $empresa->nombre_comercial }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $empresa->plan?->nombre ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $empresa->nit }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $empresa->estado === 'activa' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($empresa->estado) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{
                                $empresa->estado_suscripcion === 'active' ? 'bg-green-100 text-green-800' :
                                ($empresa->estado_suscripcion === 'trial' ? 'bg-blue-100 text-blue-800' :
                                'bg-red-100 text-red-800')
                            }}">
                                {{ ucfirst($empresa->estado_suscripcion) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $empresa->fecha_proximo_pago?->format('d/m/Y') ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <div class="flex gap-2">
                                <a href="{{ route('super-admin.empresas.show', $empresa) }}"
                                   class="text-blue-600 hover:text-blue-800">Ver</a>
                                @if($empresa->estado === 'activa')
                                    <form method="POST" action="{{ route('super-admin.empresas.suspend', $empresa) }}"
                                          style="display: inline;" onsubmit="return confirm('¿Suspender empresa?')">
                                        @csrf
                                        <button type="submit" class="text-red-600 hover:text-red-800">Suspender</button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('super-admin.empresas.activate', $empresa) }}"
                                          style="display: inline;">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-800">Activar</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-600">
                            No hay empresas registradas
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="px-6 py-4 border-t bg-gray-50">
            {{ $empresas->links() }}
        </div>
    </div>
</div>

<script>
function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('filterEstado').value = '';
    document.getElementById('filterSuscripcion').value = '';
    location.reload();
}
</script>
@endsection
