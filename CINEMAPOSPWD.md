# CinemaPOS - Plan de Reestructuración SaaS

**Documento de Análisis y Propuesta de Arquitectura**  
**Versión 1.0 - 30 de enero de 2026**

---

## 1. DIAGNÓSTICO DE LA ESTRUCTURA ACTUAL

### Estado Actual
El proyecto tiene una **estructura base parcialmente preparada** para multiempresa:
- ✅ Tabla `empresa` existente (singular: "empresa", no "empresas")
- ✅ Tabla `empleados` con relación a `users`
- ✅ Tabla `cajas` con `user_id` 
- ✅ Tabla `movimientos` con `caja_id`
- ✅ Tabla `ventas` actualizada con `caja_id`
- ❌ **Falta:** `empresa_id` en tablas clientes, proveedores, productos, compras, ventas, cajas, empleados
- ❌ **Falta:** Tarifa por servicio en las ventas
- ❌ **Falta:** Preparación para Stripe (campos de integración)
- ❌ **Falta:** Relación correcta `users` → `empresa`

### Migraciones Actuales (Análisis Detallado)

| Migración | Estado | Acción | Notas |
|-----------|--------|--------|-------|
| `2014_10_12_000000_create_users_table` | ✅ Existente | MANTENER | Base de autenticación Laravel |
| `2014_10_12_100000_create_password_resets_table` | ✅ Existente | MANTENER | Estándar Laravel |
| `2019_08_19_000000_create_failed_jobs_table` | ✅ Existente | MANTENER | Para jobs asincronos |
| `2019_12_14_000001_create_personal_access_tokens_table` | ✅ Existente | MANTENER | Para Sanctum API tokens |
| `2023_03_10_011515_create_documentos_table` | ✅ Existente | MANTENER | Tipos de comprobante (Boleta, Factura, etc) |
| `2023_03_10_012149_create_personas_table` | ✅ Existente | MANTENER | Base de clientes/proveedores |
| `2023_03_10_015030_create_proveedores_table` | ⚠️ Incompleto | **MODIFICAR** | Agregar `empresa_id` |
| `2023_03_10_015806_create_clientes_table` | ⚠️ Incompleto | **MODIFICAR** | Agregar `empresa_id` |
| `2023_03_10_020010_create_comprobantes_table` | ✅ Existente | MANTENER | Tipos de comprobante |
| `2023_03_10_020257_create_compras_table` | ⚠️ Incompleto | **MODIFICAR** | Agregar `empresa_id` |
| `2023_03_10_022517_create_ventas_table` | ⚠️ Incompleto | **MODIFICAR** | Agregar `empresa_id`, `tarifa_servicio`, `monto_tarifa` |
| `2023_03_10_023329_create_caracteristicas_table` | ✅ Existente | MANTENER | Características de productos |
| `2023_03_10_023555_create_categorias_table` | ✅ Existente | MANTENER | Categorías de productos |
| `2023_03_10_023818_create_marcas_table` | ✅ Existente | MANTENER | Marcas de productos |
| `2023_03_10_023953_create_presentaciones_table` | ✅ Existente | MANTENER | Presentaciones de productos |
| `2023_03_10_024112_create_productos_table` | ⚠️ Incompleto | **MODIFICAR** | Agregar `empresa_id` |
| `2023_03_10_025748_create_compra_producto_table` | ✅ Existente | MANTENER | Pivot tabla |
| `2023_03_10_030137_create_producto_venta_table` | ⚠️ Incompleto | **MODIFICAR** | Agregar `tarifa_unitaria` |
| `2025_01_22_114613_create_permission_tables` | ✅ Existente | MANTENER | Spatie permissions (roles/permisos) |
| `2025_01_23_113358_create_monedas_table` | ✅ Existente | MANTENER | Monedas del sistema |
| `2025_01_23_113626_create_empresas_table` | ✅ Existente | MANTENER | Tabla empresa (singular) |
| `2025_01_23_114215_create_empleados_table` | ⚠️ Incompleto | **MODIFICAR** | Agregar `empresa_id` |
| `2025_01_23_114438_update_columns_to_users_table` | ✅ Existente | **EXTENDER** | Agregar `empresa_id` para multi-tenancy |
| `2025_01_23_115036_create_cajas_table` | ⚠️ Incompleto | **MODIFICAR** | Agregar `empresa_id` |
| `2025_01_23_115425_create_movimientos_table` | ⚠️ Incompleto | **MODIFICAR** | Agregar `empresa_id`, vincular a venta_id |
| `2025_01_23_115923_update_columns_to_ventas_table` | ✅ Existente | MANTENER | Ya tiene `caja_id` |
| `2025_01_23_120147_create_ubicaciones_table` | ✅ Existente | MANTENER | Ubicaciones del negocio |
| `2025_01_23_121110_create_inventarios_table` | ⚠️ Incompleto | **MODIFICAR** | Agregar `empresa_id` |
| `2025_01_23_121449_create_kardexes_table` | ⚠️ Incompleto | **MODIFICAR** | Agregar `empresa_id` |
| `2025_02_03_102442_create_activity_logs_table` | ✅ Existente | MANTENER | Auditoría de cambios |
| `2025_05_20_213434_create_jobs_table` | ✅ Existente | MANTENER | Queue para jobs |
| `2025_05_24_210954_create_notifications_table` | ✅ Existente | MANTENER | Notificaciones del sistema |

