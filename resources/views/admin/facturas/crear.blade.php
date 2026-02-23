@extends('layouts.admin')

@section('content')
    <div class="container mx-auto px-4 py-6 max-w-5xl" x-data="purchaseForm()">

        <!-- HEADER -->
        <div class="flex justify-between items-center mb-8 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900">📦 Gestión de Compras</h1>
                <p class="text-gray-500 mt-1">Ingresa facturas con múltiples productos y crea proveedores al instante.</p>
            </div>
            <a href="{{ route('admin.facturas.index') }}"
                class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-4 py-2 rounded-xl transition font-bold flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                    </path>
                </svg>
                Volver
            </a>
        </div>

        <form method="POST" action="{{ route('admin.facturas.guardar') }}" @submit.prevent="submitForm">
            @csrf

            <!-- TABS NAVIGATION -->
            <div class="flex gap-4 mb-6">
                <template x-for="(tab, index) in ['Datos Factura', 'Productos', 'Totales']">
                    <button type="button" @click="activeTab = index"
                        class="px-6 py-3 rounded-xl font-bold transition flex items-center gap-2"
                        :class="activeTab === index ? 'bg-blue-600 text-white shadow-lg shadow-blue-200' : 'bg-white text-gray-500 hover:bg-gray-50'">
                        <span x-text="index + 1" class="w-6 h-6 rounded-full flex items-center justify-center text-xs"
                            :class="activeTab === index ? 'bg-blue-400 text-white' : 'bg-gray-200 text-gray-600'"></span>
                        <span x-text="tab"></span>
                    </button>
                </template>
            </div>

            <!-- TAB 1: DATOS FACTURA -->
            <div x-show="activeTab === 0"
                class="space-y-6 bg-white p-8 rounded-3xl shadow-xl border border-gray-100 animate-fade-in">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Proveedor con Botón Nuevo -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Proveedor
                            ⭐</label>
                        <div class="flex gap-2">
                            <select name="proveedor_id" x-model="formData.proveedor_id"
                                class="flex-1 border-2 border-gray-100 rounded-xl px-4 py-3 focus:border-blue-500 focus:ring-0 transition bg-gray-50"
                                required>
                                <option value="">-- Selecciona Proveedor --</option>
                                @foreach($proveedores as $prov)
                                    <option value="{{ $prov->id }}">{{ $prov->persona->razon_social }}</option>
                                @endforeach
                            </select>
                            <button type="button" @click="showSupplierModal = true"
                                class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-xl transition shadow-lg shadow-green-100 flex items-center justify-center"
                                title="Nuevo Proveedor">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Número de
                            Factura</label>
                        <input type="text" name="numero_factura" x-model="formData.numero_factura"
                            class="w-full border-2 border-gray-100 rounded-xl px-4 py-3 focus:border-blue-500 focus:ring-0 transition bg-gray-50"
                            placeholder="Ej: FAC-1234">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Fecha de Compra
                            ⭐</label>
                        <input type="date" name="fecha_compra" x-model="formData.fecha_compra"
                            class="w-full border-2 border-gray-100 rounded-xl px-4 py-3 focus:border-blue-500 focus:ring-0 transition bg-gray-50"
                            required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Notas</label>
                        <textarea name="notas" x-model="formData.notas" rows="1"
                            class="w-full border-2 border-gray-100 rounded-xl px-4 py-3 focus:border-blue-500 focus:ring-0 transition bg-gray-50"
                            placeholder="Observaciones de la compra..."></textarea>
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="button" @click="activeTab = 1"
                        class="bg-blue-600 text-white px-10 py-4 rounded-2xl font-black shadow-xl shadow-blue-200 hover:bg-blue-700 transition transform hover:scale-105">
                        Siguiente: Agregar Productos →
                    </button>
                </div>
            </div>

            <!-- TAB 2: PRODUCTOS -->
            <div x-show="activeTab === 1"
                class="space-y-6 bg-white p-8 rounded-3xl shadow-xl border border-gray-100 animate-fade-in">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Detalle de Productos</h3>
                    <button type="button" @click="addItem()"
                        class="bg-indigo-50 text-indigo-600 px-4 py-2 rounded-xl font-bold border-2 border-indigo-100 hover:bg-indigo-100 transition flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Agregar Fila
                    </button>
                </div>

                <div class="overflow-x-auto rounded-2xl border border-gray-100">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 text-gray-600 uppercase text-xs font-black">
                            <tr>
                                <th class="px-4 py-4 w-1/3">Producto</th>
                                <th class="px-4 py-4">Cantidad</th>
                                <th class="px-4 py-4">Precio Unit. (C/IVA)</th>
                                <th class="px-4 py-4">Subtotal</th>
                                <th class="px-4 py-4 text-center"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <template x-for="(item, index) in items" :key="index">
                                <tr class="hover:bg-blue-50/30 transition">
                                    <td class="px-4 py-3">
                                        <select x-model="item.producto_id"
                                            class="w-full border-gray-200 rounded-lg focus:border-blue-400 focus:ring-0 text-sm">
                                            <option value="">Seleccionar...</option>
                                            @foreach($productos as $prod)
                                                <option value="{{ $prod->id }}">{{ $prod->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="number" x-model.number="item.cantidad" @input="calculateTotals()"
                                            class="w-24 border-gray-200 rounded-lg focus:border-blue-400 focus:ring-0 text-sm text-center"
                                            step="0.01" min="0.01">
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="relative">
                                            <span class="absolute left-2 top-1.2 text-gray-400 text-xs">$</span>
                                            <input type="number" x-model.number="item.costo_unitario"
                                                @input="calculateTotals()"
                                                class="w-32 pl-5 border-gray-200 rounded-lg focus:border-blue-400 focus:ring-0 text-sm"
                                                step="0.01">
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="font-mono font-bold text-gray-700"
                                            x-text="formatCurrency(item.cantidad * item.costo_unitario)"></span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <button type="button" @click="removeItem(index)"
                                            class="text-red-400 hover:text-red-600 transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-between items-center pt-6">
                    <button type="button" @click="activeTab = 0" class="text-gray-500 font-bold hover:text-gray-700">
                        ← Volver a Datos
                    </button>
                    <button type="button" @click="activeTab = 2"
                        class="bg-blue-600 text-white px-10 py-4 rounded-2xl font-black shadow-xl shadow-blue-200 hover:bg-blue-700 transition transform hover:scale-105">
                        Siguiente: Revisar Totales →
                    </button>
                </div>
            </div>

            <!-- TAB 3: TOTALES -->
            <div x-show="activeTab === 2" class="animate-fade-in">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Col Izquierda: Impuestos -->
                    <div class="md:col-span-2 space-y-6">
                        <div class="bg-white p-8 rounded-3xl shadow-xl border border-gray-100">
                            <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                                <span
                                    class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center text-sm">%</span>
                                Impuestos y Retenciones
                            </h3>

                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-2 uppercase">Tipo de
                                        Impuesto</label>
                                    <select x-model="formData.impuesto_tipo" @change="calculateTotals()"
                                        class="w-full border-2 border-gray-50 rounded-xl px-4 py-3 focus:border-blue-400 focus:ring-0 transition bg-gray-50">
                                        <option value="">Ninguno</option>
                                        <option value="IVA">IVA</option>
                                        <option value="CONSUMO">Consumo</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-2 uppercase">Porcentaje
                                        (%)</label>
                                    <input type="number" x-model.number="formData.impuesto_porcentaje"
                                        @input="calculateTotals()"
                                        class="w-full border-2 border-gray-50 rounded-xl px-4 py-3 focus:border-blue-400 focus:ring-0 transition bg-gray-50 font-bold"
                                        placeholder="19" step="0.1">
                                </div>
                            </div>
                        </div>

                        <button type="submit"
                            class="w-full bg-green-500 hover:bg-green-600 text-white py-6 rounded-3xl text-2xl font-black shadow-2xl shadow-green-200 transition transform hover:-translate-y-1 active:scale-95 flex items-center justify-center gap-4">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            REGISTRAR FACTURA COMPLETA
                        </button>

                        <button type="button" @click="activeTab = 1"
                            class="w-full text-gray-400 font-bold py-2 hover:text-gray-600">
                            ← Editar productos agregados
                        </button>
                    </div>

                    <!-- Col Derecha: Resumen de Dinero -->
                    <div class="bg-gray-900 rounded-3xl p-8 text-white shadow-2xl flex flex-col justify-between">
                        <div>
                            <h3 class="text-xs font-black text-blue-400 uppercase tracking-widest mb-8">Resumen Financiero
                            </h3>

                            <div class="space-y-6">
                                <div class="flex justify-between items-center text-gray-400">
                                    <span>Subtotal Bruto:</span>
                                    <span class="font-mono text-xl" x-text="formatCurrency(totals.grossSubtotal)"></span>
                                </div>
                                <div class="flex justify-between items-center text-gray-400">
                                    <span x-text="'Impuesto (' + (formData.impuesto_porcentaje || 0) + '%):'"></span>
                                    <span class="font-mono text-xl" x-text="formatCurrency(totals.taxAmount)"></span>
                                </div>
                                <div class="border-t border-gray-800 pt-6 mt-6">
                                    <p class="text-[10px] font-bold text-gray-500 uppercase mb-1">Total a Pagar</p>
                                    <p class="text-6xl font-black text-white tracking-tighter"
                                        x-text="formatCurrency(totals.grandTotal)"></p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 bg-white/5 p-4 rounded-2xl border border-white/10">
                            <p class="text-[10px] text-gray-400 italic">
                                * El sistema actualizará el stock de <span class="text-blue-400 font-bold"
                                    x-text="items.length"></span> ítems y generará los registros correspondientes en el
                                Kardex.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- MODAL NUEVO PROVEEDOR -->
        <div x-show="showSupplierModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showSupplierModal = false"></div>

                <div class="bg-white rounded-3xl shadow-2xl relative w-full max-w-lg overflow-hidden animate-pop-in"
                    x-data="supplierModal()">
                    <div class="p-8 border-b border-gray-50 bg-gray-50/50 flex justify-between items-center">
                        <div>
                            <h2 class="text-2xl font-black text-gray-800">✨ Nuevo Proveedor</h2>
                            <p class="text-xs text-gray-500 mt-1">Regístralo rápido sin salir de la compra.</p>
                        </div>
                        <button @click="showSupplierModal = false" class="text-gray-400 hover:text-gray-600 transition">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="p-8 space-y-5">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2">
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Razón Social ⭐</label>
                                <input type="text" x-model="sData.razon_social"
                                    class="w-full border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-blue-400 transition"
                                    placeholder="Nombre completo o Empresa">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Tipo Doc ⭐</label>
                                <select x-model="sData.documento_id"
                                    class="w-full border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-blue-400 transition">
                                    <option value="">Seleccione...</option>
                                    @foreach($documentos as $doc)
                                        <option value="{{ $doc->id }}">{{ $doc->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">N° Documento/NIT
                                    ⭐</label>
                                <input type="text" x-model="sData.numero_documento"
                                    class="w-full border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-blue-400 transition"
                                    placeholder="123456789">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Tipo Persona</label>
                                <select x-model="sData.tipo"
                                    class="w-full border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-blue-400 transition">
                                    <option value="JURIDICA">Jurídica</option>
                                    <option value="NATURAL">Natural</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Teléfono</label>
                                <input type="text" x-model="sData.telefono"
                                    class="w-full border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-blue-400 transition">
                            </div>
                        </div>
                    </div>

                    <div class="p-8 bg-gray-50 flex gap-4">
                        <button @click="saveSupplier" :disabled="loading"
                            class="flex-1 bg-blue-600 text-white font-black py-4 rounded-2xl shadow-xl shadow-blue-100 items-center justify-center flex gap-2 disabled:opacity-50">
                            <span x-show="!loading">GUARDAR Y SELECCIONAR</span>
                            <span x-show="loading">GUARDANDO...</span>
                        </button>
                        <button @click="showSupplierModal = false"
                            class="px-6 py-4 text-gray-400 font-bold hover:text-gray-600 transition">
                            CANCELAR
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes popIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.3s ease-out;
        }

        .animate-pop-in {
            animation: popIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        [x-cloak] {
            display: none !important;
        }
    </style>

    <script>
        function purchaseForm() {
            return {
                activeTab: 0,
                showSupplierModal: false,
                formData: {
                    proveedor_id: '',
                    numero_factura: '',
                    fecha_compra: '{{ date('Y-m-d') }}',
                    notas: '',
                    total_pagado: 0,
                    impuesto_tipo: '',
                    impuesto_porcentaje: 0,
                    impuesto_valor: 0,
                    subtotal_calculado: 0
                },
                items: [
                    { producto_id: '', cantidad: 1, costo_unitario: 0 }
                ],
                totals: {
                    grossSubtotal: 0,
                    taxAmount: 0,
                    grandTotal: 0
                },

                addItem() {
                    this.items.push({ producto_id: '', cantidad: 1, costo_unitario: 0 });
                },

                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                        this.calculateTotals();
                    }
                },

                calculateTotals() {
                    let subtotal = this.items.reduce((sum, item) => sum + (item.cantidad * item.costo_unitario), 0);
                    let taxPct = this.formData.impuesto_porcentaje || 0;

                    // Si la factura INCLUYE impuestos, el total a pagar es el subtotal de los productos.
                    // Pero como ingresamos "Precio Unitario con IVA", el subtotal ya es el Grand Total.
                    // Calculamos hacia atrás el subtotal real (neto).

                    this.totals.grandTotal = subtotal;
                    this.totals.grossSubtotal = subtotal / (1 + (taxPct / 100));
                    this.totals.taxAmount = this.totals.grandTotal - this.totals.grossSubtotal;

                    // Update formData for submission
                    this.formData.total_pagado = this.totals.grandTotal;
                    this.formData.impuesto_valor = this.totals.taxAmount;
                    this.formData.subtotal_calculado = this.totals.grossSubtotal;
                },

                formatCurrency(val) {
                    return new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 0 }).format(val);
                },

                submitForm() {
                    if (!this.formData.proveedor_id) return alert('Selecciona un proveedor');
                    if (this.items.some(i => !i.producto_id || i.cantidad <= 0)) return alert('Completa los productos correctamente');

                    // Preparar el FormData para enviar
                    let data = new FormData();
                    data.append('_token', '{{ csrf_token() }}');
                    data.append('proveedor_id', this.formData.proveedor_id);
                    data.append('numero_factura', this.formData.numero_factura);
                    data.append('fecha_compra', this.formData.fecha_compra);
                    data.append('notas', this.formData.notas);
                    data.append('total_pagado', this.formData.total_pagado);
                    data.append('impuesto_tipo', this.formData.impuesto_tipo);
                    data.append('impuesto_porcentaje', this.formData.impuesto_porcentaje);
                    data.append('impuesto_valor', this.formData.impuesto_valor);
                    data.append('subtotal_calculado', this.formData.subtotal_calculado);

                    this.items.forEach((item, index) => {
                        data.append(`items[${index}][producto_id]`, item.producto_id);
                        data.append(`items[${index}][cantidad]`, item.cantidad);
                        data.append(`items[${index}][costo_unitario]`, item.costo_unitario);
                    });

                    fetch('{{ route('admin.facturas.guardar') }}', {
                        method: 'POST',
                        body: data
                    }).then(res => {
                        if (res.redirected) window.location.href = res.url;
                        else alert('Error al guardar. Revisa la consola.');
                    });
                }
            };
        }

        function supplierModal() {
            return {
                loading: false,
                sData: {
                    razon_social: '',
                    documento_id: '',
                    numero_documento: '',
                    tipo: 'JURIDICA',
                    telefono: '',
                    email: '',
                    direccion: ''
                },
                saveSupplier() {
                    if (!this.sData.razon_social || !this.sData.numero_documento) return alert('Campos obligatorios');
                    this.loading = true;

                    fetch('{{ route('admin.proveedores.quick-store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(this.sData)
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                // Agregar al select de la página
                                const select = document.querySelector('select[name="proveedor_id"]');
                                const opt = document.createElement('option');
                                opt.value = data.id;
                                opt.text = data.razon_social;
                                opt.selected = true;
                                select.appendChild(opt);

                                // Actualizar el modelo de Alpine
                                const alpineMain = document.querySelector('[x-data="purchaseForm()"]').__x.$data;
                                alpineMain.formData.proveedor_id = data.id;

                                this.loading = false;
                                document.querySelector('[x-data="purchaseForm()"]').__x.$data.showSupplierModal = false;
                            } else {
                                alert(data.message || 'Error al guardar');
                                this.loading = false;
                            }
                        });
                }
            };
        }
    </script>
@endsection