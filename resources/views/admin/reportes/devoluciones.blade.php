@extends('layouts.app')

@section('title', 'Reporte de Devoluciones')

@section('content')
    <div class="w-full px-4 md:px-6 py-8">
        <div class="max-w-4xl mx-auto text-center">
            <div
                class="mb-8 inline-flex items-center justify-center w-24 h-24 rounded-full bg-amber-100 text-amber-600 shadow-inner">
                <i class="fas fa-undo-alt text-4xl"></i>
            </div>

            <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight mb-4">Módulo de Devoluciones</h1>
            <p class="text-xl text-slate-500 mb-10">Estamos perfeccionando el sistema de auditoría reversa para garantizar
                la integridad total de tus inventarios.</p>

            <div class="bg-white p-8 rounded-3xl shadow-xl border border-slate-100 text-left">
                <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-info-circle text-blue-500"></i>
                    Próximamente podrás:
                </h3>
                <ul class="space-y-4">
                    <li class="flex items-start gap-4">
                        <div
                            class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="fas fa-check text-xs"></i>
                        </div>
                        <p class="text-slate-600">Realizar devoluciones parciales o totales con reintegro automático a
                            inventario.</p>
                    </li>
                    <li class="flex items-start gap-4">
                        <div
                            class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="fas fa-check text-xs"></i>
                        </div>
                        <p class="text-slate-600">Registrar mermas y bajas autorizadas con PIN de supervisor.</p>
                    </li>
                    <li class="flex items-start gap-4">
                        <div
                            class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="fas fa-check text-xs"></i>
                        </div>
                        <p class="text-slate-600">Ver historial detallado de anulaciones por usuario y motivo.</p>
                    </li>
                </ul>

                <div class="mt-8 pt-6 border-t border-slate-50 flex justify-center">
                    <a href="{{ route('admin.dashboard.index') }}"
                        class="px-6 py-3 bg-slate-900 text-white font-bold rounded-xl hover:bg-slate-800 transition-all shadow-lg active:scale-95">
                        Volver al Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
