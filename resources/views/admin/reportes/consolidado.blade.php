@extends('layouts.app')

@section('title', 'Reporte Consolidado')

@section('content')
    <div class="w-full px-4 md:px-6 py-4">
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">📊 Reporte Consolidado de Ventas</h1>
                <p class="text-gray-500 mt-1">Visión unificada de boletería y confitería.</p>
            </div>

            <!-- Filtros de fecha -->
            <form method="GET" class="flex flex-wrap gap-3 bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                <div class="flex flex-col">
                    <label class="text-[10px] font-bold text-gray-400 uppercase mb-1">Desde</label>
                    <input type="date" name="fecha_inicio" value="{{ $fechaInicio }}"
                        class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div class="flex flex-col">
                    <label class="text-[10px] font-bold text-gray-400 uppercase mb-1">Hasta</label>
                    <input type="date" name="fecha_fin" value="{{ $fechaFin }}"
                        class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div class="flex items-end">
                    <button type="submit"
                        class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700 transition-all">
                        Filtrar
                    </button>
                </div>
            </form>
        </div>

        <!-- Tabla Consolidada Principal -->
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden mb-10">
            <table class="w-full text-left">
                <thead class="bg-gray-900 text-white">
                    <tr class="text-[11px] uppercase tracking-widest font-bold">
                        <th class="px-8 py-5">Canal de Venta</th>
                        <th class="px-8 py-5 text-right">Transacciones</th>
                        <th class="px-8 py-5 text-right">Ingreso Bruto</th>
                        <th class="px-8 py-5 text-right">Impuestos</th>
                        <th class="px-8 py-5 text-right">Ingreso Neto</th>
                        <th class="px-8 py-5 text-right">Participación</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <!-- Boletería -->
                    <tr class="hover:bg-blue-50/50 transition-colors">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center text-blue-600">
                                    <i class="fas fa-ticket-alt"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900">Boletería</p>
                                    <p class="text-[10px] text-gray-400 font-bold uppercase">Ventas en Ventanilla</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6 text-right font-medium text-gray-600">
                            {{ number_format($consolidado['boleteria']?->total_transacciones ?? 0) }}
                        </td>
                        <td class="px-8 py-6 text-right font-black text-gray-900">
                            ${{ number_format($consolidado['boleteria']?->ingreso_total ?? 0, 0) }}</td>
                        <td class="px-8 py-6 text-right text-gray-400">
                            ${{ number_format($consolidado['boleteria']?->total_impuestos ?? 0, 0) }}</td>
                        <td class="px-8 py-6 text-right font-bold text-gray-700">
                            ${{ number_format($consolidado['boleteria']?->ingreso_neto ?? 0, 0) }}</td>
                        <td class="px-8 py-6 text-right">
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black bg-blue-100 text-blue-800">
                                {{ number_format($consolidado['participacion']['boleteria_porcentaje'], 1) }}%
                            </span>
                        </td>
                    </tr>

                    <!-- Confitería -->
                    <tr class="hover:bg-amber-50/50 transition-colors">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600">
                                    <i class="fas fa-cookie-bite"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900">Confitería</p>
                                    <p class="text-[10px] text-gray-400 font-bold uppercase">Snacks y Bebidas</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6 text-right font-medium text-gray-600">
                            {{ number_format($consolidado['confiteria']?->total_transacciones ?? 0) }}
                        </td>
                        <td class="px-8 py-6 text-right font-black text-gray-900">
                            ${{ number_format($consolidado['confiteria']?->ingreso_total ?? 0, 0) }}</td>
                        <td class="px-8 py-6 text-right text-gray-400">
                            ${{ number_format($consolidado['confiteria']?->total_impuestos ?? 0, 0) }}</td>
                        <td class="px-8 py-6 text-right font-bold text-gray-700">
                            ${{ number_format($consolidado['confiteria']?->ingreso_neto ?? 0, 0) }}</td>
                        <td class="px-8 py-6 text-right">
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black bg-amber-100 text-amber-800">
                                {{ number_format($consolidado['participacion']['confiteria_porcentaje'], 1) }}%
                            </span>
                        </td>
                    </tr>



                    <!-- TOTAL CONSOLIDADO -->
                    <tr class="bg-slate-900 text-white">
                        <td class="px-8 py-8">
                            <p class="text-xl font-black italic">TOTAL CONSOLIDADO</p>
                            <p class="text-[10px] text-slate-400 font-bold uppercase">Suma de todos los canales</p>
                        </td>
                        <td class="px-8 py-8 text-right text-xl font-bold">
                            {{ number_format($consolidado['total']->total_transacciones) }}
                        </td>
                        <td class="px-8 py-8 text-right text-3xl font-black text-emerald-400">
                            ${{ number_format($consolidado['total']->ingreso_total, 0) }}</td>
                        <td class="px-8 py-8 text-right text-slate-400 font-bold">
                            ${{ number_format($consolidado['total']->total_impuestos, 0) }}</td>
                        <td class="px-8 py-8 text-right text-2xl font-bold">
                            ${{ number_format($consolidado['total']->ingreso_neto, 0) }}</td>
                        <td class="px-8 py-8 text-right text-xl font-black">100%</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Tarjetas de Acceso a Reportes Individuales -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <a href="{{ route('admin.reportes.cinema') }}"
                class="group relative overflow-hidden bg-blue-600 p-8 rounded-[2rem] shadow-2xl transition-all hover:scale-[1.02]">
                <div class="absolute top-0 right-0 p-10 opacity-10 group-hover:scale-150 transition-transform">
                    <i class="fas fa-ticket-alt text-9xl text-white"></i>
                </div>
                <div class="relative z-10">
                    <h3 class="text-2xl font-black text-white mb-2">🎬 Reporte Detallado Boletería</h3>
                    <p class="text-blue-100 text-sm font-medium">Análisis de salas, ocupación y funciones más vendedoras.
                    </p>
                    <div class="mt-6 flex items-center gap-2 text-white font-bold text-xs uppercase tracking-widest">
                        <span>Ver Detalles</span>
                        <i class="fas fa-arrow-right group-hover:translate-x-2 transition-transform"></i>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.reportes.confiteria') }}"
                class="group relative overflow-hidden bg-emerald-600 p-8 rounded-[2rem] shadow-2xl transition-all hover:scale-[1.02]">
                <div class="absolute top-0 right-0 p-10 opacity-10 group-hover:scale-150 transition-transform">
                    <i class="fas fa-cookie-bite text-9xl text-white"></i>
                </div>
                <div class="relative z-10">
                    <h3 class="text-2xl font-black text-white mb-2">🍿 Reporte Detallado Confitería</h3>
                    <p class="text-emerald-100 text-sm font-medium">Ranking de productos, ticket promedio y rentabilidad
                        neta.</p>
                    <div class="mt-6 flex items-center gap-2 text-white font-bold text-xs uppercase tracking-widest">
                        <span>Ver Detalles</span>
                        <i class="fas fa-arrow-right group-hover:translate-x-2 transition-transform"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>
@endsection
