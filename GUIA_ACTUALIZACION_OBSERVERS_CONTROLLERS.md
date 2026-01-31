# 📝 GUÍA DE ACTUALIZACIÓN - OBSERVERS Y CONTROLLERS

**Fase:** 2.2 - Actualización de dependencias post-modelos  
**Prioridad:** MEDIA  
**Estimado:** 2-3 horas

---

## ⚠️ TAREAS PENDIENTES

Aunque los modelos están actualizados, existen componentes que **dependen** de los cambios realizados y deben ser verificados/actualizados:

---

## 1. 🔔 OBSERVERS QUE NECESITAN REVISIÓN

### VentaObserver (Crítico ⚠️)

**Ubicación:** `app/Observers/VentaObsever.php` (Nota: hay error de typo en nombre)

**Cambios requeridos:**

```php
// El observer necesita capturar empresa_id automáticamente
public function creating(Venta $venta): void
{
    // ✅ AGREGAR: Asegurar que empresa_id se captura del usuario autenticado
    if (auth()->check() && !$venta->empresa_id) {
        $venta->empresa_id = auth()->user()->empresa_id;
    }
    
    // Código existente...
}

// ✅ REVISAR: Si calcula tarifa, usar el nuevo método
public function updating(Venta $venta): void
{
    // Si aplicaba tarifa manualmente, cambiar por:
    if ($venta->isDirty('tarifa_servicio') || $venta->isDirty('subtotal')) {
        $venta->calcularTarifa($venta->tarifa_servicio);
    }
}
```

**Tareas:**
- [ ] Revisar línea por línea
- [ ] Asegurar que empresa_id se captura automáticamente
- [ ] Si hay cálculo de tarifa, actualizar para usar calcularTarifa()
- [ ] Si hay movimientos en caja, verificar que venta_id se asigna

---

### CajaObserver (Crítico ⚠️)

**Ubicación:** `app/Observers/CajaObserver.php`

**Cambios requeridos:**

```php
// ✅ AGREGAR: Capturar empresa_id
public function creating(Caja $caja): void
{
    if (auth()->check() && !$caja->empresa_id) {
        $caja->empresa_id = auth()->user()->empresa_id;
    }
    
    // ✅ REVISAR: Si hay lógica de saldo_inicial, asegurar decimales
    $caja->saldo_inicial = (float) $caja->saldo_inicial ?? 0;
    
    // Código existente...
}

// ✅ REVISAR: Si hay cierre automático, usar método cerrar()
public function updating(Caja $caja): void
{
    if ($caja->isDirty('estado') && $caja->estado === 'cerrada') {
        // Cambiar por: $caja->cerrar($caja->saldo_final);
    }
}
```

**Tareas:**
- [ ] Revisar línea por línea
- [ ] Asegurar que empresa_id se captura automáticamente
- [ ] Si hay lógica de cierre, actualizar para usar cerrar()
- [ ] Si hay cálculo de saldo, usar calcularSaldo()

---

### CompraObserver (Crítico ⚠️)

**Ubicación:** `app/Observers/CompraObserver.php`

**Cambios requeridos:**

```php
// ✅ AGREGAR: Capturar empresa_id
public function creating(Compra $compra): void
{
    if (auth()->check() && !$compra->empresa_id) {
        $compra->empresa_id = auth()->user()->empresa_id;
    }
    
    // Código existente...
}
```

**Tareas:**
- [ ] Revisar línea por línea
- [ ] Asegurar que empresa_id se captura automáticamente
- [ ] Si actualiza kardex, pasar empresa_id

---

### InventarioObserver (Importante)

**Ubicación:** `app/Observers/InventarioObserver.php`

**Cambios requeridos:**

```php
// ✅ AGREGAR: Capturar empresa_id
public function creating(Inventario $inventario): void
{
    if (auth()->check() && !$inventario->empresa_id) {
        $inventario->empresa_id = auth()->user()->empresa_id;
    }
}

// ✅ REVISAR: Si hay método de stock, usar aumentarStock/disminuirStock
public function updating(Inventario $inventario): void
{
    if ($inventario->isDirty('cantidad')) {
        $cantidadAnterior = $inventario->getOriginal('cantidad');
        $diferencia = $inventario->cantidad - $cantidadAnterior;
        
        if ($diferencia > 0) {
            // $inventario->aumentarStock($diferencia);
        } else if ($diferencia < 0) {
            // $inventario->disminuirStock(abs($diferencia));
        }
    }
}
```

**Tareas:**
- [ ] Revisar línea por línea
- [ ] Asegurar que empresa_id se captura automáticamente

---

## 2. 🎮 CONTROLLERS QUE NECESITAN REVISIÓN

### Patrón General para Todos los Controllers

**Crear venta/compra/caja/etc:**

```php
// ❌ ANTES
$venta = Venta::create($validated);

// ✅ DESPUÉS
$validated['empresa_id'] = auth()->user()->empresa_id;
$venta = Venta::create($validated);
```

**O mejor, usar el observer:**

