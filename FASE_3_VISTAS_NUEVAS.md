# VISTAS NUEVAS A CREAR - FASE 3.2

## 🆕 Vistas Nuevas (2 total)

Estas vistas **NO EXISTEN** actualmente y deben ser creadas en FASE 3.2:

---

## 1. `resources/views/caja/show.blade.php`

**Propósito:** Mostrar detalles de una caja abierta con su saldo actual

**Funcionalidad:**
- Mostrar datos de apertura (fecha, hora, usuario)
- Mostrar saldo actual calculado
- Listar todos los movimientos de la caja
- Botón para cerrar caja

**Componentes necesarios:**
- Tabla de movimientos con columnas: Fecha, Tipo, Monto, Saldo Acumulado
- Card con información de apertura
- Card con saldo total
- Botón "Cerrar Caja" que abre formulario de cierre

**Ruta:** `GET /cajas/{caja}` (CajaController@show)

**Datos que recibe:**
```php
$caja = Caja::find($id); // Modelo Caja con relación movimientos
$cajaAbierta = $caja->estaAbierta(); // boolean
$saldoActual = $caja->calcularSaldo(); // decimal
$movimientos = $caja->movimientos()->latest()->paginate(15); // Collection
```

---

## 2. `resources/views/caja/close.blade.php`

**Propósito:** Formulario para cerrar caja y registrar diferencia

**Funcionalidad:**
- Mostrar saldo teórico calculado
- Input para ingresar dinero real en caja
- Calcular automáticamente diferencia (real - teórico)
- Mostrar tabla resumida de movimientos
- Botón para confirmar cierre

**Componentes necesarios:**
- Display del saldo teórico (read-only)
- Input numérico para dinero real
- Display de diferencia (actualizar con JavaScript)
- Tabla comprimida de movimientos resumidos
- Formulario POST hacia `cajas.close` route

**Ruta:** `GET /cajas/{caja}/close-form` (CajaController@showCloseForm)

**Datos que recibe:**
```php
$caja = Caja::find($id);
$saldoTeórico = $caja->calcularSaldo(); // decimal
$movimientos = $caja->movimientos()->latest()->limit(20)->get();
```

---

## Características Compartidas (Bootstrap → Tailwind)

Ambas vistas deben:
- ✅ Usar Tailwind CSS (no Bootstrap)
- ✅ Seguir layout base de `layouts/app.blade.php`
- ✅ Ser responsive (mobile/tablet/desktop)
- ✅ Usar blade components existentes (`x-forms.*`, etc.)
- ✅ Validar con JavaScript (diferencia real-time)
- ✅ Tener breadcrumbs de navegación

---

## 🔄 Vistas Que Serán MODIFICADAS (no creadas)

Estas vistas ya existen pero necesitan actualización:

| Vista | Cambio Principal | Razón |
|-------|-----------------|-------|
| `caja/create.blade.php` | Actualizar CSS Bootstrap → Tailwind | Parte de FASE 3.2 |
| `caja/index.blade.php` | Actualizar CSS Bootstrap → Tailwind | Parte de FASE 3.2 |
| `venta/create.blade.php` | Actualizar CSS Bootstrap → Tailwind + agregar display de tarifa | Parte de FASE 3.2 |
| `venta/index.blade.php` | Actualizar CSS Bootstrap → Tailwind | Parte de FASE 3.2 |
| `movimiento/create.blade.php` | Actualizar CSS Bootstrap → Tailwind + validar caja abierta | Parte de FASE 3.2 |
| `movimiento/index.blade.php` | Actualizar CSS Bootstrap → Tailwind | Parte de FASE 3.2 |

---

## 📋 Checklist para FASE 3.2

### Vistas Nuevas:
- [ ] Crear `caja/show.blade.php`
- [ ] Crear `caja/close.blade.php`

### Vistas a Actualizar (Críticas):
- [ ] Actualizar `layouts/app.blade.php` (PRIMERO)
- [ ] Actualizar `caja/create.blade.php`
- [ ] Actualizar `caja/index.blade.php`
- [ ] Actualizar `venta/create.blade.php`
- [ ] Actualizar `venta/index.blade.php`
- [ ] Actualizar `venta/show.blade.php`
- [ ] Actualizar `movimiento/create.blade.php`
- [ ] Actualizar `movimiento/index.blade.php`

### Vistas a Actualizar (Secundarias):
- [ ] Actualizar resto de vistas por módulo (ver FASE_3_2_PLAN_VISTAS_BOOTSTRAP_TAILWIND.md)

---

## 🎨 Estructura HTML Template para Referencia

