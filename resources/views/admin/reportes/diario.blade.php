@extends('layouts.admin')

@section('content')
    <div class="px-6 py-8 bg-slate-50 min-h-screen">
        <div class="max-w-4xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-black text-slate-900">Reporte Diario (Detalle)</h1>
                <form action="{{ route('admin.ventas.diarias') }}" method="GET" class="flex items-center gap-2">
                    <input type="date" name="fecha" value="{{ $fecha }}"
                        class="border-0 bg-white shadow-sm rounded-xl px-4 py-2 text-sm font-bold text-slate-700 outline-none focus:ring-2 focus:ring-slate-900 transition-all">
                    <button type="submit"
                        class="bg-slate-900 text-white px-4 py-2 rounded-xl text-sm font-black hover:bg-slate-800 transition-all">
                        Ir
                    </button>
                </form>
            </div>
            <p class="text-sm font-bold text-slate-500 mb-4 uppercase tracking-widest">FECHA OPERATIVA:
                {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</p>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-2xl p-6 shadow-sm border-l-4 border-slate-900">
                    <p class="text-[10px] font-black text-slate-400 uppercase">Total General</p>
                    <p class="text-2xl font-black">${{ number_format($totalGeneral, 0) }}</p>
                </div>
                <div class="bg-white rounded-2xl p-6 shadow-sm border-l-4 border-blue-500">
                    <p class="text-[10px] font-black text-slate-400 uppercase">Total Cine (Exento)</p>
                    <p class="text-2xl font-black">${{ number_format($totalCine, 0) }}</p>
                </div>
                <div class="bg-white rounded-2xl p-6 shadow-sm border-l-4 border-amber-500">
                    <p class="text-[10px] font-black text-slate-400 uppercase">Confitería (Neto)</p>
                    <p class="text-2xl font-black">${{ number_format($totalConfiteria - $totalINC, 0) }}</p>
                </div>
                <div class="bg-white rounded-2xl p-6 shadow-sm border-l-4 border-emerald-500">
                    <p class="text-[10px] font-black text-slate-400 uppercase">INC Recaudado (8%)</p>
                    <p class="text-2xl font-black">${{ number_format($totalINC, 0) }}</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm mb-6">
                <h3 class="font-bold text-slate-700 mb-2 uppercase text-xs tracking-widest">Resumen por Medio de Pago:</h3>
                <ul class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach($resumenMedios as $metodo => $monto)
                        <li class="bg-slate-50 p-3 rounded-xl border border-slate-100 text-center">
                            <span class="block text-[9px] font-black text-slate-400 uppercase">{{ $metodo }}</span>
                            <span class="font-black text-slate-900">${{ number_format($monto, 0) }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="bg-white rounded-2xl overflow-hidden shadow-sm">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-100 font-bold text-slate-600">
                        <tr>
                            <th class="p-4">Hora</th>
                            <th class="p-4">Usuario</th>
                            <th class="p-4">Caja</th>
                            <th class="p-4 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ventas as $venta)
                            <tr class="border-b">
                                <td class="p-4">{{ $venta->hora }}</td>
                                <td class="p-4">{{ $venta->user->name ?? 'N/A' }}</td>
                                <td class="p-4">{{ $venta->caja->nombre ?? 'N/A' }}</td>
                                <td class="p-4 text-right font-bold">
                                    ${{ number_format($venta->total_final > 0 ? $venta->total_final : $venta->total, 0) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-8 text-center text-slate-400">No hay ventas registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection