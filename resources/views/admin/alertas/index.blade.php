@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">🔔 Centro de Alertas</h1>
        
        <div class="flex gap-2">
            <a href="{{ route('admin.alertas.index', ['filtro' => 'activas']) }}" 
               class="px-4 py-2 rounded {{ $filtro === 'activas' ? 'bg-blue-600 text-white' : 'bg-gray-200' }}">
                Activas
            </a>
            <a href="{{ route('admin.alertas.index', ['filtro' => 'criticas']) }}" 
               class="px-4 py-2 rounded {{ $filtro === 'criticas' ? 'bg-red-600 text-white' : 'bg-gray-200' }}">
                Críticas
            </a>
            <a href="{{ route('admin.alertas.index', ['filtro' => 'todas']) }}" 
               class="px-4 py-2 rounded {{ $filtro === 'todas' ? 'bg-gray-600 text-white' : 'bg-gray-200' }}">
                Historial (Todas)
            </a>
        </div>
    </div>

    @if($alertas->isEmpty())
        <div class="bg-green-50 border border-green-200 rounded-lg p-8 text-center">
            <p class="text-4xl mb-4">✅</p>
            <h2 class="text-2xl font-bold text-green-800">Todo en orden</h2>
            <p class="text-green-600">No tienes alertas pendientes en este momento.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($alertas as $alerta)
                @php
                    $bgClass = $alerta->resuelta ? 'bg-gray-50 opacity-75' : 'bg-white';
                    
                    if (!$alerta->resuelta) {
                        if ($alerta->tipo === 'CRITICA') $borderClass = 'border-l-4 border-red-500';
                        elseif ($alerta->tipo === 'ADVERTENCIA') $borderClass = 'border-l-4 border-yellow-500';
                        else $borderClass = 'border-l-4 border-blue-500';
                    } else {
                        $borderClass = 'border-l-4 border-gray-300';
                    }
                    
                    $iconos = [
                        'INVENTARIO' => '🍿',
                        'OCUPACION' => '🎬',
                        'CAJA' => '💰',
                        'PRECIO' => '💲',
                        'GENERAL' => 'ℹ️',
                    ];
                @endphp
            
                <div class="{{ $bgClass }} shadow rounded-lg p-4 {{ $borderClass }} flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-1">
                            <span class="text-2xl">{{ $iconos[$alerta->categoria] ?? '❓' }}</span>
                            <h3 class="font-bold text-lg {{ $alerta->resuelta ? 'text-gray-500 line-through' : '' }}">
                                {{ $alerta->titulo }}
                            </h3>
                            
                            @if(!$alerta->resuelta)
                                @if($alerta->tipo === 'CRITICA')
                                    <span class="bg-red-100 text-red-800 text-xs px-2 py-0.5 rounded font-bold">CRÍTICA</span>
                                @elseif($alerta->tipo === 'ADVERTENCIA')
                                    <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-0.5 rounded font-bold">ADVERTENCIA</span>
                                @endif
                                
                                @if(!$alerta->vista)
                                    <span class="bg-blue-100 text-blue-800 text-xs px-2 py-0.5 rounded font-bold">NUEVA</span>
                                @endif
                            @else
                                <span class="bg-green-100 text-green-800 text-xs px-2 py-0.5 rounded font-bold">RESUELTA</span>
                            @endif
                            
                            <span class="text-gray-400 text-xs ml-auto">
                                {{ $alerta->created_at->diffForHumans() }}
                            </span>
                        </div>
                        
                        <p class="text-gray-700 ml-10 mb-3">{{ $alerta->mensaje }}</p>
                        
                        @if($alerta->datos && count($alerta->datos) > 0)
                            <div class="ml-10 bg-gray-50 p-2 rounded text-xs font-mono text-gray-600 inline-block">
                                @foreach($alerta->datos as $key => $val)
                                    <span class="mr-3"><strong class="uppercase">{{ str_replace('_', ' ', $key) }}:</strong> {{ $val }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    
                    <div class="ml-4 flex flex-col items-end gap-2">
                        @if(!$alerta->resuelta)
                            <form action="{{ route('admin.alertas.resolver', $alerta->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="bg-white border text-gray-600 hover:bg-green-50 hover:text-green-700 px-3 py-1 rounded text-sm transition shadow-sm">
                                    ✓ Marcar Resuelta
                                </button>
                            </form>
                            
                            {{-- Acciones contextuales --}}
                            @if($alerta->categoria === 'INVENTARIO' && isset($alerta->datos['producto_id']))
                                <a href="{{ route('admin.products.edit', $alerta->datos['producto_id']) }}" class="text-blue-600 text-sm hover:underline">
                                    Editar Producto →
                                </a>
                            @elseif($alerta->categoria === 'OCUPACION')
                                {{-- Link ficticio a cartelera --}}
                                <a href="#" class="text-blue-600 text-sm hover:underline">Ver Cartelera →</a>
                            @endif
                        @else
                            <span class="text-xs text-green-600">Resuelta hace {{ $alerta->resuelta_at->diffForHumans() }}</span>
                        endif
                    </div>
                </div>
            @endforeach
            
            <div class="mt-6">
                {{ $alertas->appends(['filtro' => $filtro])->links() }}
            </div>
        </div>
    @endif

</div>
@endsection
