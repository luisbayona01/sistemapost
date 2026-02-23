<div class="bg-indigo-50 border-2 border-indigo-200 rounded-3xl p-6 mb-8 relative overflow-hidden">
    <div class="absolute top-0 right-0 p-4 opacity-10">
        <i class="fas fa-eye text-9xl text-indigo-900"></i>
    </div>
    <div class="relative z-10">
        <div class="flex items-center gap-3 mb-4">
            <div class="bg-indigo-600 text-white rounded-full p-2">
                <i class="fas fa-user-secret"></i>
            </div>
            <h2 class="font-bold text-indigo-900 text-lg">Reto de Auditoría Ciega (Diario)</h2>
        </div>
        <p class="text-sm text-indigo-700 mb-6">El sistema ha seleccionado estos 5 productos al azar. Cuenta las
            existencias físicas e ingrésalas sin mirar el sistema.</p>

        <form action="{{ route('inventario-avanzado.auditorias.store') }}" method="POST"> <!-- Ruta Correcta -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                @foreach($productosAuditoria as $prod)
                    <div class="bg-white p-3 rounded-xl shadow-sm border border-indigo-100">
                        <p class="font-bold text-xs text-slate-700 mb-2 truncate" title="{{$prod->nombre}}">
                            {{$prod->nombre}}
                        </p>
                        <input type="number"
                            class="w-full border-b-2 border-slate-200 bg-slate-50 focus:bg-white focus:border-indigo-500 outline-none text-center font-bold text-indigo-900"
                            placeholder="?" name="audit[{{$prod->id}}]">
                    </div>
                @endforeach
            </div>
            <div class="mt-4 text-right">
                <button type="button"
                    onclick="Swal.fire('Auditoría Registrada', 'Los datos han sido enviados al gerente.', 'success')"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold py-2 px-6 rounded-lg transition-colors shadow-lg shadow-indigo-200">
                    Enviar Auditoría
                </button>
            </div>
        </form>
    </div>
</div>