### caja/show.blade.php
```blade
@extends('layouts.app')
@section('content')
<div class="max-w-6xl mx-auto">
    <h1 class="text-3xl font-bold mb-6">Caja Abierta</h1>
    
    <!-- Card Información Apertura -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-6 rounded shadow">
            <h3 class="text-gray-500 text-sm">Abierta por</h3>
            <p class="text-2xl font-bold">{{ $caja->usuario->name }}</p>
        </div>
        <div class="bg-white p-6 rounded shadow">
            <h3 class="text-gray-500 text-sm">Hora Apertura</h3>
            <p class="text-2xl font-bold">{{ $caja->hora_apertura }}</p>
        </div>
        <div class="bg-blue-100 p-6 rounded shadow">
            <h3 class="text-gray-700 text-sm font-bold">SALDO ACTUAL</h3>
            <p class="text-3xl font-bold text-blue-600">{{ number_format($saldoActual, 2) }}</p>
        </div>
    </div>

    <!-- Tabla Movimientos -->
    <div class="bg-white rounded shadow overflow-hidden">
        <div class="p-6 border-b">
            <h2 class="text-lg font-bold">Movimientos</h2>
        </div>
        <table class="w-full">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Fecha</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Tipo</th>
                    <th class="px-6 py-3 text-right text-sm font-semibold">Monto</th>
                    <th class="px-6 py-3 text-right text-sm font-semibold">Saldo</th>
                </tr>
            </thead>
            <tbody>
                @foreach($movimientos as $mov)
                <tr class="border-t hover:bg-gray-50">
                    <td class="px-6 py-3 text-sm">{{ $mov->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-6 py-3 text-sm">
                        <span class="px-2 py-1 rounded {{ $mov->esIngreso() ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $mov->tipo }}
                        </span>
                    </td>
                    <td class="px-6 py-3 text-sm text-right">{{ number_format($mov->monto, 2) }}</td>
                    <td class="px-6 py-3 text-sm text-right font-semibold">{{ number_format($mov->saldoAcumulado ?? 0, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Botón Cerrar -->
    <div class="mt-6 flex gap-2">
        <a href="{{ route('cajas.closeForm', $caja) }}" class="px-6 py-3 bg-red-600 text-white rounded hover:bg-red-700">
            Cerrar Caja
        </a>
    </div>
</div>
@endsection
```

### caja/close.blade.php
```blade
@extends('layouts.app')
@section('content')
<div class="max-w-2xl mx-auto">
    <h1 class="text-3xl font-bold mb-6">Cerrar Caja</h1>

    <form action="{{ route('cajas.close', $caja) }}" method="POST" class="bg-white p-6 rounded shadow">
        @csrf
        
        <!-- Saldo Teórico -->
        <div class="mb-6">
            <label class="block text-sm font-semibold mb-2">Saldo Teórico</label>
            <input type="text" readonly class="w-full px-4 py-2 border rounded bg-gray-100" 
                   value="{{ number_format($saldoTeórico, 2) }}">
        </div>

        <!-- Dinero Real -->
        <div class="mb-6">
            <label class="block text-sm font-semibold mb-2">Dinero Real en Caja</label>
            <input type="number" name="dinero_real" step="0.01" required 
                   class="w-full px-4 py-2 border rounded" placeholder="0.00">
        </div>

        <!-- Diferencia (calculada con JS) -->
        <div class="mb-6 p-4 rounded bg-blue-50 border border-blue-200">
            <label class="block text-sm font-semibold mb-2">Diferencia</label>
            <p class="text-2xl font-bold" id="diferencia">0.00</p>
            <p class="text-xs text-gray-600 mt-2">Positivo = sobrante | Negativo = faltante</p>
        </div>

        <!-- Resumen Movimientos -->
        <div class="mb-6">
            <h3 class="text-sm font-semibold mb-3">Últimos Movimientos</h3>
            <table class="w-full text-sm">
                <tbody>
                    @foreach($movimientos->take(10) as $mov)
                    <tr class="border-b">
                        <td class="py-2">{{ $mov->tipo }}</td>
                        <td class="py-2 text-right">{{ number_format($mov->monto, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Botones -->
        <div class="flex gap-2">
            <button type="submit" class="px-6 py-3 bg-green-600 text-white rounded hover:bg-green-700">
                Confirmar Cierre
            </button>
            <a href="{{ route('cajas.show', $caja) }}" class="px-6 py-3 bg-gray-600 text-white rounded hover:bg-gray-700">
                Cancelar
            </a>
        </div>
    </form>
</div>

<script>
document.querySelector('input[name="dinero_real"]').addEventListener('input', function() {
    const teórico = {{ $saldoTeórico }};
    const real = parseFloat(this.value) || 0;
    const diferencia = real - teórico;
    document.getElementById('diferencia').textContent = diferencia.toFixed(2);
});
</script>
@endsection
```

---

## 📝 Notas Importantes

1. **Orden de Creación:** Primero `caja/show.blade.php`, luego `caja/close.blade.php`
2. **Dependencias:** Ambas dependen de que `layouts/app.blade.php` esté actualizado a Tailwind
3. **Componentes:** Usar componentes blade existentes cuando sea posible
4. **Validación:** JavaScript para diferencia real-time en `caja/close.blade.php`
5. **Estilos:** Solo Tailwind CSS, no agregar CSS personalizado

---

**Estado:** Documentación lista para FASE 3.2  
**Próximo:** Crear estas 2 vistas después de actualizar `layouts/app.blade.php`