---

## 2. PROPUESTA DE REESTRUCTURACIÓN

### 2.1 Fase 1: Agregar Multi-Tenancy Básica

#### Migración A: Actualizar `users` table
```
Agregar:
- empresa_id (nullable foreign key, inicialmente NULL)
```

**Por qué:** Multi-tenancy requiere que cada usuario pertenezca a una empresa. El campo es nullable durante la transición.

#### Migración B: Actualizar `empleados` table
```
Agregar:
- empresa_id (required foreign key)
```

**Por qué:** Los empleados son específicos de cada empresa en la confitería.

#### Migración C: Actualizar `cajas` table
```
Agregar:
- empresa_id (required foreign key)
```

**Por qué:** Cada caja pertenece a una empresa específica. Las cajas del cine 1 no son las mismas que las del cine 2.

#### Migración D: Actualizar `movimientos` table
```
Agregar:
- empresa_id (required foreign key)
- venta_id (nullable foreign key - vinculación directa a venta)
- tipo_movimiento (enum: 'VENTA', 'RETIRO', 'DEPOSITO', 'AJUSTE')
```

**Por qué:** Trazabilidad de movimientos por empresa. Vincular a venta permite auditoría.

#### Migración E: Actualizar `ventas` table
```
Agregar:
- empresa_id (required foreign key)
- tarifa_servicio (decimal 5,2 - porcentaje de tarifa)
- monto_tarifa (decimal 10,2 - monto calculado en base a subtotal)
- stripe_payment_intent_id (varchar nullable - para Stripe)
```

**Cambios:**
- `total` = subtotal + impuesto + monto_tarifa (mantener compatibilidad)

**Por qué:** Tarifa de servicio explícita en BD. Preparación para Stripe.

#### Migración F: Actualizar `productos` table
```
Agregar:
- empresa_id (required foreign key)
```

**Por qué:** Productos específicos de cada empresa (cada cine tiene su menú).

#### Migración G: Actualizar `compras` table
```
Agregar:
- empresa_id (required foreign key)
```

**Por qué:** Compras de suministros para cada empresa específica.

#### Migración H: Actualizar `clientes` table
```
Agregar:
- empresa_id (required foreign key)
```

**Por qué:** Clientes registrados por empresa (para programas de lealtad futuros).

#### Migración I: Actualizar `proveedores` table
```
Agregar:
- empresa_id (required foreign key)
```

**Por qué:** Proveedores pueden ser específicos de cada negocio.

#### Migración J: Actualizar `inventarios` table
```
Agregar:
- empresa_id (required foreign key)
```

**Por qué:** Inventarios separados por empresa.

#### Migración K: Actualizar `kardexes` table
```
Agregar:
- empresa_id (required foreign key)
```

**Por qué:** Kardex separado por empresa.