```php
// ✅ MEJOR (El observer lo hace automáticamente)
$venta = Venta::create($validated);
// Si el observer está correctamente configurado, empresa_id será capturado
```

---

### VentaController (Crítico)

**Métodos a revisar:**

```php
// ✅ store() - Crear venta
public function store(StoreVentaRequest $request)
{
    $validated = $request->validated();
    $validated['empresa_id'] = auth()->user()->empresa_id; // ASEGURAR ESTO
    
    // ✅ Agregar tarifa al crear
    $venta = Venta::create($validated);
    $venta->calcularTarifa($validated['tarifa_servicio'] ?? 0);
    
    return response()->json($venta);
}

// ✅ Cualquier endpoint que calcule tarifa debe usar calcularTarifa()
public function aplicarTarifa(Request $request)
{
    $venta = Venta::findOrFail($request->id);
    $venta->calcularTarifa($request->tarifa_servicio);
    $venta->save();
    
    return response()->json($venta);
}
```

**Tareas:**
- [ ] Verificar método store()
- [ ] Verificar cualquier método que aplique tarifa
- [ ] Verificar que crea PaymentTransaction si existe
- [ ] Verificar que asigna empresa_id

---

### CajaController (Crítico)

**Métodos a revisar:**

```php
// ✅ apertura() - Abrir caja
public function apertura(StoreCajaRequest $request)
{
    $validated = $request->validated();
    $validated['empresa_id'] = auth()->user()->empresa_id;
    
    $caja = Caja::create($validated);
    return response()->json($caja);
}

// ✅ cierre() - Cerrar caja
public function cierre(CloseCajaRequest $request)
{
    $caja = Caja::findOrFail($request->caja_id);
    $caja->cerrar($request->monto_entregado);
    
    return response()->json($caja);
}

// ✅ saldo() - Obtener saldo
public function saldo(Request $request)
{
    $caja = Caja::findOrFail($request->caja_id);
    
    return response()->json([
        'saldo_inicial' => $caja->saldo_inicial,
        'saldo_actual' => $caja->calcularSaldo(),
        'saldo_final' => $caja->saldo_final,
    ]);
}
```

**Tareas:**
- [ ] Verificar método de apertura
- [ ] Actualizar método de cierre para usar cerrar()
- [ ] Actualizar método de saldo para usar calcularSaldo()
- [ ] Verificar que asigna empresa_id

---

### CompraController (Importante)

**Métodos a revisar:**

```php
// ✅ store() - Crear compra
public function store(StoreCompraRequest $request)
{
    $validated = $request->validated();
    $validated['empresa_id'] = auth()->user()->empresa_id;
    
    $compra = Compra::create($validated);
    
    // ✅ Si hay lógica de kardex, asegurar que recibe empresa_id
    // Evento o listener debería recibir empresa_id
    
    return response()->json($compra);
}
```

**Tareas:**
- [ ] Verificar método store()
- [ ] Asegurar que pasa empresa_id a listeners/observers

---

### InventarioController (Importante)

**Métodos a revisar:**

```php
// ✅ Cualquier método que aumente/disminuya stock
public function aumentarStock(Request $request)
{
    $inventario = Inventario::findOrFail($request->inventario_id);
    $inventario->aumentarStock($request->cantidad);
    
    return response()->json($inventario);
}

public function disminuirStock(Request $request)
{
    $inventario = Inventario::findOrFail($request->inventario_id);
    $inventario->disminuirStock($request->cantidad);
    
    return response()->json($inventario);
}
```

**Tareas:**
- [ ] Encontrar métodos de actualización de stock
- [ ] Reemplazar lógica manual por aumentarStock()/disminuirStock()

---

## 3. 🔗 LISTENERS QUE NECESITAN REVISIÓN

### CreateVentaDetalleEvent

**Ubicación:** `app/Listeners/CreateVentaDetalleListener.php` (o similar)

**Debe hacer:**

```php
public function handle(CreateVentaDetalleEvent $event)
{
    $venta = $event->venta;
    
    // ✅ Asegurar que paymentTransaction recibe empresa_id
    PaymentTransaction::create([
        'empresa_id' => $venta->empresa_id,
        'venta_id' => $venta->id,
        // ... otros campos
    ]);
    
    // ✅ Asegurar que kardex recibe empresa_id
    // ... código de kardex
}
```

**Tareas:**
- [ ] Revisar listener
- [ ] Asegurar que pasa empresa_id a PaymentTransaction
- [ ] Asegurar que pasa empresa_id a Kardex

---

### CreateCompraDetalleEvent

**Similar a VentaDetalle**

```php
public function handle(CreateCompraDetalleEvent $event)
{
    $compra = $event->compra;
    
    // ✅ Asegurar que kardex recibe empresa_id
    Kardex::create([
        'empresa_id' => $compra->empresa_id,
        'producto_id' => $producto_id,
        // ...
    ]);
}
```

**Tareas:**
- [ ] Revisar listener
- [ ] Asegurar que pasa empresa_id a creaciones

