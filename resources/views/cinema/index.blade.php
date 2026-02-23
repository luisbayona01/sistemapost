@extends('layouts.app')

@section('title', 'Cartelera Cinema')

@push('css')
    <style>
        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .glass-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .movie-poster {
            height: 200px;
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            border-radius: 12px 12px 0 0;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid px-6 py-8">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Cine POS: Cartelera</h1>
                <p class="text-slate-500 mt-1">Selecciona una función para proceder con la venta de entradas.</p>
            </div>
            <div class="flex space-x-3">
                <span
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600">
                    Punto de Venta Activo
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            @foreach($funciones as $funcion)
                <div class="glass-card rounded-2xl overflow-hidden flex flex-col">
                    <div class="movie-poster">
                        <i class="fas fa-film fa-3x opacity-20 mr-3"></i>
                        <span class="text-xl px-4 text-center">{{ optional($funcion->pelicula)->titulo ?? 'SIN TÍTULO' }}</span>
                    </div>
                    <div class="p-6 flex-1 flex flex-col">
                        <div class="flex items-center justify-between mb-4">
                            <span class="px-3 py-1 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-full uppercase">
                                {{ $funcion->sala->nombre }}
                            </span>
                            <span class="text-slate-400 text-xs">
                                <i class="far fa-clock mr-1"></i> {{ $funcion->fecha_hora->format('H:i A') }}
                            </span>
                        </div>

                        <h3 class="text-lg font-bold text-slate-800 mb-2">{{ $funcion->fecha_hora->format('d M, Y') }}</h3>
                        <p class="text-slate-500 text-sm mb-6 flex-1">
                            Gestión de taquilla en tiempo real. Sistema coordinado con facturación.
                        </p>

                        <a href="{{ route('cinema.seat-map', $funcion->id) }}"
                            class="w-full bg-slate-900 hover:bg-slate-800 text-white text-center py-3 rounded-xl font-bold transition flex items-center justify-center">
                            <i class="fas fa-couch mr-2"></i> VER ASIENTOS
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        @if($funciones->isEmpty())
            <div class="text-center py-20 bg-white rounded-3xl border-2 border-dashed border-slate-200">
                <i class="fas fa-calendar-times fa-4x text-slate-200 mb-4"></i>
                <h2 class="text-xl font-bold text-slate-400">No hay funciones programadas</h2>
                <p class="text-slate-400">Empieza creando una función en el panel administrativo.</p>
            </div>
        @endif
    </div>
@endsection