#### Migración L: Actualizar `producto_venta` table
```
Agregar:
- tarifa_unitaria (decimal 10,2 - tarifa aplicada al producto)
```

**Por qué:** Registro histórico de tarifa aplicada en cada venta.

---

### 2.2 Fase 2: Preparación para Stripe

#### Migración M: Nueva tabla `stripe_configs`
```sql
CREATE TABLE stripe_configs (
    id BIGINT PRIMARY KEY AUTO_INCREMENT
    empresa_id BIGINT FOREIGN KEY (referencia Empresa)
    public_key VARCHAR(255)
    secret_key VARCHAR(255) ENCRYPTED
    webhook_secret VARCHAR(255) ENCRYPTED
    test_mode BOOLEAN DEFAULT true
    enabled BOOLEAN DEFAULT false
    created_at TIMESTAMP
    updated_at TIMESTAMP
)
```

**Por qué:** Cada empresa puede tener su configuración de Stripe diferente.

#### Migración N: Nueva tabla `payment_transactions`
```sql
CREATE TABLE payment_transactions (
    id BIGINT PRIMARY KEY AUTO_INCREMENT
    venta_id BIGINT FOREIGN KEY (referencia Venta)
    empresa_id BIGINT FOREIGN KEY (referencia Empresa)
    payment_method VARCHAR(50) - 'CASH', 'CARD', 'STRIPE', 'OTHER'
    stripe_payment_intent_id VARCHAR(255) nullable
    stripe_charge_id VARCHAR(255) nullable
    amount_paid DECIMAL(10,2)
    currency VARCHAR(3) DEFAULT 'USD'
    status VARCHAR(50) - 'PENDING', 'SUCCESS', 'FAILED', 'REFUNDED'
    metadata JSON nullable
    created_at TIMESTAMP
    updated_at TIMESTAMP
)
```

**Por qué:** Registro detallado de cada transacción para auditoría y reportes.

---

### 2.3 Índices y Optimizaciones

Se recomienda agregar índices en:
- `users(empresa_id)`
- `empleados(empresa_id)`
- `cajas(empresa_id)`
- `movimientos(empresa_id)`
- `ventas(empresa_id)`
- `productos(empresa_id)`
- `compras(empresa_id)`
- `clientes(empresa_id)`
- `proveedores(empresa_id)`
- `inventarios(empresa_id)`
- `kardexes(empresa_id)`
- Índice compuesto: `movimientos(empresa_id, caja_id, created_at)` para reportes

---

## 3. CAMBIOS EN LOS MODELOS

### 3.1 Modelo `User`
```php
// Relación nueva
public function empresa(): BelongsTo {
    return $this->belongsTo(Empresa::class);
}

// Scope para filtrar por empresa actual (multi-tenancy)
public function scopeForEmpresa($query, $empresaId) {
    return $query->where('empresa_id', $empresaId);
}
```

### 3.2 Modelo `Venta`
```php
// Relaciones nuevas
public function empresa(): BelongsTo {
    return $this->belongsTo(Empresa::class);
}

public function movimientos(): HasMany {
    return $this->hasMany(Movimiento::class);
}

public function paymentTransaction(): HasOne {
    return $this->hasOne(PaymentTransaction::class);
}

// Accesor para total con tarifa
public function getTotalAttribute() {
    return $this->subtotal + $this->impuesto + $this->monto_tarifa;
}

// Método para calcular tarifa
public function calcularTarifa($tarifa_porcentaje) {
    $this->tarifa_servicio = $tarifa_porcentaje;
    $this->monto_tarifa = ($this->subtotal * $tarifa_porcentaje) / 100;
    return $this->monto_tarifa;
}
```

### 3.3 Modelo `Movimiento`
```php
// Relaciones nuevas
public function empresa(): BelongsTo {
    return $this->belongsTo(Empresa::class);
}

public function venta(): BelongsTo {
    return $this->belongsTo(Venta::class)->nullable();
}

// Scope
public function scopeForEmpresa($query, $empresaId) {
    return $query->where('empresa_id', $empresaId);
}
```

