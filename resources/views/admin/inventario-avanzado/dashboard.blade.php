@extends('admin.inventario-avanzado.layout')

@section('valor_activos', '$' . number_format($valorActivos, 0))

@section('inventory_content')
    <div class="max-w-7xl mx-auto">
        <div class="mb-8 flex justify-between items-end">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Inteligencia de Inventario</h1>
                <p class="text-slate-500 mt-2">Visión global de activos, riesgos de stock y rentabilidad neta.</p>
            </div>
            <!-- Offline Indicator (Visual Only for Dashboard, Logic in POS) -->
            <div id="offline-indicator"
                class="hidden align-middle bg-rose-500 text-white text-xs font-bold px-3 py-1 rounded-full animate-pulse">
                <i class="fas fa-wifi-slash mr-1"></i> MODO OFFLINE
            </div>
        </div>

        @include('admin.inventario-avanzado.partials.blind_audit')

        <!-- Píldoras de Inteligencia Proactiva -->
        @include('admin.inventario-avanzado.partials.insights')

        <!-- KPIs Principales -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Valoración Total</p>
                    <h3 class="text-4xl font-black text-slate-900">${{ number_format($valorActivos, 0) }}</h3>
                    <span
                        class="inline-flex items-center mt-3 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                        <i class="fas fa-arrow-up mr-1 text-[10px]"></i> Activo Líquido
                    </span>
                </div>
                <div class="w-16 h-16 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600">
                    <i class="fas fa-coins text-3xl"></i>
                </div>
            </div>

            <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Puntos Críticos</p>
                    <h3 class="text-4xl font-black text-slate-900">{{ $stockBajoCount }}</h3>
                    <span
                        class="inline-flex items-center mt-3 px-2.5 py-0.5 rounded-full text-xs font-medium {{ $stockBajoCount > 0 ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}">
                        {{ $stockBajoCount > 0 ? 'Reponer Stock' : 'Optimizado' }}
                    </span>
                </div>
                <div class="w-16 h-16 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-600">
                    <i class="fas fa-exclamation-triangle text-3xl"></i>
                </div>
            </div>

            <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Alertas Vencimiento</p>
                    <h3 class="text-4xl font-black text-slate-900">{{ count($alertasVencimiento) }}</h3>
                    <span
                        class="inline-flex items-center mt-3 px-2.5 py-0.5 rounded-full text-xs font-medium {{ count($alertasVencimiento) > 0 ? 'bg-red-100 text-red-800' : 'bg-emerald-100 text-emerald-800' }}">
                        {{ count($alertasVencimiento) > 0 ? 'Lotes Próximos' : 'Al Día' }}
                    </span>
                </div>
                <div class="w-16 h-16 bg-red-50 rounded-2xl flex items-center justify-center text-red-600">
                    <i class="fas fa-calendar-times text-3xl"></i>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-10">
            <!-- Top Ventas: Crecimiento vs Estancamiento -->
            <div class="bg-indigo-900 p-8 rounded-[3rem] shadow-2xl shadow-indigo-200 text-white">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-xl font-bold">Resumen de Tendencias</h2>
                        <p class="text-indigo-300 text-[10px] uppercase font-bold tracking-widest mt-1">Comparativa Semanal
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-indigo-800 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-fire text-orange-400"></i>
                    </div>
                </div>

                <div class="space-y-4">
                    @forelse($trendingProducts->take(4) as $trending)
                        <div
                            class="bg-indigo-800/50 p-4 rounded-2xl border border-indigo-700/50 flex justify-between items-center">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-xl flex items-center justify-center font-bold {{ $trending['crecimiento'] >= 0 ? 'bg-emerald-500/20 text-emerald-400' : 'bg-rose-500/20 text-rose-400' }}">
                                    @if($trending['crecimiento'] >= 10) <i class="fas fa-arrow-trend-up"></i>
                                    @elseif($trending['crecimiento'] <= -10) <i class="fas fa-arrow-trend-down"></i>
                                    @else <i class="fas fa-equals"></i>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-bold text-sm">{{ $trending['nombre'] }}</p>
                                    <p class="text-[10px] text-indigo-300">Total: {{ $trending['ventas_actual'] }} unidades</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span
                                    class="font-black text-lg {{ $trending['crecimiento'] >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                                    {{ $trending['crecimiento'] >= 0 ? '+' : '' }}{{ number_format($trending['crecimiento'], 1) }}%
                                </span>
                                <p class="text-[9px] uppercase font-bold text-indigo-400/70">Crecimiento</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-indigo-400 py-8 italic">Sin datos de ventas suficientes.</p>
                    @endforelse
                </div>
            </div>

            <!-- Resumen Financiero -->
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 flex flex-col">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="font-bold text-slate-800 text-lg">Balance Operativo</h2>
                        <p class="text-xs text-slate-400 font-medium uppercase tracking-wider">Flujo de Caja - Mes Actual
                        </p>
                    </div>
                    <span class="bg-slate-100 text-slate-600 px-3 py-1 rounded-full text-xs font-bold">
                        {{$resumenFinanciero['periodo_inicio']}} - {{$resumenFinanciero['periodo_fin']}}
                    </span>
                </div>

                <div class="space-y-4 flex-1">
                    <div class="flex justify-between items-center p-4 bg-slate-50 rounded-2xl">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                <i class="fas fa-file-invoice-dollar"></i>
                            </div>
                            <span class="text-slate-600 font-medium text-sm">Facturas (Inversión)</span>
                        </div>
                        <span
                            class="font-bold text-slate-900">${{number_format($resumenFinanciero['total_compras_facturas'], 0)}}</span>
                    </div>

                    <div class="flex justify-between items-center p-4 bg-slate-50 rounded-2xl">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">
                                <i class="fas fa-cash-register"></i>
                            </div>
                            <span class="text-slate-600 font-medium text-sm">Ventas (Captación)</span>
                        </div>
                        <span
                            class="font-bold text-slate-900">${{number_format($resumenFinanciero['total_ventas_ingresos'], 0)}}</span>
                    </div>

                    <div class="border-t border-slate-200 mt-4 pt-4 flex justify-between items-center">
                        <span class="text-sm font-bold text-slate-400 uppercase tracking-widest">Utilidad Estimada</span>
                        <div class="text-right">
                            <span
                                class="block text-3xl font-black {{$resumenFinanciero['diferencia_neta'] >= 0 ? 'text-emerald-600' : 'text-rose-500'}}">
                                {{ $resumenFinanciero['diferencia_neta'] >= 0 ? '+' : '' }}
                                ${{number_format($resumenFinanciero['diferencia_neta'], 0)}}
                            </span>
                            <span
                                class="text-[10px] font-bold uppercase {{$resumenFinanciero['estado'] == 'GANANCIA' ? 'text-emerald-400' : 'text-rose-400'}}">
                                {{$resumenFinanciero['estado']}}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sugerencias de Compra -->
        <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 mb-10">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="font-bold text-slate-800 text-lg">Plan de Reabastecimiento Inteligente</h2>
                    <div class="flex gap-2 mt-2">
                        <a href="?range=1"
                            class="px-2 py-1 text-[10px] font-bold uppercase rounded-lg border {{isset($rangoDias) && $rangoDias == 1 ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-slate-500 border-slate-200'}}">Día</a>
                        <a href="?range=7"
                            class="px-2 py-1 text-[10px] font-bold uppercase rounded-lg border {{(!isset($rangoDias) || $rangoDias == 7) ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-slate-500 border-slate-200'}}">Semana</a>
                        <a href="?range=15"
                            class="px-2 py-1 text-[10px] font-bold uppercase rounded-lg border {{isset($rangoDias) && $rangoDias == 15 ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-slate-500 border-slate-200'}}">Quincena</a>
                    </div>
                </div>
                <button class="bg-indigo-50 text-indigo-600 px-4 py-2 rounded-xl text-xs font-bold">
                    <i class="fas fa-print mr-2"></i> Orden de Compra
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 max-h-[400px] overflow-y-auto pr-2">
                @forelse($planCompras as $sugerencia)
                    <div
                        class="flex items-center justify-between p-4 bg-slate-50/50 border border-slate-100 rounded-2xl hover:bg-white hover:shadow-xl transition-all duration-300">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-xl bg-white border border-slate-100 flex items-center justify-center text-slate-400">
                                <i class="fas fa-box-open"></i>
                            </div>
                            <div>
                                <p class="font-bold text-slate-800 text-sm">{{$sugerencia['insumo']}}</p>
                                <p class="text-[10px] text-slate-400">
                                    Stock Actual: {{number_format($sugerencia['stock_actual'], 1)}}
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="block font-black text-indigo-600 text-lg">
                                +{{number_format($sugerencia['compra_necesaria'], 1)}}
                            </span>
                            <span class="text-[9px] text-slate-400 font-bold uppercase">Requerido</span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-10">
                        <div
                            class="w-16 h-16 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-check-double text-2xl"></i>
                        </div>
                        <p class="text-sm font-bold text-slate-500">Logística perfecta. No hay compras pendientes.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="overflow-y-auto max-h-[300px] pr-2">
            @forelse($planCompras as $sugerencia)
                <div class="flex items-center justify-between p-3 hover:bg-slate-50 rounded-xl transition-colors mb-2">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full bg-amber-400"></div>
                        <div>
                            <p class="font-bold text-slate-800 text-sm">{{$sugerencia['insumo']}}</p>
                            <p class="text-[10px] text-slate-400">
                                Stock: {{number_format($sugerencia['stock_actual'], 1)}} |
                                Req: {{number_format($sugerencia['sugerido_total'], 1)}}
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="block font-black text-slate-900 text-sm">
                            +{{number_format($sugerencia['compra_necesaria'], 1)}}
                        </span>
                        <span class="text-[10px] text-emerald-600 font-bold uppercase">Comprar</span>
                    </div>
                </div>
            @empty
                <div class="text-center py-10">
                    <div
                        class="w-12 h-12 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-check"></i>
                    </div>
                    <p class="text-sm font-medium text-slate-500">Todo abastecido correctamente.</p>
                </div>
            @endforelse
        </div>
    </div>
    </div>

    <!-- Matriz de Boston -->
    <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 mb-10">
        <div class="mb-6">
            <h2 class="font-bold text-slate-800 text-lg">Matriz de Ingeniería de Menú (Boston)</h2>
            <p class="text-xs text-slate-400 font-medium mt-1">Clasificación automática basada en Volumen de Ventas vs
                Rentabilidad</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- Estrellas -->
            <div class="bg-yellow-50 rounded-2xl p-5 border border-yellow-100">
                <div class="flex items-center gap-2 mb-4">
                    <span class="text-xl">🌟</span>
                    <h3 class="font-bold text-yellow-800 uppercase text-xs tracking-widest">Estrellas</h3>
                </div>
                <div class="space-y-2">
                    @foreach($matrizBoston->where('categoria_boston', 'ESTRELLA')->take(5) as $prod)
                        <div class="bg-white p-2 rounded-lg shadow-sm text-xs font-medium text-slate-700 flex justify-between">
                            <span>{{$prod['nombre']}}</span>
                            <span class="text-emerald-600 font-bold">{{number_format($prod['margen'], 0)}}%</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Caballos -->
            <div class="bg-blue-50 rounded-2xl p-5 border border-blue-100">
                <div class="flex items-center gap-2 mb-4">
                    <span class="text-xl">🐴</span>
                    <h3 class="font-bold text-blue-800 uppercase text-xs tracking-widest">Caballos</h3>
                </div>
                <div class="space-y-2">
                    @foreach($matrizBoston->where('categoria_boston', 'CABALLO_DE_BATALLA')->take(5) as $prod)
                        <div class="bg-white p-2 rounded-lg shadow-sm text-xs font-medium text-slate-700 flex justify-between">
                            <span>{{$prod['nombre']}}</span>
                            <span class="text-blue-600 font-bold">{{number_format($prod['margen'], 0)}}%</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Puzzles -->
            <div class="bg-purple-50 rounded-2xl p-5 border border-purple-100">
                <div class="flex items-center gap-2 mb-4">
                    <span class="text-xl">❓</span>
                    <h3 class="font-bold text-purple-800 uppercase text-xs tracking-widest">Puzzles</h3>
                </div>
                <div class="space-y-2">
                    @foreach($matrizBoston->where('categoria_boston', 'PUZZLE')->take(5) as $prod)
                        <div class="bg-white p-2 rounded-lg shadow-sm text-xs font-medium text-slate-700 flex justify-between">
                            <span>{{$prod['nombre']}}</span>
                            <span class="text-purple-600 font-bold">{{number_format($prod['margen'], 0)}}%</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Perros -->
            <div class="bg-slate-50 rounded-2xl p-5 border border-slate-200">
                <div class="flex items-center gap-2 mb-4">
                    <span class="text-xl">🐕</span>
                    <h3 class="font-bold text-slate-600 uppercase text-xs tracking-widest">Perros</h3>
                </div>
                <div class="space-y-2">
                    @foreach($matrizBoston->where('categoria_boston', 'PERRO')->take(5) as $prod)
                        <div
                            class="bg-white p-2 rounded-lg shadow-sm text-xs font-medium text-slate-700 flex justify-between opacity-75">
                            <span>{{$prod['nombre']}}</span>
                            <span class="text-red-600 font-bold">{{number_format($prod['margen'], 0)}}%</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
