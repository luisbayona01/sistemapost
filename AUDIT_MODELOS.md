# 📋 AUDIT DE MODELOS - CinemaPOS SaaS

**Fecha:** 30 de enero de 2026  
**Objetivo:** Validar consistencia modelo ↔ migración y corregir deficiencias

---

## ✅ VALIDACIÓN DE MIGRACIONES vs MODELOS

### 1. User Model

**Campos en BD (de migraciones):**
- id, name, email, password, remember_token, email_verified_at, timestamps
- estado (from 2025_01_23_114438)
- empleado_id (from 2025_01_23_114438)
- **empresa_id (from 2026_01_30_114320 - NEW)**

**Estado Actual del Modelo:**
```php
protected $fillable = ['name', 'email', 'password', 'estado', 'empleado_id'];
```

**PROBLEMAS IDENTIFICADOS:**
❌ Falta `empresa_id` en fillable
❌ Falta relación `empresa()` BelongsTo
❌ Falta global scope para filtrar por empresa
❌ No hay casts para empresa_id

**RECOMENDACIÓN:** ✅ ACTUALIZAR

---

### 2. Venta Model

**Campos en BD (de migraciones):**
- id, cliente_id, user_id, comprobante_id, numero_comprobante, metodo_pago
- fecha_hora, subtotal, impuesto, total, monto_recibido, vuelto_entregado
- caja_id (from 2025_01_23_115923)
- **empresa_id (from 2026_01_30_114340 - NEW)**
- **tarifa_servicio (from 2026_01_30_114340 - NEW)**
- **monto_tarifa (from 2026_01_30_114340 - NEW)**
- **stripe_payment_intent_id (from 2026_01_30_114340 - NEW)**
- timestamps

**Estado Actual del Modelo:**
```php
protected $guarded = ['id'];
// Relaciones: caja, cliente, user, comprobante, productos
// NO tiene: empresa, paymentTransactions, movimientos
```

**PROBLEMAS IDENTIFICADOS:**
❌ Falta relación `empresa()` BelongsTo
❌ Falta relación `paymentTransactions()` HasMany
❌ Falta relación `movimientos()` HasMany (venta_id in movimientos)
❌ No hay global scope para empresa
❌ Falta método `calcularTarifa()`
❌ Falta accessor para total con tarifa
❌ No hay casts para tarifa y stripe fields
❌ Falta con `pivot('tarifa_unitaria')` en productos

**RECOMENDACIÓN:** ✅ ACTUALIZAR COMPLETAMENTE

---

### 3. Caja Model

**Campos en BD (de migraciones):**
- id, nombre, fecha_hora_apertura, fecha_hora_cierre
- saldo_inicial, saldo_final, estado, user_id
- **empresa_id (from 2026_01_30_114330 - NEW)**
- timestamps

**Estado Actual del Modelo:**
```php
protected $guarded = ['id'];
// Relaciones: user, movimientos, ventas
// NO tiene: empresa
// NO tiene scopes
```

**PROBLEMAS IDENTIFICADOS:**
❌ Falta relación `empresa()` BelongsTo
❌ Falta global scope para filtrar por empresa
❌ No hay scopes abierta(), cerrada(), forEmpresa()

**RECOMENDACIÓN:** ✅ ACTUALIZAR

---

### 4. Movimiento Model

**Campos en BD (de migraciones):**
- id, tipo, descripcion, monto, metodo_pago, caja_id, timestamps
- **empresa_id (from 2026_01_30_114335 - NEW)**
- **venta_id (from 2026_01_30_114335 - NEW, nullable)**

**Estado Actual del Modelo:**
```php
protected $guarded = ['id'];
protected $casts = ['tipo' => TipoMovimientoEnum::class, 'metodo_pago' => MetodoPagoEnum::class];
// Relación: caja
// NO tiene: empresa, venta
// NO tiene scopes
```

**PROBLEMAS IDENTIFICADOS:**
❌ Falta relación `empresa()` BelongsTo
❌ Falta relación `venta()` BelongsTo nullable
❌ Falta global scope para filtrar por empresa
❌ No hay scopes tipo(), enPeriodo()

**RECOMENDACIÓN:** ✅ ACTUALIZAR

---

### 5. Empresa Model

**Campos en BD:**
- id, nombre, propietario, ruc, porcentaje_impuesto, abreviatura_impuesto
- direccion, correo, telefono, ubicacion
- moneda_id, timestamps
- (Podría tener tarifa_servicio_defecto - NO EN MIGRACIÓN ACTUAL)