### 3.4 Modelo `Empleado`
```php
// Relación nueva
public function empresa(): BelongsTo {
    return $this->belongsTo(Empresa::class);
}

// Relación inversa
public function users(): HasMany {
    return $this->hasMany(User::class);
}
```

### 3.5 Modelo `Caja`
```php
// Relación nueva
public function empresa(): BelongsTo {
    return $this->belongsTo(Empresa::class);
}

// Scope
public function scopeForEmpresa($query, $empresaId) {
    return $query->where('empresa_id', $empresaId);
}
```

---

## 4. FLUJO DE VENTA POS ACTUALIZADO

```
1. Usuario Inicia Sesión
   └─> Sistema carga empresa_id desde User->empresa_id
   └─> Middleware verifica empresa_id en sesión

2. Cajero Abre Caja
   └─> Se crea registro en cajas (empresa_id, user_id, saldo_inicial)
   └─> Sistema anota fecha_hora_apertura

3. Cliente Compra Productos
   └─> Se crea venta (empresa_id, caja_id, user_id, cliente_id)
   └─> Sistema calcula:
       - subtotal (suma de productos x cantidad)
       - impuesto (según configuración empresa)
       - tarifa_servicio (%, configurable por empresa o por sesión)
       - monto_tarifa (subtotal * tarifa_servicio / 100)
       - total = subtotal + impuesto + monto_tarifa
   └─> Se registra en producto_venta (tarifa_unitaria)
   └─> Se crea movimiento (tipo: VENTA, monto: total, empresa_id)

4. Pago
   └─> Si EFECTIVO: registro en movimientos (metodo_pago: EFECTIVO)
   └─> Si TARJETA: 
       - Se prepara para Stripe (llena stripe_payment_intent_id)
       - Se crea payment_transaction
   └─> Vuelto se calcula y registra

5. Cierre de Caja
   └─> Se calcula saldo_final = saldo_inicial + todos_movimientos
   └─> Se registra fecha_hora_cierre
   └─> Se genera reporte de movimientos del día
```

---

## 5. CONFIGURACIÓN DE TARIFA POR SERVICIO

### Almacenamiento
- **Tabla:** `ventas` 
- **Campos:**
  - `tarifa_servicio` (DECIMAL 5,2) - porcentaje, ej: 3.50
  - `monto_tarifa` (DECIMAL 10,2) - monto calculado

### Cálculo
```php
$tarifa_porcentaje = 3.50; // % configurado
$subtotal = 100.00;
$monto_tarifa = ($subtotal * $tarifa_porcentaje) / 100;
// monto_tarifa = 3.50

$total = $subtotal + $impuesto + $monto_tarifa;
```

### Configuración por Empresa
Sugerencia: Agregar campo en tabla `empresa`:
```sql
ALTER TABLE empresa ADD tarifa_servicio_defecto DECIMAL(5,2) DEFAULT 3.00;
```

### Registro por Item
En `producto_venta`:
- `tarifa_unitaria` - tarifa aplicada a ese producto en esa venta

---

## 6. PREPARACIÓN PARA STRIPE

### Qué está listo:
- ✅ Campo `stripe_payment_intent_id` en `ventas`
- ✅ Tabla `stripe_configs` para almacenar claves
- ✅ Tabla `payment_transactions` para registrar transacciones

### Qué NO está implementado (future):
- ❌ SDK de Stripe (se instala con composer en fase 2)
- ❌ Controladores para crear payment intent
- ❌ Webhooks de Stripe
- ❌ Lógica de captura de pagos
- ❌ Refunds

### Próximos pasos cuando se implemente Stripe:
1. Instalar: `composer require stripe/stripe-php`
2. Crear servicio: `StripePaymentService`
3. Implementar flujo:
   - Usuario selecciona pago con tarjeta
   - Backend crea PaymentIntent
   - Frontend muestra Stripe Elements
   - Usuario completa pago
   - Webhook confirma y marca venta como pagada
4. Manejo de errores y reintentos

---

## 7. LISTA DE MIGRACIONES A CREAR/MODIFICAR

### Migraciones a CREAR (nuevas):

