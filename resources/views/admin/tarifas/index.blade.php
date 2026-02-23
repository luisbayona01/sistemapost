@extends('layouts.admin')
@section('content')
    <div class="container mx-auto px-4 py-6">

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">🎟️ Tarifas de Entrada</h1>
            <a href="{{ route('admin.tarifas.crear') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-bold">
                ➕ Nueva Tarifa
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($tarifas as $tarifa)
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="h-2" style="background-color: {{ $tarifa->color }}"></div>
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-xl font-bold">{{ $tarifa->nombre }}</h3>
                            @if($tarifa->es_default)
                                <span class="bg-blue-100 text-blue-700 text-xs px-2 py-1 rounded-full">
                                    DEFAULT
                                </span>
                            @endif
                        </div>

                        <p class="text-4xl font-bold mb-4" style="color:{{ $tarifa->color }}">
                            ${{ number_format($tarifa->precio, 0) }}
                        </p>

                        @php
                            $nombres = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
                            $dias = $tarifa->dias_semana ?? [];
                        @endphp

                        <div class="text-sm text-gray-600 mb-4 space-y-1">
                            @if(!empty($dias))
                                <p>📅 {{ implode(', ', array_map(fn($d) => $nombres[$d] ?? '', $dias)) }}</p>
                            @endif
                            @if($tarifa->aplica_festivos)
                                <p>🎉 Incluye festivos</p>
                            @endif
                            @if(empty($dias) && !$tarifa->aplica_festivos)
                                <p class="text-gray-400">Sin días automáticos</p>
                            @endif
                        </div>

                        <div class="flex gap-2">
                            <a href="{{ route('admin.tarifas.editar', $tarifa->id) }}"
                                class="flex-1 bg-gray-100 hover:bg-gray-200 text-center py-2 rounded font-semibold text-sm">
                                ✏️ Editar
                            </a>
                            <span class="flex-1 text-center py-2 rounded text-sm font-semibold
                                {{ $tarifa->activa ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $tarifa->activa ? '✅ Activa' : '❌ Inactiva' }}
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection