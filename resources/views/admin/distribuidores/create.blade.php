@extends('layouts.app')

@section('title', 'Nuevo Distribuidor')

@section('content')
    <div class="px-6 py-8 max-w-4xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-slate-900">Nuevo Distribuidor</h1>
            <p class="text-slate-600 mt-1">Registre la información de contacto de la casa distribuidora.</p>
        </div>

        <x-breadcrumb.template class="mb-6">
            <x-breadcrumb.item :href="route('admin.dashboard.index')" content="Administración" />
            <x-breadcrumb.item :href="route('distribuidores.index')" content="Distribuidores" />
            <x-breadcrumb.item active='true' content="Nuevo" />
        </x-breadcrumb.template>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <form action="{{ route('distribuidores.store') }}" method="POST" class="p-8">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div class="col-span-1 md:col-span-2">
                        <label for="nombre" class="block text-sm font-semibold text-slate-700 mb-2">Nombre del Distribuidor
                            <span class="text-red-500">*</span></label>
                        <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all outline-none"
                            placeholder="Ej: Warner Bros, Universal Pictures..." required>
                        @error('nombre') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="contacto" class="block text-sm font-semibold text-slate-700 mb-2">Persona de
                            Contacto</label>
                        <input type="text" name="contacto" id="contacto" value="{{ old('contacto') }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all outline-none"
                            placeholder="Nombre del representante">
                    </div>

                    <div>
                        <label for="telefono" class="block text-sm font-semibold text-slate-700 mb-2">Teléfono</label>
                        <input type="text" name="telefono" id="telefono" value="{{ old('telefono') }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all outline-none"
                            placeholder="Teléfono de contacto">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all outline-none"
                            placeholder="ejemplo@distribuidora.com">
                    </div>

                    <div>
                        <label for="activo" class="block text-sm font-semibold text-slate-700 mb-2">Estado</label>
                        <select name="activo" id="activo"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all outline-none">
                            <option value="1" {{ old('activo', '1') == '1' ? 'selected' : '' }}>Activo</option>
                            <option value="0" {{ old('activo') == '0' ? 'selected' : '' }}>Inactivo</option>
                        </select>
                    </div>

                    <div class="col-span-1 md:col-span-2">
                        <label for="notas" class="block text-sm font-semibold text-slate-700 mb-2">Notas adicionales</label>
                        <textarea name="notas" id="notas" rows="3"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all outline-none resize-none"
                            placeholder="Información relevante, convenios, etc.">{{ old('notas') }}</textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                    <a href="{{ route('distribuidores.index') }}"
                        class="px-6 py-2.5 rounded-xl text-slate-600 font-semibold hover:bg-slate-50 transition-colors">Cancelar</a>
                    <button type="submit"
                        class="px-8 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition-all shadow-md hover:shadow-lg transform active:scale-95">
                        Guardar Distribuidor
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
