@extends('admin.inventario-avanzado.layout')

@section('inventory_content')
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Centro de Auditoría</h1>
                <p class="text-slate-500 mt-1">Detección de mermas, control de cortesías y auditorías ciegas de stock.</p>
            </div>
            <form action="{{ route('inventario-avanzado.auditorias.store') }}" method="POST">
                @csrf
                <button type="submit"
                    class="bg-indigo-600 text-white px-6 py-3 rounded-2xl font-bold hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-200 flex items-center gap-2">
                    <i class="fas fa-search"></i> Iniciar Auditoría Ciega
                </button>
            </form>
        </div>

        <!-- Historial de Auditorias -->
        <div class="bg-white rounded-3xl border border-slate-100 overflow-hidden shadow-sm">
            <table class="w-full text-left">
                <thead class="bg-slate-50">
                    <tr class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                        <th class="px-8 py-4">Fecha / ID</th>
                        <th class="px-8 py-4">Auditor</th>
                        <th class="px-8 py-4">Estado</th>
                        <th class="px-8 py-4 text-right">Fuga de Dinero Est.</th>
                        <th class="px-8 py-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($auditorias as $auditoria)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-8 py-4">
                                <p class="font-bold text-slate-700">#{{ $auditoria->id }}</p>
                                <p class="text-[10px] text-slate-400 font-medium">
                                    {{ $auditoria->fecha_auditoria->format('d/m/Y h:i A') }}
                                </p>
                            </td>
                            <td class="px-8 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 text-[10px] font-bold">
                                        {{ substr($auditoria->user->name, 0, 2) }}
                                    </div>
                                    <span class="text-sm font-medium text-slate-600">{{ $auditoria->user->name }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-4">
                                @if($auditoria->estado === 'abierta')
                                    <span
                                        class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700 border border-amber-200">ABIERTA
                                        (EN PROCESO)</span>
                                @else
                                    <span
                                        class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 border border-emerald-200">FINALIZADA</span>
                                @endif
                            </td>
                            <td class="px-8 py-4 text-right">
                                @if($auditoria->estado === 'finalizada')
                                    <span
                                        class="text-sm font-black text-red-600">-${{ number_format($auditoria->total_diferencia_valor, 0) }}</span>
                                @else
                                    <span class="text-sm font-bold text-slate-300">Pendiente</span>
                                @endif
                            </td>
                            <td class="px-8 py-4 text-right">
                                @if($auditoria->estado === 'abierta')
                                    <button
                                        onclick="document.getElementById('modal-conteo-{{ $auditoria->id }}').classList.remove('hidden')"
                                        class="bg-indigo-50 text-indigo-600 px-4 py-2 rounded-xl text-xs font-bold hover:bg-indigo-100 transition-all">Ingresar
                                        Conteos</button>
                                @else
                                    <button class="text-slate-400 hover:text-slate-600 px-3 py-2"><i
                                            class="fas fa-eye"></i></button>
                                @endif
                            </td>
                        </tr>

                        <!-- Modal para Conteos (Auditoría Ciega) -->
                        @if($auditoria->estado === 'abierta')
                            <div id="modal-conteo-{{ $auditoria->id }}"
                                class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
                                <div
                                    class="bg-white rounded-3xl w-full max-w-2xl p-8 shadow-2xl max-h-[80vh] overflow-hidden flex flex-col">
                                    <div class="flex justify-between items-center mb-6">
                                        <div>
                                            <h2 class="text-xl font-bold text-slate-900">Auditoría Ciega #{{ $auditoria->id }}</h2>
                                            <p class="text-xs text-slate-400">Ingrese las existencias físicas vistas en estante.</p>
                                        </div>
                                        <button
                                            onclick="document.getElementById('modal-conteo-{{ $auditoria->id }}').classList.add('hidden')"
                                            class="text-slate-400 hover:text-slate-600"><i class="fas fa-times"></i></button>
                                    </div>
                                    <form action="{{ route('inventario-avanzado.auditorias.finalize', $auditoria) }}" method="POST"
                                        class="flex-1 overflow-y-auto space-y-4 pr-2">
                                        @csrf
                                        @foreach($auditoria->detalles as $detalle)
                                            <div
                                                class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-100">
                                                <span
                                                    class="font-bold text-slate-700 uppercase p-2 border border-black rounded-lg">{{ $detalle->insumo->nombre }}</span>
                                                <div class="w-32">
                                                    <input type="number" step="0.001" name="stock_fisico[{{ $detalle->insumo_id }}]"
                                                        required placeholder="0.00"
                                                        class="w-full bg-white border border-slate-200 rounded-xl p-3 text-center font-black">
                                                </div>
                                            </div>
                                        @endforeach
                                        <div class="pt-6">
                                            <button type="submit"
                                                class="w-full bg-slate-900 text-white py-4 rounded-xl font-bold shadow-xl shadow-slate-200">Finalizar
                                                Audioría y Cerrar</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Sección de Salidas Especiales -->
        <div class="mt-12 grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm">
                <h3 class="text-lg font-black text-slate-900 mb-6">Salidas Especiales Rápidas</h3>
                <div class="grid grid-cols-2 gap-4">
                    <a href="{{ route('inventario-avanzado.baja.create', ['tipo' => 'BAJA']) }}"
                        class="bg-red-50 text-red-600 p-6 rounded-2xl flex flex-col items-center gap-3 hover:bg-red-100 transition-all group">
                        <i class="fas fa-trash-alt text-2xl group-hover:scale-110 transition-transform"></i>
                        <span class="text-xs font-bold uppercase tracking-widest">Registrar Baja</span>
                    </a>
                    <a href="{{ route('inventario-avanzado.baja.create', ['tipo' => 'CORTESIA']) }}"
                        class="bg-indigo-50 text-indigo-600 p-6 rounded-2xl flex flex-col items-center gap-3 hover:bg-indigo-100 transition-all group">
                        <i class="fas fa-gift text-2xl group-hover:scale-110 transition-transform"></i>
                        <span class="text-xs font-bold uppercase tracking-widest">Registrar Cortesía</span>
                    </a>
                </div>
                <p class="text-xs text-slate-400 mt-6 italic text-center">Estas salidas restan stock y se categorizan como
                    gasto de marketing o merma operativa.</p>
            </div>

            <div class="bg-emerald-50 p-8 rounded-3xl border border-emerald-100">
                <h3 class="text-lg font-black text-emerald-900 mb-2">Resumen de Merma</h3>
                <p class="text-xs text-emerald-700/70 mb-6 font-medium">Acumulado del mes actual por conceptos de auditoría
                    y bajas.</p>
                <div class="flex items-end gap-2">
                    <span class="text-5xl font-black text-emerald-900">$0.00</span>
                    <span class="text-emerald-700/50 mb-2 font-bold uppercase tracking-widest text-[10px]">COP</span>
                </div>
                <div class="mt-8 flex gap-4">
                    <div class="flex-1 bg-white/50 p-4 rounded-xl">
                        <p class="text-[10px] text-emerald-800/50 uppercase font-bold mb-1">Cortesías</p>
                        <p class="text-lg font-black text-emerald-900">$0</p>
                    </div>
                    <div class="flex-1 bg-white/50 p-4 rounded-xl">
                        <p class="text-[10px] text-emerald-800/50 uppercase font-bold mb-1">Mermas/Bajas</p>
                        <p class="text-lg font-black text-emerald-900">$0</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