**Estado Actual del Modelo:**
```php
protected $guarded = ['id'];
protected $table = 'empresa';
// Relación: moneda
// NO tiene: users, empleados, cajas, ventas, etc.
```

**PROBLEMAS IDENTIFICADOS:**
❌ Falta relaciones inversas: users(), empleados(), cajas(), ventas(), etc.

**RECOMENDACIÓN:** ✅ ACTUALIZAR

---

### 6. Empleado Model

**Campos en BD (de migraciones):**
- id, razon_social, cargo, img_path, timestamps
- **empresa_id (from 2026_01_30_114325 - NEW)**

**Estado Actual del Modelo:**
```php
protected $guarded = ['id'];
// Relación: user (HasOne)
// NO tiene: empresa
```

**PROBLEMAS IDENTIFICADOS:**
❌ Falta relación `empresa()` BelongsTo
❌ user() debería ser HasMany (múltiples usuarios por empleado)

**RECOMENDACIÓN:** ✅ ACTUALIZAR

---

### 7. Producto Model

**Campos en BD (de migraciones):**
- id, codigo, nombre, descripcion, img_path, estado
- precio, marca_id, presentacione_id, categoria_id
- **empresa_id (from 2026_01_30_114345 - NEW)**
- timestamps

**Estado Actual del Modelo:**
```php
protected $guarded = ['id'];
// Relaciones: compras, ventas, categoria, marca, presentacione, inventario
// NO tiene: empresa
// ventas BelongsToMany pero necesita 'tarifa_unitaria' en pivot
```

**PROBLEMAS IDENTIFICADOS:**
❌ Falta relación `empresa()` BelongsTo
❌ Falta global scope para filtrar por empresa
❌ Falta actualizar pivot en `ventas()` para incluir `tarifa_unitaria`
❌ No hay scope activos()

**RECOMENDACIÓN:** ✅ ACTUALIZAR

---

### 8. Cliente Model

**Campos en BD (de migraciones):**
- id, persona_id
- **empresa_id (from 2026_01_30_114355 - NEW)**
- timestamps

**Estado Actual del Modelo:**
```php
protected $fillable = ['persona_id'];
// Relaciones: persona (BelongsTo), ventas (HasMany)
// NO tiene: empresa
```

**PROBLEMAS IDENTIFICADOS:**
❌ Falta relación `empresa()` BelongsTo
❌ Falta actualizar fillable para incluir empresa_id
❌ Falta global scope para filtrar por empresa

**RECOMENDACIÓN:** ✅ ACTUALIZAR

---

### 9. Compra Model

**Campos en BD (de migraciones):**
- id, user_id, comprobante_id, proveedore_id
- numero_comprobante, comprobante_path, metodo_pago
- fecha_hora, impuesto, subtotal, total, timestamps
- **empresa_id (from 2026_01_30_114350 - NEW)**

**Status:** Falta revisar

**RECOMENDACIÓN:** ✅ REVISAR Y ACTUALIZAR

---

### 10. Proveedor Model

**Status:** Falta revisar (probablemente Proveedore)

---

### 11-14. Otros Modelos

- Inventario (falta empresa_id - from 2026_01_30_114405)
- Kardex (falta empresa_id - from 2026_01_30_114410)
- ProductoVenta (falta tarifa_unitaria - from 2026_01_30_114415)
- (NEW) StripeConfig - CREAR NUEVO MODELO
- (NEW) PaymentTransaction - CREAR NUEVO MODELO

---

## 📊 RESUMEN DE CAMBIOS NECESARIOS

### Modelos a ACTUALIZAR (12)

| Modelo | Cambios | Prioridad |
|--------|---------|-----------|
| User | +empresa_id fillable, +empresa() relation, +globalScope | 🔴 CRÍTICA |
| Venta | +empresa_id, +5 relaciones, +globalScope, +casts, +métodos | 🔴 CRÍTICA |
| Caja | +empresa() relation, +globalScope, +scopes | 🔴 CRÍTICA |
| Movimiento | +empresa(), +venta(), +globalScope, +scopes | 🔴 CRÍTICA |
| Empresa | +relaciones inversas (users, empleados, cajas, ventas) | 🟠 ALTA |
| Empleado | +empresa() relation, fix user() → HasMany | 🟠 ALTA |
| Producto | +empresa() relation, +globalScope, +actualizar pivot | 🟠 ALTA |
| Cliente | +empresa() relation, +empresa_id fillable, +globalScope | 🟠 ALTA |
| Compra | +empresa() relation, +globalScope | 🟠 ALTA |
| Proveedore | +empresa() relation, +globalScope | 🟠 ALTA |
| Inventario | +empresa() relation, +globalScope | 🟠 ALTA |
| Kardex | +empresa() relation, +globalScope | 🟠 ALTA |

