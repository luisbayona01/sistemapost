# FASE 4: Auditoría de Estabilidad POS - CinemaPOS

**Fecha:** 30/01/2026  
**Estado:** AUDITORÍA INICIAL COMPLETADA  
**Objetivo:** Identificar riesgos críticos antes de producción  
**Enfoque:** Estabilidad, seguridad multiempresa, manejo de errores

---

## 📊 RESUMEN EJECUTIVO

### Arquitectura Actual
- ✅ UI: 100% migrada a Tailwind CSS
- ✅ Multiempresa: Implementado con `global scope` en modelos
- ✅ Caja: Sistema completo (apertura → movimientos → cierre)
- ✅ Ventas: Vinculada a caja + tarifa de servicio
- ✅ DB Transacciones: Parcialmente implementadas en venta/store
- ⚠️ Validaciones defensivas: INCOMPLETAS
- ⚠️ Null pointer checks: FALTANTES EN LISTENERS
- ⚠️ Middleware de empresa: NO VALIDADO SIEMPRE

### Riesgos Detectados: **7 CRÍTICOS**

---

## 🔴 RIESGOS CRÍTICOS IDENTIFICADOS

### 1. **Listener CreateMovimientoVentaCajaListener - NULL POINTER**

**Archivo:** `app/Listeners/CreateMovimientoVentaCajaListener.php` (línea 24)

```php
$caja_id = Caja::where('user_id', Auth::id())->where('estado', 1)->first()->id;
```

**Riesgo:** `.first()` puede retornar `null` si no hay caja abierta
- **Impacto:** CRASH si se dispara evento sin caja abierta
- **Escenario:** Race condition entre crear venta y cierre simultáneo de caja
- **Severity:** 🔴 CRÍTICO

**Solución Defensiva:**
```php
$caja = Caja::where('user_id', Auth::id())->where('estado', 1)->first();
if (!$caja) {
    Log::warning('Evento venta sin caja abierta', ['user_id' => Auth::id()]);
    return;
}
```

---

### 2. **UpdateInventarioVentaListener - SIN VALIDAR INVENTARIO EXISTENTE**

**Archivo:** `app/Listeners/UpdateInventarioVentaListener.php` (línea 19)

```php
$registro = Inventario::where('producto_id', $event->producto_id)->first();
$registro->update(['cantidad' => ($registro->cantidad - $event->cantidad)]);
```

**Riesgos:**
- `$registro` puede ser `null` → NULL POINTER
- No valida si cantidad suficiente
- Sin transacción DB → stock negativo posible
- Sin bloqueo → race condition multihilo

**Impacto:**
- Stock negativo en BD
- Doble venta del mismo producto
- Reportes de inventario incorrectos

**Severity:** 🔴 CRÍTICO

---

### 3. **VentaObsever - FALTA NULL CHECK EN CAJA**

**Archivo:** `app/Observers/VentaObsever.php` (línea 18)

```php
public function creating(Venta $venta): void
{
    $caja = Caja::where('user_id', Auth::id())->where('estado', 1)->first();
    // ...
    $venta->caja_id = $caja->id;  // ← PUEDE SER NULL
}
```

**Riesgo:** Si `$caja` es null, error en línea 21  
**Severity:** 🔴 CRÍTICO

---

### 4. **CheckCajaAperturadaUser Middleware - NO FILTRA POR EMPRESA**

**Archivo:** `app/Http/Middleware/CheckCajaAperturadaUser.php` (línea 15)

```php
$existe = Caja::where('user_id', Auth::id())->where('estado', 1)->exists();
```

**Problema:** No valida `empresa_id`
- Usuario A puede crear venta con caja de usuario A de OTRA empresa
- Global scope de Caja + middleware = redundancia débil

**Escenario de explotación:**
```
1. Usuario A abre caja en Empresa 1
2. Cambia empresa a Empresa 2
3. Middleware OK (caja existe globalmente)
4. Venta se crea en Empresa 2 con caja de Empresa 1
5. Datos contaminados 🔥
```

**Severity:** 🔴 CRÍTICO (Fuga de datos multiempresa)

---

