@extends('layouts.app')

@section('title', 'Gestión de Funciones')

@section('content')
    <div class="px-6 py-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Programación de Cartelera</h1>
                <p class="text-slate-600 mt-1">Gestione los horarios y funciones de su cine de forma inteligente.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                @can('crear-producto')
                    <a href="{{ route('productos.create') }}"
                        class="inline-flex items-center px-5 py-2.5 bg-slate-800 text-white font-bold rounded-xl hover:bg-slate-700 transition-all shadow-sm">
                        <i class="fas fa-film mr-2"></i> Nueva Película
                    </a>
                @endcan

                <a href="{{ route('funciones.bulkCreate') }}"
                    class="inline-flex items-center px-5 py-2.5 bg-indigo-100 text-indigo-700 font-bold rounded-xl hover:bg-indigo-200 transition-all shadow-sm">
                    <i class="fas fa-magic mr-2"></i> Programador Masivo
                </a>
                <a href="{{ route('funciones.create') }}"
                    class="inline-flex items-center px-5 py-2.5 bg-emerald-600 text-white font-bold rounded-xl hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-200">
                    <i class="fas fa-plus mr-2"></i> Nueva Función
                </a>
            </div>
        </div>

        @if(session('warning'))
            <div class="bg-amber-50 border-l-4 border-amber-400 p-4 mb-6 rounded-r-xl">
                <div class="flex">
                    <i class="fas fa-exclamation-triangle text-amber-400 mr-3 mt-1"></i>
                    <p class="text-sm text-amber-700">{{ session('warning') }}</p>
                </div>
            </div>
        @endif

        <x-breadcrumb.template class="mb-6">
            <x-breadcrumb.item :href="route('admin.dashboard.index')" content="Administración" />
            <x-breadcrumb.item active='true' content="Funciones" />
        </x-breadcrumb.template>

        <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100">
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Película</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Sala / Horario
                            </th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Ocupación</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Precio</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($funciones as $funcion)
                            <tr
                                class="hover:bg-slate-50/80 transition-colors group {{ !$funcion->activo ? 'opacity-60 bg-slate-50/30' : '' }}">
                                <td class="px-6 py-4 text-sm font-bold text-slate-400">
                                    #{{ $funcion->id }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div
                                            class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400 mr-3">
                                            <i class="fas fa-film"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-900">
                                                {{ $funcion->pelicula->titulo ?? 'PELÍCULA NO ENCONTRADA' }}</p>
                                            @if($funcion->pelicula)
                                                <p class="text-[10px] text-slate-500 uppercase tracking-wider">
                                                    {{ $funcion->pelicula->clasificacion }} • {{ $funcion->pelicula->duracion }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-slate-700 flex items-center">
                                            <i class="fas fa-door-open mr-2 text-slate-400"></i> {{ $funcion->sala->nombre }}
                                        </span>
                                        <span class="text-xs font-medium text-indigo-600 mt-0.5">
                                            {{ $funcion->fecha_hora->format('d/m/Y') }} @
                                            {{ $funcion->fecha_hora->format('h:i A') }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $total = $funcion->asientos->count();
                                        $vendidos = $funcion->asientos->where('estado', 'vendido')->count();
                                        $porcentaje = $total > 0 ? round(($vendidos / $total) * 100) : 0;
                                    @endphp
                                    <div class="flex flex-col gap-1.5 min-w-[120px]">
                                        <div
                                            class="flex items-center justify-between text-[10px] font-bold uppercase tracking-wider">
                                            <span class="text-slate-400">{{ $vendidos }} / {{ $total }} sillas</span>
                                            <span
                                                class="{{ $porcentaje > 80 ? 'text-red-500' : 'text-emerald-500' }}">{{ $porcentaje }}%</span>
                                        </div>
                                        <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                            <div class="{{ $porcentaje > 80 ? 'bg-red-500' : 'bg-emerald-500' }} h-full transition-all duration-500"
                                                style="width: {{ $porcentaje }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm font-bold text-slate-900">
                                    ${{ number_format($funcion->precio, 0) }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2 text-sm">
                                        <form action="{{ route('funciones.toggleActivo', $funcion) }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                class="p-2 transition-all {{ $funcion->activo ? 'text-emerald-600 hover:bg-emerald-50' : 'text-slate-400 hover:bg-slate-100' }} rounded-xl"
                                                title="{{ $funcion->activo ? 'Visible en Web' : 'Oculto en Web' }}">
                                                <i class="fas {{ $funcion->activo ? 'fa-eye' : 'fa-eye-slash' }} text-lg"></i>
                                            </button>
                                        </form>
                                        <a href="{{ route('funciones.edit', $funcion) }}"
                                            class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        @if(session('confirm_delete_id') == $funcion->id)
                                            <form action="{{ route('funciones.destroy', $funcion) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="confirm_delete" value="1">
                                                <button type="submit"
                                                    class="px-3 py-1 bg-red-600 text-white text-[10px] font-bold rounded-lg hover:bg-red-700 animate-pulse">
                                                    CONFIRMAR ELIMINAR
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('funciones.destroy', $funcion) }}" method="POST"
                                                onsubmit="return confirm('¿Está seguro de eliminar esta función?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-calendar-times text-slate-200 text-6xl mb-4"></i>
                                        <p class="text-slate-400 font-bold text-lg">No hay funciones programadas</p>
                                        <p class="text-slate-300 text-sm">Use el programador masivo para crear la cartelera
                                            rápidamente.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">
                {{ $funciones->links() }}
            </div>
        </div>
    </div>
@endsection