1. **`YYYYMMDD_HHMISS_add_empresa_id_to_users_table.php`**
   - Agregar `empresa_id` nullable

2. **`YYYYMMDD_HHMISS_add_empresa_id_to_empleados_table.php`**
   - Agregar `empresa_id` required

3. **`YYYYMMDD_HHMISS_add_empresa_id_to_cajas_table.php`**
   - Agregar `empresa_id` required

4. **`YYYYMMDD_HHMISS_add_empresa_id_to_movimientos_table.php`**
   - Agregar `empresa_id` required
   - Agregar `venta_id` nullable

5. **`YYYYMMDD_HHMISS_add_fields_to_ventas_table.php`**
   - Agregar `empresa_id` required
   - Agregar `tarifa_servicio` decimal
   - Agregar `monto_tarifa` decimal
   - Agregar `stripe_payment_intent_id` nullable

6. **`YYYYMMDD_HHMISS_add_empresa_id_to_productos_table.php`**
   - Agregar `empresa_id` required

7. **`YYYYMMDD_HHMISS_add_empresa_id_to_compras_table.php`**
   - Agregar `empresa_id` required

8. **`YYYYMMDD_HHMISS_add_empresa_id_to_clientes_table.php`**
   - Agregar `empresa_id` required

9. **`YYYYMMDD_HHMISS_add_empresa_id_to_proveedores_table.php`**
   - Agregar `empresa_id` required

10. **`YYYYMMDD_HHMISS_add_empresa_id_to_inventarios_table.php`**
    - Agregar `empresa_id` required

11. **`YYYYMMDD_HHMISS_add_empresa_id_to_kardexes_table.php`**
    - Agregar `empresa_id` required

12. **`YYYYMMDD_HHMISS_add_tarifa_unitaria_to_producto_venta_table.php`**
    - Agregar `tarifa_unitaria` decimal

13. **`YYYYMMDD_HHMISS_create_stripe_configs_table.php`**
    - Nueva tabla para configuración Stripe

14. **`YYYYMMDD_HHMISS_create_payment_transactions_table.php`**
    - Nueva tabla para transacciones de pago

---

## 8. MATRIZ DE COMPATIBILIDAD

| Cambio | Data Antigua | Data Nueva | Riesgo | Mitigación |
|--------|-------------|------------|--------|-----------|
| Agregar `empresa_id` (nullable) a users | - | 1 por defecto | BAJO | Asignación inicial en seeder |
| Agregar `empresa_id` (required) a otros | - | Error si NULL | ALTO | Migración con backfill a empresa_id=1 |
| Tarifa en ventas | Implícita en total | Explícita en monto_tarifa | BAJO | Total sigue siendo igual |
| Stripe fields | - | NULL | BAJO | No afecta flujo actual |

### Estrategia de Backfill:
```php
// En migración:
DB::statement('
    UPDATE users SET empresa_id = 1 WHERE empresa_id IS NULL
');

// Lo mismo para otras tablas
```

---

## 9. VENTAJAS DEL DISEÑO PROPUESTO

### Multi-Tenancy
- ✅ Cada empresa completamente aislada
- ✅ Queries automáticas filtradas por empresa_id
- ✅ Escalable a N empresas sin cambios código

### Tarifa de Servicio
- ✅ Explícita en BD (no cálculos en memoria)
- ✅ Auditable (qué tarifa se aplicó en cada venta)
- ✅ Configurable por empresa y por transacción

### Preparado para Stripe
- ✅ Campos listos pero no implementados
- ✅ No rompe flujo actual (EFECTIVO sigue funcionando)
- ✅ Transacciones registradas para auditoría

### Compatibilidad
- ✅ Datos históricos se mantienen
- ✅ Estructuras antiguas no se eliminar
- ✅ Migraciones reversibles

---

## 10. DIAGRAMA ER ACTUALIZADO