### Modelos a CREAR (2)

| Modelo | Descripción | Prioridad |
|--------|-------------|-----------|
| StripeConfig | Config Stripe por empresa | 🟠 ALTA |
| PaymentTransaction | Transacciones de pago | 🟠 ALTA |

---

## 🔐 GLOBAL SCOPES A IMPLEMENTAR

Los siguientes modelos DEBEN tener Global Scope para filtrar automáticamente por empresa:

```php
protected static function booted(): void
{
    static::addGlobalScope('empresa', function ($query) {
        if (auth()->check() && auth()->user()->empresa_id) {
            $query->where('empresa_id', auth()->user()->empresa_id);
        }
    });
}
```

**Modelos que NECESITAN Global Scope:**
- User (conditional, solo si autenticado)
- Venta ✅
- Caja ✅
- Movimiento ✅
- Producto ✅
- Cliente ✅
- Compra ✅
- Proveedore ✅
- Inventario ✅
- Kardex ✅

---

## ✨ RELACIONES NUEVAS REQUERIDAS

### Venta
```php
public function empresa(): BelongsTo
public function paymentTransactions(): HasMany
public function movimientos(): HasMany
```

### Caja
```php
public function empresa(): BelongsTo
```

### Movimiento
```php
public function empresa(): BelongsTo
public function venta(): BelongsTo // nullable
```

### Empresa
```php
public function users(): HasMany
public function empleados(): HasMany
public function cajas(): HasMany
public function ventas(): HasMany
public function productos(): HasMany
public function compras(): HasMany
public function clientes(): HasMany
public function proveedores(): HasMany
public function inventarios(): HasMany
public function kardexes(): HasMany
public function stripeConfig(): HasOne // relación 1-a-1
```

### User
```php
public function empresa(): BelongsTo
```

### Empleado
```php
public function empresa(): BelongsTo
public function users(): HasMany // cambiar de HasOne
```

### Producto
```php
public function empresa(): BelongsTo
```

### Cliente
```php
public function empresa(): BelongsTo
```

### Compra
```php
public function empresa(): BelongsTo
```

### Proveedore
```php
public function empresa(): BelongsTo
```

### Inventario
```php
public function empresa(): BelongsTo
```

### Kardex
```php
public function empresa(): BelongsTo
```

---

## 🔧 CAMBIOS EN PIVOTS

### producto_venta (Venta::productos)
**Cambio requerido:** Agregar 'tarifa_unitaria' al withPivot

```php
public function productos(): BelongsToMany
{
    return $this->belongsToMany(Producto::class, 'producto_venta')
        ->withTimestamps()
        ->withPivot('cantidad', 'precio_venta', 'tarifa_unitaria'); // ✅ ADD tarifa_unitaria
}
```

---

## 📝 CASTS Y ACCESORES

### Venta
```php
protected $casts = [
    'tarifa_servicio' => 'decimal:2',
    'monto_tarifa' => 'decimal:2',
    'fecha_hora' => 'datetime',
];

// Accesor para total (incluye tarifa)
public function getTotalAttribute(): float
{
    return ($this->subtotal ?? 0) 
        + ($this->impuesto ?? 0) 
        + ($this->monto_tarifa ?? 0);
}

// Mutador para calcular tarifa
public function setTarifaServicioAttribute($value): void
{
    $this->attributes['tarifa_servicio'] = $value;
    if ($this->subtotal) {
        $this->attributes['monto_tarifa'] = ($this->subtotal * $value) / 100;
    }
}
```

---

## ✅ CHECKLIST DE VALIDACIÓN FINAL

- [ ] Todos los fillable incluyen empresa_id (excepto User que es miembro de auth)
- [ ] Todas las relaciones empresa() existen
- [ ] Global scopes implementados donde corresponde
- [ ] Pivots actualizados con tarifa_unitaria
- [ ] Casts correctos para decimales
- [ ] Métodos calcularTarifa() en Venta
- [ ] Relaciones inversas en Empresa
- [ ] StripeConfig y PaymentTransaction creados
- [ ] No hay breaking changes
- [ ] Tests pasen

---

**Status:** 📋 LISTO PARA IMPLEMENTACIÓN  
**Estimado:** 3-4 horas de trabajo  
**Riesgo:** BAJO (solo modelos, migraciones intactas)
