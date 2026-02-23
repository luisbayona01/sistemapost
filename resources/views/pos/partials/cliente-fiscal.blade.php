{{--
Componente: Bloque de datos del cliente para Factura Electrónica
Uso: Se muestra dinámicamente en el checkout cuando solicita_factura = true
Compatible con Alpine.js (posApp context)
--}}
<div x-show="solicitaFactura" x-cloak x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
    class="mt-3 border-2 border-blue-200 rounded-2xl p-4 bg-blue-50 space-y-3">

    <p class="text-[10px] font-black text-blue-600 uppercase tracking-widest flex items-center gap-2">
        <i class="fas fa-file-invoice"></i> Datos para Factura Electrónica
    </p>

    {{-- Tipo + Número Documento --}}
    <div class="grid grid-cols-5 gap-2">
        <div class="col-span-2">
            <label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Tipo Doc.</label>
            <select x-model="cliente.tipo_doc"
                class="w-full bg-white border-2 border-blue-100 rounded-xl px-2 py-2 text-xs font-bold text-slate-800 focus:border-blue-500 outline-none">
                <option value="CC">CC</option>
                <option value="NIT">NIT</option>
                <option value="CE">C.Ext</option>
                <option value="PPN">Pasaporte</option>
            </select>
        </div>
        <div class="col-span-3">
            <label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">
                Número <span x-show="cliente.tipo_doc === 'NIT'" class="text-blue-500">+ Dígito</span>
            </label>
            <div class="relative">
                <input type="text" x-model="cliente.documento" @input="validarNIT()"
                    :placeholder="cliente.tipo_doc === 'NIT' ? '900123456-7' : '10203040'"
                    class="w-full bg-white border-2 rounded-xl px-3 py-2 text-xs font-bold text-slate-800 outline-none transition-all"
                    :class="nitValido === false ? 'border-red-400' : 'border-blue-100 focus:border-blue-500'">
                <span x-show="nitValido === true" class="absolute right-2 top-2 text-emerald-500 text-xs">
                    <i class="fas fa-check-circle"></i>
                </span>
                <span x-show="nitValido === false" class="absolute right-2 top-2 text-red-400 text-xs">
                    <i class="fas fa-times-circle"></i>
                </span>
            </div>
            <p x-show="nitValido === false" class="text-[9px] text-red-500 mt-0.5 font-bold">
                NIT inválido — Verifica el dígito de verificación
            </p>
        </div>
    </div>

    {{-- Nombre / Razón Social --}}
    <div>
        <label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">Nombre / Razón Social *</label>
        <input type="text" x-model="cliente.nombre" placeholder="Ej: Juan Pérez o Empresa ABC S.A.S"
            class="w-full bg-white border-2 border-blue-100 rounded-xl px-3 py-2 text-xs font-bold text-slate-800 focus:border-blue-500 outline-none">
    </div>

    {{-- Correo (opcional) --}}
    <div>
        <label class="block text-[9px] font-bold text-slate-500 uppercase mb-1">
            Correo <span class="text-slate-400 normal-case font-normal">(opcional – envío digital)</span>
        </label>
        <input type="email" x-model="cliente.email" placeholder="cliente@ejemplo.com"
            class="w-full bg-white border-2 border-blue-100 rounded-xl px-3 py-2 text-xs font-bold text-slate-800 focus:border-blue-500 outline-none">
    </div>

    {{-- Botón limpiar --}}
    <button type="button"
        @click="solicitaFactura = false; cliente = {tipo_doc:'CC', documento:'', nombre:'', email:'', telefono:'', preferencia:'fe_todo'}; nitValido = null"
        class="w-full text-center text-[9px] font-bold text-blue-400 hover:text-red-500 transition-colors uppercase tracking-widest pt-1">
        <i class="fas fa-times mr-1"></i> Quitar factura electrónica
    </button>
</div>