---

### UpdateInventarioVentaListener

**Debe hacer:**

```php
public function handle(UpdateInventarioVentaListener $event)
{
    // ✅ Usar disminuirStock()
    $inventario = Inventario::where('empresa_id', $event->venta->empresa_id)
        ->where('producto_id', $producto_id)
        ->first();
    
    $inventario->disminuirStock($cantidad);
}
```

**Tareas:**
- [ ] Revisar listener
- [ ] Reemplazar lógica manual por disminuirStock()
- [ ] Asegurar que filtra por empresa_id

---

### UpdateInventarioCompraListener

**Debe hacer:**

```php
public function handle(UpdateInventarioCompraListener $event)
{
    // ✅ Usar aumentarStock()
    $inventario = Inventario::where('empresa_id', $event->compra->empresa_id)
        ->where('producto_id', $producto_id)
        ->first();
    
    $inventario->aumentarStock($cantidad);
}
```

**Tareas:**
- [ ] Revisar listener
- [ ] Reemplazar lógica manual por aumentarStock()
- [ ] Asegurar que filtra por empresa_id

---

## 4. 🔐 MIDDLEWARE QUE NECESITA REVISIÓN

### Middleware de Autorización

**Si existe middleware que verifica empresa, debe actualizar:**

```php
// ✅ Verificar que solo accede a datos de su empresa
public function handle(Request $request, Closure $next)
{
    // Para modelos con global scope, esto es automático
    // Pero si hay middleware adicional, verificar:
    
    if ($request->route('venta')) {
        $venta = Venta::find($request->route('venta'));
        
        if ($venta->empresa_id !== auth()->user()->empresa_id) {
            abort(403, 'No tienes acceso a esta venta');
        }
    }
    
    return $next($request);
}
```

**Tareas:**
- [ ] Revisar si existe middleware de empresa
- [ ] Asegurar que valida empresa_id correctamente

---

## 5. 🧪 PLAN DE TESTING

### Unit Tests a Crear

```php
// tests/Unit/Models/VentaModelTest.php
public function test_venta_calcula_tarifa_correctamente()
{
    $venta = Venta::create([
        'subtotal' => 100,
        'tarifa_servicio' => 5,
        // ...
    ]);
    
    $this->assertEquals(5, $venta->monto_tarifa);
}

public function test_venta_pertenece_a_empresa()
{
    $empresa = Empresa::create(['nombre' => 'Test']);
    $venta = $empresa->ventas()->create([...]);
    
    $this->assertEquals($empresa->id, $venta->empresa_id);
}

public function test_global_scope_filtra_por_empresa()
{
    $empresa1 = Empresa::create(['nombre' => 'Empresa 1']);
    $empresa2 = Empresa::create(['nombre' => 'Empresa 2']);
    
    $empresa1->ventas()->create([...]);
    $empresa2->ventas()->create([...]);
    
    auth()->login($empresa1->users()->first()); // Login en empresa 1
    
    $ventas = Venta::all();
    $this->assertEquals(1, $ventas->count()); // Solo ve 1 venta
}
```

---

## 6. 📋 CHECKLIST DE IMPLEMENTACIÓN

- [ ] Revisar VentaObserver
- [ ] Revisar CajaObserver
- [ ] Revisar CompraObserver
- [ ] Revisar InventarioObserver
- [ ] Revisar VentaController
- [ ] Revisar CajaController
- [ ] Revisar CompraController
- [ ] Revisar InventarioController
- [ ] Revisar CreateVentaDetalleEvent listener
- [ ] Revisar CreateCompraDetalleEvent listener
- [ ] Revisar UpdateInventarioVentaListener
- [ ] Revisar UpdateInventarioCompraListener
- [ ] Revisar middleware de autorización
- [ ] Ejecutar tests unitarios
- [ ] Ejecutar tests de integración
- [ ] Testing manual en desarrollo
- [ ] Code review
- [ ] Deploy a staging
- [ ] Testing en staging
- [ ] Deploy a producción

---

## 7. ⏰ ESTIMADO DE TIEMPO

| Tarea | Estimado |
|-------|----------|
| Revisar/actualizar Observers | 45 min |
| Revisar/actualizar Controllers | 60 min |
| Revisar/actualizar Listeners | 30 min |
| Revisar/actualizar Middleware | 15 min |
| Crear tests | 60 min |
| Testing manual | 30 min |
| **Total** | **3-4 horas** |

---

## 8. 🚀 PRÓXIMO PASO

Una vez completadas estas actualizaciones:

1. Ejecutar test suite completa
2. Revisar logs en desarrollo
3. Verificar que no hay SQL errors
4. Testing end-to-end en desarrollo
5. Code review
6. Merge a rama principal
7. Deploying a staging
8. Final testing
9. Production release

---

**Documento creado:** 30 de enero de 2026  
**Status:** 📋 GUÍA LISTA PARA IMPLEMENTACIÓN  
**Prioridad:** ⚠️ MEDIA (Después de validar modelos)
