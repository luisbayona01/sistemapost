@extends('layouts.app')

@section('title', 'Catálogo de Películas')

@push('css-datatable')
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" type="text/css">
@endpush

@push('css')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .poster-thumb {
            width: 40px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
        }
    </style>
@endpush

@section('content')

    <div class="w-full px-4 md:px-6 py-4">
        <h1 class="text-3xl font-bold text-center text-gray-900 mb-6">Catálogo de Películas</h1>

        <x-breadcrumb.template>
            <x-breadcrumb.item :href="route('admin.dashboard.index')" content="Inicio" />
            <x-breadcrumb.item active='true' content="Películas" />
        </x-breadcrumb.template>

        <div class="mb-6 flex justify-between items-center">
            @can('crear-producto')
                <a href="{{route('peliculas.create')}}">
                    <button type="button"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors shadow-sm flex items-center">
                        <i class="fas fa-plus mr-2"></i> Nueva Película
                    </button>
                </a>
            @endcan
        </div>

        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="bg-slate-50 border-b border-gray-200 px-6 py-4 font-semibold text-gray-700 flex items-center">
                <i class="fas fa-film mr-2 text-blue-600"></i>
                Listado de Películas
            </div>
            <div class="px-6 py-4">
                <table id="datatablesSimple" class="w-full border-collapse">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="text-left font-semibold p-3 border-b border-gray-200">Poster</th>
                            <th class="text-left font-semibold p-3 border-b border-gray-200">Título</th>
                            <th class="text-left font-semibold p-3 border-b border-gray-200">Género</th>
                            <th class="text-left font-semibold p-3 border-b border-gray-200">Duración</th>
                            <th class="text-left font-semibold p-3 border-b border-gray-200">Clasificación</th>
                            <th class="text-left font-semibold p-3 border-b border-gray-200">Estado</th>
                            <th class="text-right font-semibold p-3 border-b border-gray-200">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($peliculas as $pelicula)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="p-3">
                                                @if($pelicula->afiche)
                                                    <img src="{{ Storage::url($pelicula->afiche) }}" alt="{{ $pelicula->titulo }}"
                                                        class="poster-thumb shadow-sm">
                                                @else
                                                    <div class="poster-thumb bg-gray-200 flex items-center justify-center text-gray-400">
                                                        <i class="fas fa-image"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="p-3 font-medium text-gray-900">
                                                {{ $pelicula->titulo }}
                                                @if($pelicula->distribuidor)
                                                    <div class="text-xs text-gray-500 mt-1">{{ $pelicula->distribuidor->nombre }}</div>
                                                @endif
                                            </td>
                                            <td class="p-3 text-sm text-gray-600">
                                                <span
                                                    class="px-2 py-1 bg-blue-50 text-blue-700 rounded text-xs">{{ $pelicula->genero }}</span>
                                            </td>
                                            <td class="p-3 text-sm text-gray-600">
                                                <i class="far fa-clock mr-1 text-gray-400"></i> {{ $pelicula->duracion }}
                                            </td>
                                            <td class="p-3">
                                                <span
                                                    class="px-2 py-1 rounded border text-xs font-bold 
                                                            {{ $pelicula->clasificacion == '+18' ? 'border-red-200 bg-red-50 text-red-700' :
                            ($pelicula->clasificacion == 'G' || $pelicula->clasificacion == 'ATP' ? 'border-green-200 bg-green-50 text-green-700' : 'border-gray-200 bg-gray-50 text-gray-700') }}">
                                                    {{ $pelicula->clasificacion }}
                                                </span>
                                            </td>
                                            <td class="p-3">
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $pelicula->activo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $pelicula->activo ? 'Activa' : 'Inactiva' }}
                                                </span>
                                            </td>
                                            <td class="p-3 text-right">
                                                <div class="flex justify-end gap-2">
                                                    @can('editar-producto')
                                                        <a href="{{ route('peliculas.edit', $pelicula) }}"
                                                            class="p-2 bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-100 transition-colors"
                                                            title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    @endcan

                                                    @can('eliminar-producto')
                                                        <form action="{{ route('peliculas.destroy', $pelicula) }}" method="POST"
                                                            class="inline-block"
                                                            onsubmit="return confirm('¿Está seguro de eliminar esta película?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors"
                                                                title="Eliminar">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                {{ $peliculas->links() }}
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" type="text/javascript"></script>
    <script>
        window.addEventListener('DOMContentLoaded', event => {
            const dataTable = new simpleDatatables.DataTable("#datatablesSimple", {
                labels: {
                    placeholder: "Buscar...",
                    perPage: "elementos por página",
                    noRows: "No hay entradas encontradas",
                    info: "Mostrando {start} a {end} de {rows} entradas",
                }
            });
        });
    </script>
@endpush
