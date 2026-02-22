<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cartelera - Sistema POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 font-sans">
    <div class="container mx-auto px-4 py-12">
        <header class="text-center mb-16">
            <h1 class="text-5xl font-black text-slate-900 uppercase tracking-tighter mb-4">🎬 Nuestra Cartelera</h1>
            <p class="text-lg text-slate-500 font-bold uppercase tracking-widest">Disfruta de las mejores funciones</p>
        </header>

        @forelse($funciones as $pelicula => $funcionesPelicula)
            <section class="mb-12">
                <div class="bg-white rounded-[3rem] shadow-xl overflow-hidden border border-slate-100 p-8 md:p-12">
                    <div class="flex flex-col md:flex-row gap-8 items-start">
                        <div class="w-full md:w-64 h-96 bg-slate-200 rounded-3xl overflow-hidden shadow-2xl flex-shrink-0">
                            @if($funcionesPelicula->first()->pelicula && $funcionesPelicula->first()->pelicula->afiche)
                                <img src="{{ asset('storage/' . $funcionesPelicula->first()->pelicula->afiche) }}"
                                    alt="{{ $pelicula }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-400">
                                    <i class="fas fa-film text-6xl"></i>
                                </div>
                            @endif
                        </div>

                        <div class="flex-1">
                            <h2 class="text-4xl font-black text-slate-900 uppercase tracking-tighter mb-2">{{ $pelicula }}
                            </h2>
                            <p
                                class="text-emerald-600 font-black uppercase tracking-widest text-sm mb-8 inline-block bg-emerald-50 px-4 py-1 rounded-full">
                                En Cartelera</p>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($funcionesPelicula as $funcion)
                                    <div
                                        class="bg-slate-50 border-2 border-slate-100 rounded-3xl p-6 text-center hover:border-emerald-500 transition-all">
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                                            {{ $funcion->fecha_hora->translatedFormat('d \d\e F') }}</p>
                                        <p class="text-3xl font-black text-slate-900 mb-1">
                                            {{ $funcion->fecha_hora->format('H:i') }}</p>
                                        <p class="text-xs font-bold text-slate-500 uppercase">{{ $funcion->sala->nombre }}</p>
                                        <div class="mt-4">
                                            <span
                                                class="bg-slate-900 text-white px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest">
                                                ${{ number_format($funcion->precio_base ?? 10000, 0) }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @empty
            <div class="text-center py-20 opacity-30">
                <p class="text-2xl font-black uppercase tracking-widest">No hay funciones disponibles por ahora.</p>
            </div>
        @endforelse
    </div>
</body>

</html>