### 5. **CheckMovimientoCajaUserMiddleware - FALTA VALIDAR EMPRESA**

**Archivo:** `app/Http/Middleware/CheckMovimientoCajaUserMiddleware.php` (línea 18)

```php
$caja = Caja::findOrfail($request->caja_id);
if ($caja->user_id != Auth::id()) {  // ← NO VALIDA EMPRESA
    throw new HttpException(401, 'No autorizado');
}
```

**Riesgo:** Mismo que #4 - usuario de otra empresa accede a caja

**Severity:** 🔴 CRÍTICO

---

### 6. **CheckShowVentaUser Middleware - NO VALIDA EMPRESA**

**Archivo:** `app/Http/Middleware/CheckShowVentaUser.php` (línea 14)

```php
if ($venta->user_id != Auth::id()) {
    throw new HttpException(401, 'No autorizado');
}
```

**Problema:** Solo valida user, no empresa  
**Escenario:** Usuario de otra empresa ve venta ajena

**Severity:** 🔴 CRÍTICO (Leak de datos financieros)

---

### 7. **VentaController::store - DUPLICACIÓN DE LÓGICA MOVIMIENTO**

**Archivo:** `app/Http/Controllers/ventaController.php` (líneas 140-157)

```php
// Línea 148-157: Crea movimiento MANUALMENTE
Movimiento::create([...]);

// Línea 168: Dispara evento que INTENTA crear otro movimiento
CreateVentaEvent::dispatch($venta);
```

**Listener `CreateMovimientoVentaCajaListener` también crea movimiento**

**Riesgos:**
- ✅ Movimiento creado en controller (línea 148)
- ✅ Listener intenta crear OTRO (línea 30)
- Resultado: 2 movimientos por venta (¡DUPLICADOS!)
- Saldo de caja INCORRECTO

**Impacto:** Reportes contables 100% erróneos

**Severity:** 🔴 CRÍTICO

---

## ⚠️ RIESGOS SECUNDARIOS

### 8. **CajaObserver::updating - SIN VALIDAR ESTADO**

**Archivo:** `app/Observers/CajaObserver.php` (línea 33)

```sql
SELECT SUM(CASE WHEN tipo = 'VENTA' ...) FROM movimientos
WHERE caja_id = ?
```

**Problema:**
- Si no hay movimientos: `NULL` retornado
- `$caja->saldo_final = $caja->saldo_inicial + ($movimientos->total_venta ?? 0)`
- `?? 0` lo cubre, PERO si hay error de query → excepción no capturada

**Severity:** 🟡 ALTO

---

### 9. **Listener UpdateInventarioVentaListener - SIN ROLLBACK**

**Contexto:** CreateVentaDetalleEvent se dispara en LOOP (línea 125-133)

```php
for ($i = 0; $i < $siseArray; $i++) {
    // Crea detalle
    $venta->productos()->syncWithoutDetaching([...]);
    
    // Dispara evento para ACTUALIZAR INVENTARIO
    CreateVentaDetalleEvent::dispatch(...);
}
```

**Riesgo:** Si listener #2 falla
- Detalle 1-3: inventario ACTUALIZADO
- Detalle 4: ERROR
- Detalle 5: NUNCA se procesa
- **Inventario parcialmente descontado** ❌

**Severity:** 🟡 ALTO

---

### 10. **Falta de Logging en Puntos Críticos**

- ✓ CajaController: Log en try/catch
- ✗ VentaController: Log SOLO en catch (no en cierre exitoso)
- ✗ MovimientoController: SIN logging de validaciones
- ✗ Listener de inventario: CERO logging

**Impacto:** Auditoría débil, imposible trackear problemas

**Severity:** 🟡 ALTO

---

## ✅ PUNTOS FUERTES DETECTADOS

1. ✅ **Global Scope de Empresa:** Bien implementado en Venta, Caja, Movimiento
2. ✅ **DB::beginTransaction() en venta/store:** Protege creación de venta
3. ✅ **Try/Catch en CajaController:** Manejo de excepciones básico
4. ✅ **Middleware de autorización:** Estructura presente (aunque incompleta)
5. ✅ **Observers para lógica de negocio:** Patrón correcto