```
USUARIOS
├─ User
│  ├─ PK: id
│  ├─ FK: empresa_id
│  └─ FK: empleado_id
│
├─ Empresa
│  ├─ PK: id
│  ├─ nombre, ruc, etc.
│  └─ FK: moneda_id
│
└─ Empleado
   ├─ PK: id
   └─ FK: empresa_id

CAJA Y MOVIMIENTOS
├─ Caja
│  ├─ PK: id
│  ├─ FK: empresa_id
│  └─ FK: user_id
│
└─ Movimiento
   ├─ PK: id
   ├─ FK: empresa_id
   ├─ FK: caja_id
   └─ FK: venta_id (nullable)

VENTAS
├─ Venta
│  ├─ PK: id
│  ├─ FK: empresa_id
│  ├─ FK: caja_id
│  ├─ FK: user_id
│  ├─ FK: cliente_id
│  ├─ tarifa_servicio
│  ├─ monto_tarifa
│  └─ stripe_payment_intent_id
│
├─ ProductoVenta (pivot)
│  ├─ PK: id
│  ├─ FK: venta_id
│  ├─ FK: producto_id
│  └─ tarifa_unitaria
│
└─ PaymentTransaction
   ├─ PK: id
   ├─ FK: venta_id
   ├─ FK: empresa_id
   ├─ stripe_payment_intent_id
   └─ stripe_charge_id

INVENTARIO
├─ Producto
│  ├─ PK: id
│  └─ FK: empresa_id
│
├─ Inventario
│  ├─ PK: id
│  └─ FK: empresa_id
│
└─ Kardex
   ├─ PK: id
   └─ FK: empresa_id

COMPRAS
├─ Compra
│  ├─ PK: id
│  └─ FK: empresa_id
│
└─ CompraProducto (pivot)
   └─ similar a ProductoVenta
```

---

## 11. DECISIONES ARQUITECTÓNICAS

### 1. Por qué `empresa_id` en lugar de `tenant_id`
- **Razón:** Modelo específico del dominio POS/confitería
- **Alternativa rechazada:** `tenant_id` es más genérico pero menos legible en contexto

### 2. Por qué `tarifa_servicio` + `monto_tarifa` en `ventas`
- **Razón:** Facilita reportes (total = subtotal + impuesto + monto_tarifa)
- **Alternativa rechazada:** Calcular sobre la marcha causa inconsistencias en auditoría

### 3. Por qué tabla `payment_transactions` separada
- **Razón:** Desacoplamiento de datos de venta de datos de pago
- **Beneficio:** Múltiples pagos por venta (split payments)
- **Futuro:** Soporte para reembolsos parciales

### 4. Por qué `stripe_payment_intent_id` en `ventas`
- **Razón:** Referencia directa para auditoría rápida
- **Alternativa:** Solo en `payment_transactions`
- **Decisión:** Ambos (redundancia intencional para queries eficientes)

### 5. Por qué `venta_id` en `movimientos`
- **Razón:** Trazabilidad completa: venta → movimiento → caja
- **Beneficio:** Reportes de reconciliación sin joins complejos

---

## 12. PRÓXIMOS PASOS

### Fase 1: Implementar (INMEDIATO)
- [ ] Crear todas las migraciones listadas en Sección 7
- [ ] Actualizar modelos con relaciones
- [ ] Agregar scopes de multi-tenancy
- [ ] Crear middleware para `empresa_id` en requests
- [ ] Implementar seeders

### Fase 2: Testing (SEMANA 2)
- [ ] Tests unitarios de modelos
- [ ] Tests de migraciones (rollback/forward)
- [ ] Tests de cálculo de tarifa
- [ ] Tests de multi-tenancy

### Fase 3: Integración Stripe (DESPUÉS DE FASE 1)
- [ ] Crear servicio `StripePaymentService`
- [ ] Implementar endpoints de pago
- [ ] Webhooks de Stripe
- [ ] Tests de transacciones

---

## 13. ROLLBACK PLAN

Si algo falla:
```bash
php artisan migrate:rollback --step=14
# Borra todas las nuevas migraciones
```

Cada migración es reversible (down() implementado).

---

**Documento Preparado por:** Arquitecto Senior SaaS/POS  
**Fecha:** 30 de enero de 2026  
**Estado:** Listo para Implementación  
**Siguiente Revisión:** Después de Fase 1
