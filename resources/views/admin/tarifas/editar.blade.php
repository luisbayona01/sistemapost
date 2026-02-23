@extends('layouts.admin')
@section('content')
<div class="container mx-auto px-4 py-6 max-w-2xl">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-3xl font-bold mb-6 border-b pb-4">✏️ Editar Tarifa</h1>

        <form method="POST" action="{{ route('admin.tarifas.actualizar', $tarifa->id) }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block font-bold mb-2">Nombre:</label>
                <input type="text" name="nombre"
                       value="{{ $tarifa->nombre }}"
                       class="w-full border-2 rounded-lg px-4 py-3 text-lg" required>
            </div>

            <div class="mb-4">
                <label class="block font-bold mb-2">Precio:</label>
                <input type="number" name="precio" step="1000" min="0"
                       value="{{ $tarifa->precio }}"
                       class="w-full border-2 rounded-lg px-4 py-3 text-2xl font-bold text-center" required>
            </div>

            <div class="mb-4">
                <label class="block font-bold mb-3">Días que aplica:</label>
                <div class="grid grid-cols-7 gap-2">
                    @foreach(['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'] as $i => $d)
                    @php $marcado = in_array($i, $tarifa->dias_semana ?? []); @endphp
                    <div>
                        <input type="checkbox" name="dias_semana[]"
                               value="{{ $i }}" id="d{{ $i }}" class="hidden"
                               {{ $marcado ? 'checked' : '' }}>
                        <label for="d{{ $i }}" id="ld{{ $i }}"
                               class="block border-2 rounded-lg p-3 text-center font-bold cursor-pointer
                                      hover:border-blue-400 transition select-none
                                      {{ $marcado ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300' }}"
                               onclick="toggleDia({{ $i }})">
                            {{ $d }}
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="mb-4">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="aplica_festivos" value="1" class="w-5 h-5"
                           {{ $tarifa->aplica_festivos ? 'checked' : '' }}>
                    <span class="font-bold">🎉 Aplica también en festivos</span>
                </label>
            </div>

            <div class="mb-6">
                <label class="block font-bold mb-2">Color:</label>
                <div class="flex gap-3 flex-wrap">
                    @foreach(['#3B82F6','#10B981','#F59E0B','#EF4444','#8B5CF6','#EC4899','#06B6D4','#84CC16'] as $c)
                    <div class="w-10 h-10 rounded-full cursor-pointer border-4 transition hover:scale-110"
                         style="background-color:{{ $c }};
                                border-color:{{ $tarifa->color === $c ? '#1f2937' : 'transparent' }}"
                         onclick="selectColor('{{ $c }}', this)">
                    </div>
                    @endforeach
                </div>
                <input type="hidden" name="color" id="color-val" value="{{ $tarifa->color }}">
            </div>

            <div class="flex gap-3">
                <button type="submit"
                        class="flex-1 bg-green-600 hover:bg-green-700 text-white py-4 rounded-lg font-bold text-lg">
                    ✅ Guardar Cambios
                </button>
                <a href="{{ route('admin.tarifas.index') }}"
                   class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 py-4 rounded-lg font-bold text-lg text-center">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<script>
const sel = new Set([{{ implode(',', $tarifa->dias_semana ?? []) }}]);
function toggleDia(i) {
    const lbl = document.getElementById('ld'+i);
    const chk = document.getElementById('d'+i);
    if (sel.has(i)) {
        sel.delete(i); chk.checked = false;
        lbl.classList.remove('bg-blue-600','text-white','border-blue-600');
        lbl.classList.add('border-gray-300');
    } else {
        sel.add(i); chk.checked = true;
        lbl.classList.add('bg-blue-600','text-white','border-blue-600');
        lbl.classList.remove('border-gray-300');
    }
}
function selectColor(c, el) {
    document.getElementById('color-val').value = c;
    document.querySelectorAll('[onclick^="selectColor"]').forEach(d => d.style.borderColor = 'transparent');
    el.style.borderColor = '#1f2937';
}
</script>
@endsection