---

## 📋 SMOKE TESTING CHECKLIST - CASOS CRÍTICOS

### Test 1: Crear Caja
```
PRE: Usuario logueado en Empresa A
STEPS:
  1. POST /admin/cajas (saldo_inicial = 100)
  2. Verificar Caja creada con empresa_id = auth()->user()->empresa_id
  3. Verificar estado = 'abierta'
EXPECTED:
  ✓ Caja visible en index
  ✓ Movimientos.index disponible
  ✓ Crear venta HABILITADO
FAIL: Usuario puede crear venta sin caja
```

### Test 2: Bloquear Venta Sin Caja
```
PRE: Usuario sin caja abierta
STEPS:
  1. GET /admin/ventas/create
  2. Middleware CheckCajaAperturadaUser activa
EXPECTED:
  ✓ Redirige a /admin/cajas
  ✓ Mensaje: "Debe aperturar una caja"
FAIL: Accede a formulario de venta sin caja
```

### Test 3: Venta Con Caja Abierta - Movimiento Creado UNA VEZ
```
PRE: Caja abierta con saldo_inicial = 1000
STEPS:
  1. POST /admin/ventas/store (total = 150)
  2. Verificar movimientos COUNT
EXPECTED:
  ✓ Movimientos count = 1
  ✓ Movimiento.monto = 150
  ✓ Caja.saldo_final = 1150
FAIL: 2 movimientos creados (DUPLICADO)
FAIL: Saldo incorrecto
```

### Test 4: Aislamiento por Empresa
```
PRE:
  - Usuario A logueado en Empresa 1
  - Usuario B logueado en Empresa 2
  - Ambos abrieron caja
STEPS:
  1. User A: POST venta (total = 100) en Empresa 1
  2. User B: GET /admin/ventas (index)
EXPECTED:
  ✓ User B solo ve sus ventas (0)
  ✓ User A solo ve venta propia (1)
FAIL: User B ve venta de User A
```

### Test 5: Cierre de Caja
```
PRE: Caja con 3 movimientos (VENTA 50, VENTA 75, RETIRO 20)
STEPS:
  1. PUT /admin/cajas/{id} (estado = 'cerrada')
  2. Verificar saldo_final
EXPECTED:
  ✓ saldo_final = saldo_inicial + (50 + 75) - 20
FAIL: Cálculo incorrecto
```

### Test 6: Inventario Descontado Una Vez
```
PRE: Producto con inventario = 100
STEPS:
  1. Crear venta con 30 unidades del producto
  2. Verificar inventario COUNT
EXPECTED:
  ✓ inventario.cantidad = 70
  ✓ Kardex registro creado (SALIDA: 30)
FAIL: inventario = 40 (descontado 2x)
```

### Test 7: Validar Empresa en Middleware
```
PRE:
  - Caja ID=1 pertenece a Empresa A, User X
  - User X logueado en Empresa B
STEPS:
  1. GET /admin/movimientos (caja_id=1)
EXPECTED:
  ✓ Redirige 403 o error
FAIL: Accede a movimientos de otra empresa
```

---

## 🛠️ CAMBIOS RECOMENDADOS (Orden de Prioridad)

### PRIORIDAD 1️⃣ - BLOQUEADORES
1. **Listener null checks** - Prevenir CRASH
2. **Eliminar duplicación movimiento** - Contabilidad correcta
3. **Validar empresa en middleware** - Seguridad multiempresa
4. **Inventario transacción** - Stock consistente

### PRIORIDAD 2️⃣ - ESTABILIDAD
5. Logging mejorado - Auditoría
6. Mensaje UX claro - Experiencia

### PRIORIDAD 3️⃣ - OPTIMIZACIÓN
7. Índices DB - Performance
8. Caché de empresa - Velocidad

---

## 📝 IMPLEMENTACIÓN SIGUIENTE

1. **Tests Feature** → Validar casos críticos
2. **Cambios mínimos** → Solo lo crítico
3. **Validación completa** → `php -l` + tests
4. **Documentación** → Changelog detallado

