@extends('layouts.app')

@section('title', 'Reporte Fiscal INC')

@section('content')
    <div class="w-full px-4 md:px-6 py-4">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Reporte Fiscal INC</h1>
                <p class="text-slate-500">Impuesto Nacional al Consumo (8%) - Control Bimestral</p>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('reports.fiscal.inc.export', request()->all()) }}"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg font-bold flex items-center gap-2 transition-all">
                    <i class="fas fa-file-csv"></i>
                    Exportar CSV
                </a>
            </div>
        </div>

        <!-- Filtros -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 mb-6">
            <form action="{{ route('reports.fiscal.inc') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Año</label>
                    <select name="year" class="w-full border-slate-200 rounded-xl focus:ring-slate-900">
                        @for($i = date('Y'); $i >= 2024; $i--)
                            <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Bimestre</label>
                    <select name="bimestre" class="w-full border-slate-200 rounded-xl focus:ring-slate-900">
                        <option value="1" {{ $bimestre == 1 ? 'selected' : '' }}>Enero - Febrero</option>
                        <option value="2" {{ $bimestre == 2 ? 'selected' : '' }}>Marzo - Abril</option>
                        <option value="3" {{ $bimestre == 3 ? 'selected' : '' }}>Mayo - Junio</option>
                        <option value="4" {{ $bimestre == 4 ? 'selected' : '' }}>Julio - Agosto</option>
                        <option value="5" {{ $bimestre == 5 ? 'selected' : '' }}>Septiembre - Octubre</option>
                        <option value="6" {{ $bimestre == 6 ? 'selected' : '' }}>Noviembre - Diciembre</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit"
                        class="w-full bg-slate-900 text-white rounded-xl py-2.5 font-bold hover:bg-slate-800 transition-all">
                        Filtrar Reporte
                    </button>
                </div>
            </form>
        </div>

        <!-- Totales -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                <p class="text-slate-500 text-xs font-bold uppercase mb-1">Base Gravable (Confitería)</p>
                <h3 class="text-2xl font-black text-slate-900">${{ number_format($totales['base'], 0, ',', '.') }}</h3>
            </div>
            <div class="bg-indigo-50 p-6 rounded-2xl shadow-sm border border-indigo-100">
                <p class="text-indigo-600 text-xs font-bold uppercase mb-1">Total INC Causado (8%)</p>
                <h3 class="text-2xl font-black text-indigo-900">${{ number_format($totales['inc'], 0, ',', '.') }}</h3>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                <p class="text-slate-500 text-xs font-bold uppercase mb-1">Total Recaudado Bruto</p>
                <h3 class="text-2xl font-black text-slate-900">${{ number_format($totales['total'], 0, ',', '.') }}</h3>
            </div>
        </div>

        <!-- Tabla Detalle -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
                <h4 class="font-bold text-slate-900">Desglose Mensual</h4>
            </div>
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-[10px] font-black uppercase tracking-widest">
                        <th class="px-6 py-4">Mes</th>
                        <th class="px-6 py-4 text-right">Base Gravable</th>
                        <th class="px-6 py-4 text-right">INC Causado</th>
                        <th class="px-6 py-4 text-right">Total Bruto</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($data as $row)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 font-bold text-slate-900">
                                @php
                                    $nombresMeses = [
                                        1 => 'Enero',
                                        2 => 'Febrero',
                                        3 => 'Marzo',
                                        4 => 'Abril',
                                        5 => 'Mayo',
                                        6 => 'Junio',
                                        7 => 'Julio',
                                        8 => 'Agosto',
                                        9 => 'Septiembre',
                                        10 => 'Octubre',
                                        11 => 'Noviembre',
                                        12 => 'Diciembre'
                                    ];
                                @endphp
                                {{ $nombresMeses[$row->mes] }}
                            </td>
                            <td class="px-6 py-4 text-right text-slate-600 font-medium">
                                ${{ number_format($row->base_gravable, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-right text-indigo-600 font-bold">
                                ${{ number_format($row->inc_causado, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-right text-slate-900 font-black">
                                ${{ number_format($row->total_ventas, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach

                    @if($data->isEmpty())
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-slate-400">
                                <i class="fas fa-search mb-2 text-2xl"></i>
                                <p>No se encontraron registros para este periodo.</p>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@endsection