@extends('layouts.public')

@section('title', 'Cartelera Actual')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="mb-12">
            <h1 class="text-4xl font-black text-slate-900 tracking-tighter uppercase">En Cartelera</h1>
            <p class="text-slate-500 font-medium">Selecciona tu película favorita y reserva tus asientos ahora.</p>
        </div>

        @if($funciones->isEmpty())
            <div class="bg-white rounded-3xl p-20 text-center border-2 border-dashed border-slate-200">
                <i class="fas fa-film text-6xl text-slate-200 mb-6"></i>
                <h3 class="text-2xl font-bold text-slate-800">No hay funciones programadas</h3>
                <p class="text-slate-500 mt-2">Vuelve pronto para ver los nuevos estrenos.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($funciones->groupBy('pelicula_id') as $peliculaId => $funcionesPelicula)
                    @php $pelicula = $funcionesPelicula->first()->pelicula; @endphp
                    <div
                        class="bg-white rounded-3xl overflow-hidden shadow-xl shadow-slate-200/50 border border-slate-100 group hover:scale-[1.02] transition-all duration-300">
                        <div class="relative aspect-[2/3] overflow-hidden">
                            <img src="{{ asset($pelicula->img_path ?? 'assets/img/poster-default.jpg') }}"
                                alt="{{ $pelicula->nombre }}" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-transparent to-transparent opacity-80">
                            </div>
                            <div class="absolute bottom-6 left-6 right-6">
                                <span
                                    class="bg-primary text-white text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-widest">{{ $pelicula->genero ?? 'General' }}</span>
                                <h2 class="text-2xl font-black text-white mt-2 leading-tight">{{ $pelicula->nombre }}</h2>
                            </div>
                        </div>

                        <div class="p-6">
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Funciones Disponibles</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach($funcionesPelicula->sortBy('hora_inicio') as $funcion)
                                    <a href="{{ route('public.funcion', $funcion->id) }}"
                                        class="px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-700 hover:bg-primary hover:text-white hover:border-primary transition-all">
                                        {{ \Carbon\Carbon::parse($funcion->hora_inicio)->format('h:i A') }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection