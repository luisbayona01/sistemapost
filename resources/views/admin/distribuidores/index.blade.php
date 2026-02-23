@extends('layouts.app')

@section('title', 'Distribuidores de Cine')

@section('content')
    <div class="px-6 py-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Distribuidores</h1>
                <p class="text-slate-600 mt-1">Gestión de casas distribuidoras para el catálogo de cine.</p>
            </div>
            <a href="{{ route('distribuidores.create') }}"
                class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg transition-colors shadow-sm">
                <i class="fas fa-plus mr-2"></i> Nuevo Distribuidor
            </a>
        </div>

        <x-breadcrumb.template class="mb-6">
            <x-breadcrumb.item :href="route('admin.dashboard.index')" content="Administración" />
            <x-breadcrumb.item active='true' content="Distribuidores" />
        </x-breadcrumb.template>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="px-6 py-4 text-sm font-bold text-slate-700 uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-4 text-sm font-bold text-slate-700 uppercase tracking-wider">Contacto</th>
                            <th class="px-6 py-4 text-sm font-bold text-slate-700 uppercase tracking-wider">Teléfono / Email
                            </th>
                            <th class="px-6 py-4 text-sm font-bold text-slate-700 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-4 text-sm font-bold text-slate-700 uppercase tracking-wider text-right">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($distribuidores as $distribuidor)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-semibold text-slate-900">{{ $distribuidor->nombre }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-slate-600">
                                    {{ $distribuidor->contacto ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-slate-900">{{ $distribuidor->telefono }}</div>
                                    <div class="text-xs text-slate-500">{{ $distribuidor->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($distribuidor->activo)
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                            Activo
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">
                                            Inactivo
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('distribuidores.edit', $distribuidor) }}"
                                            class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                                            title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('distribuidores.destroy', $distribuidor) }}" method="POST"
                                            class="inline"
                                            onsubmit="return confirm('¿Estás seguro de eliminar este distribuidor?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                                title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-building text-slate-300 text-5xl mb-4"></i>
                                        <p class="text-slate-500 text-lg">No hay distribuidores registrados.</p>
                                        <a href="{{ route('distribuidores.create') }}"
                                            class="mt-4 text-emerald-600 font-semibold hover:underline">Agregar el primero</a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($distribuidores->hasPages())
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">
                    {{ $distribuidores->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
