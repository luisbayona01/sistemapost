@extends('layouts.admin')
@section('content')
    <div class="container mx-auto px-4 py-6">

        <h1 class="text-3xl font-bold mb-6">↩️ Devoluciones</h1>

        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded">
                {{ session('error') }}
            </div>
        @endif

        <!-- Buscar venta -->
        <div class="bg-white rounded-lg shadow p-6 mb-6 border border-gray-100">
            <h2 class="text-xl font-bold mb-4">🔍 Buscar Venta para Devolver</h2>

            <form method="POST" action="{{ route('admin.devoluciones.buscar') }}" class="flex gap-3">
                @csrf
                <input type="number" name="venta_id"
                    class="border-2 rounded-lg px-4 py-3 text-xl font-bold w-48 text-center focus:border-blue-500 focus:ring-0 transition"
                    placeholder="# Venta" min="1" required>
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-bold text-lg transition shadow-lg shadow-blue-100">
                    🔍 Buscar
                </button>
            </form>

            <p class="text-sm text-gray-500 mt-2">
                ℹ️ Solo se pueden devolver ventas del día actual.
                Para casos especiales, contactar al administrador del sistema.
            </p>
        </div>

        <!-- Historial -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <div class="bg-gray-900 text-white p-6">
                <h2 class="text-xl font-bold">📋 Historial de Devoluciones</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-black">
                        <tr>
                            <th class="px-6 py-4">ID</th>
                            <th class="px-6 py-4">Venta</th>
                            <th class="px-6 py-4">Fecha</th>
                            <th class="px-6 py-4">Procesó</th>
                            <th class="px-6 py-4">Tipo</th>
                            <th class="px-6 py-4 text-right">Monto</th>
                            <th class="px-6 py-4 text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($devoluciones as $dev)
                            <tr class="hover:bg-gray-50 transition {{ $dev->es_excepcional ? 'bg-yellow-50/50' : '' }}">
                                <td class="px-6 py-4 font-bold text-gray-900">#{{ $dev->id }}</td>
                                <td class="px-6 py-4">
                                    <span class="bg-blue-50 text-blue-600 px-3 py-1 rounded-full text-xs font-bold">
                                        Venta #{{ $dev->venta_id }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $dev->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-4 text-gray-700 font-medium">{{ $dev->user->name }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $dev->tipo }}</td>
                                <td class="px-6 py-4 text-right font-black text-red-600">
                                    -${{ number_format($dev->monto_devuelto, 0) }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($dev->es_excepcional)
                                        <span
                                            class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider">
                                            ⚠️ Excepcional
                                        </span>
                                    @else
                                        <span
                                            class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider">
                                            ✅ Normal
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-20 text-center text-gray-400 font-medium italic">
                                    No hay devoluciones registradas
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($devoluciones->hasPages())
                <div class="p-6 border-t border-gray-50">{{ $devoluciones->links() }}</div>
            @endif
        </div>
    </div>
@endsection