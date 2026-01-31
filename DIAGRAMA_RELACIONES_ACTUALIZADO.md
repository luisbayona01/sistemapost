# 📊 DIAGRAMA DE RELACIONES ACTUALIZADO

## Relaciones Completas post-Actualización

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                           EMPRESA (SaaS Root)                              │
│  ├─ id (PK)                                                                │
│  ├─ nombre                                                                 │
│  ├─ porcentaje_impuesto                                                    │
│  └─ moneda_id (FK)                                                         │
└─────────────────────────────────────────────────────────────────────────────┘
         │ 1
         │
         ├── * Users ────────────┐
         │                       │
         ├── * Empleados ────────┤
         │                       │
         ├── * Cajas────────────┐│
         │    ├─ empresa_id      ││
         │    ├─ user_id (FK)    ││
         │    └─ estado          ││
         │         │ 1           ││
         │         │             ││
         │         └── * Movimientos ──────────────────────────┐
         │              ├─ empresa_id                          │
         │              ├─ caja_id (FK)                        │
         │              ├─ venta_id (FK nullable)              │
         │              ├─ tipo (enum)                         │
         │              └─ metodo_pago (enum)                  │
         │                                                      │
         ├── * Ventas ──────────────────────────┐              │
         │    ├─ empresa_id                     │              │
         │    ├─ caja_id (FK)                   │              │
         │    ├─ cliente_id (FK)                │              │
         │    ├─ user_id (FK)                   │              │
         │    ├─ tarifa_servicio (%)            │              │
         │    ├─ monto_tarifa (decimal)         │              │
         │    ├─ stripe_payment_intent_id       │              │
         │    └─ estado                         │              │
         │         │ N                          │              │
         │         │                            │              │
         │         ├── M2M Productos ────────┐  │              │
         │         │    └─ tarifa_unitaria    │  │              │
         │         │                          │  │              │
         │         └── * PaymentTransactions ─┼──┘              │
         │              ├─ empresa_id         │
         │              ├─ venta_id (FK)      │
         │              ├─ payment_method     │
         │              ├─ status (enum)      │
         │              ├─ stripe_charge_id   │
         │              └─ amount_paid        │
         │                                    │
         ├── * Compras ──────────────────────┤
         │    ├─ empresa_id                   │
         │    ├─ user_id (FK)                 │
         │    ├─ proveedore_id (FK)           │
         │    └─ estado                       │
         │         │ N                        │
         │         └── M2M Productos ─────────┘
         │              └─ precio_compra
         │                 fecha_vencimiento
         │
         ├── * Productos
         │    ├─ empresa_id
         │    ├─ categoria_id (FK)
         │    ├─ marca_id (FK)
         │    ├─ precio
         │    └─ estado
         │         │ 1
         │         │
         │         ├── Inventario ─────────────────┐
         │         │    ├─ empresa_id              │
         │         │    ├─ ubicacione_id (FK)      │
         │         │    ├─ cantidad                │
         │         │    ├─ stock_minimo            │
         │         │    └─ fecha_vencimiento       │
         │         │                               │
         │         └── * Kardex ────────────────────┘
         │              ├─ empresa_id
         │              ├─ tipo_transaccion (enum)
         │              ├─ entrada/salida/saldo
         │              └─ costo_unitario
         │
         ├── * Clientes
         │    ├─ empresa_id
         │    └─ persona_id (FK)
         │         │ N
         │         └── Ventas
         │
         ├── * Proveedores
         │    ├─ empresa_id
         │    └─ persona_id (FK)
         │         │ N
         │         └── Compras
         │
         └── StripeConfig (1-a-1)
              ├─ empresa_id (UNIQUE)
              ├─ public_key
              ├─ secret_key (encrypted)
              ├─ webhook_secret (encrypted)
              └─ test_mode
```

---

## Tabla de Relaciones Detallada

### User Model
```
User (1) ───── empresa_id ────────── (N) Empresa
       (1) ───── empleado_id ─────── (1) Empleado
       (1) ────── user_id ─────────── (N) Venta
       (1) ────── user_id ─────────── (N) Compra
       (1) ────── user_id ─────────── (N) Caja
```

### Empresa Model (HUB CENTRAL)
```
Empresa (1) ─── (N) User
        (1) ─── (N) Empleado
        (1) ─── (N) Caja
        (1) ─── (N) Venta
        (1) ─── (N) Compra
        (1) ─── (N) Producto
        (1) ─── (N) Cliente
        (1) ─── (N) Proveedore
        (1) ─── (N) Movimiento
        (1) ─── (N) Inventario
        (1) ─── (N) Kardex
        (1) ─── (N) PaymentTransaction
        (1) ─── (1) StripeConfig
```

### Venta Model (Core Transacción)
```
Venta (N) ────── empresa_id ──────── (1) Empresa
      (N) ────── caja_id ─────────── (1) Caja
      (N) ────── cliente_id ──────── (1) Cliente
      (N) ────── user_id ─────────── (1) User
      (N) ────── comprobante_id ──── (1) Comprobante
      (N) ── M2M ─── (N) Producto (con tarifa_unitaria)
      (1) ────── (N) PaymentTransaction
      (1) ────── (N) Movimiento
```

### Movimiento Model (Auditoría Caja)
```
Movimiento (N) ──── empresa_id ──── (1) Empresa
          (N) ──── caja_id ──────── (1) Caja
          (N) ──── venta_id (nullable) ── (1) Venta
```

### Producto Model
```
Producto (N) ───── empresa_id ──── (1) Empresa
        (N) ───── categoria_id ─── (1) Categoria
        (N) ───── marca_id ────── (1) Marca
        (N) ───── presentacione_id (1) Presentacione
        (1) ───── (1) Inventario
        (1) ───── (N) Kardex
        (N) ── M2M ── (N) Venta
        (N) ── M2M ── (N) Compra
```

### Caja Model (Register)
```
Caja (N) ────── empresa_id ──── (1) Empresa
     (N) ────── user_id ──────── (1) User
     (1) ────── (N) Movimiento
     (1) ────── (N) Venta
```

### Inventario Model
```
Inventario (N) ───── empresa_id ──── (1) Empresa
          (N) ───── ubicacione_id ─ (1) Ubicacione
          (N) ───── producto_id ─── (1) Producto
```

### Kardex Model (Ledger)
```
Kardex (N) ───── empresa_id ──── (1) Empresa
      (N) ───── producto_id ─── (1) Producto
```

### Cliente & Proveedore Models
```
Cliente (N) ───── empresa_id ──── (1) Empresa
       (N) ───── persona_id ─── (1) Persona
       (1) ───── (N) Venta

Proveedore (N) ───── empresa_id ──── (1) Empresa
          (N) ───── persona_id ─── (1) Persona
          (1) ───── (N) Compra
```

### PaymentTransaction Model (NEW)
```
PaymentTransaction (N) ───── empresa_id ──── (1) Empresa
                   (N) ───── venta_id ─── (1) Venta
```

### StripeConfig Model (NEW)
```
StripeConfig (1) ───── empresa_id ──── (1) Empresa
```

---

## Global Scopes (Filtrado Automático)

```
┌─ GLOBAL SCOPE PROTECTION ─────────────────────────────┐
│                                                       │
│  Cuando auth()->user()->empresa_id = 1              │
│                                                       │
│  Venta::all()           → WHERE empresa_id = 1      │
│  Caja::all()            → WHERE empresa_id = 1      │
│  Movimiento::all()      → WHERE empresa_id = 1      │
│  Producto::all()        → WHERE empresa_id = 1      │
│  Cliente::all()         → WHERE empresa_id = 1      │
│  Compra::all()          → WHERE empresa_id = 1      │
│  Proveedore::all()      → WHERE empresa_id = 1      │
│  Inventario::all()      → WHERE empresa_id = 1      │
│  Kardex::all()          → WHERE empresa_id = 1      │
│                                                       │
│  ✅ Imposible acceder a datos de empresa_id = 2     │
│                                                       │
└───────────────────────────────────────────────────────┘
```

---

## Flujo de Datos - Venta Completa

```
┌──────────────────────────────────────────────────────────────────┐
│                    CREAR VENTA (Flujo Completo)                 │
└──────────────────────────────────────────────────────────────────┘

1. Usuario autenticado crea venta
   ↓
2. VentaController::store() 
   → $venta = Venta::create($data)
   ↓
3. VentaObserver::creating()
   → Captura empresa_id = auth()->user()->empresa_id
   ↓
4. Venta se guarda con:
   - empresa_id
   - caja_id
   - cliente_id
   - user_id
   - tarifa_servicio
   - monto_tarifa (calculado)
   ↓
5. Agregar productos:
   $venta->productos()->attach(
       $producto_id,
       ['cantidad' => 2, 'precio_venta' => 50, 'tarifa_unitaria' => 2.50]
   )
   ↓
6. CreateVentaEvent dispara listeners
   ├─ UpdateInventarioVentaListener
   │  → Disminuye stock en Inventario
   │
   ├─ CreateRegistroVentaCardexListener
   │  → Crea registro en Kardex
   │
   ├─ CreateMovimientoVentaCajaListener
   │  → Crea movimiento en Caja (venta_id grabado)
   │
   ├─ EnviarEmailClienteVentaListener
   │  → Envía confirmación al cliente
   │
   └─ CreateVentaDetalleEvent listener (si existe)
      → Crea PaymentTransaction
      → Registra pago (PENDING, SUCCESS, FAILED, etc.)

7. Toda la información queda vinculada:
   ✅ Venta.empresa_id = 1
   ✅ Movimiento.empresa_id = 1, venta_id = 1
   ✅ Kardex.empresa_id = 1
   ✅ Inventario.empresa_id = 1 (actualizado)
   ✅ PaymentTransaction.empresa_id = 1, venta_id = 1
```

---

## Campos Multi-Tenancy (empresa_id)

### Tablas con empresa_id
```
✅ users           (1:N Empresa)
✅ empleados       (1:N Empresa)
✅ cajas           (1:N Empresa)
✅ ventas          (1:N Empresa)
✅ movimientos     (1:N Empresa)
✅ compras         (1:N Empresa)
✅ productos       (1:N Empresa)
✅ clientes        (1:N Empresa)
✅ proveedores     (1:N Empresa)
✅ inventario      (1:N Empresa)
✅ kardex          (1:N Empresa)
✅ payment_transactions  (1:N Empresa)
✅ stripe_configs  (1:1 Empresa - UNIQUE)
```

### Tablas sin empresa_id (maestros globales)
```
⚪ monedas         (Maestro: moneda global del sistema)
⚪ personas        (Maestro: usada por Cliente y Proveedore)
⚪ categorias      (Maestro: categorías de productos)
⚪ marcas          (Maestro: marcas de productos)
⚪ presentaciones  (Maestro: tipos de presentación)
⚪ ubicaciones     (Maestro: ubicaciones de almacén)
⚪ documentos      (Maestro: tipos de documento)
⚪ comprobantes    (Maestro: tipos de comprobante)
```

---

## Scopes Disponibles

### Scopes de Filtrado por Empresa
```
Model::forEmpresa($empresaId)      → Todos los modelos
                                     (sorpasa global scope)
```

### Scopes Específicos por Modelo
```
Caja::abierta()                    → Estado = 'abierta'
Caja::cerrada()                    → Estado = 'cerrada'

Movimiento::ingresos()             → Tipo ingreso
Movimiento::egresos()              → Tipo egreso
Movimiento::enPeriodo($i, $f)      → Rango de fechas

Producto::activos()                → Estado activo
Producto::inactivos()              → Estado inactivo
Producto::byCategoria($id)         → Por categoría
Producto::search($term)            → Búsqueda

Inventario::stockBajo()            → Cantidad <= stock_minimo
Inventario::proximoVencimiento()   → Vencimiento próximo

Venta::enPeriodo($i, $f)           → Rango de fechas
Venta::byUser($id)                 → Por vendedor

Y muchos más...
```

---

## Métodos Helpers Clave

```php
// Venta
$venta->calcularTarifa(5)              → Calcula monto_tarifa
$venta->calcularTarifaUnitaria(id, 50) → Tarifa por producto

// Caja
$caja->cerrar(1500)                    → Cierra caja con saldo final
$caja->calcularSaldo()                 → Saldo = inicial + movs
$caja->estaAbierta()                   → Boolean
$caja->estaCerrada()                   → Boolean

// Inventario
$inv->aumentarStock(10)                → Suma cantidad
$inv->disminuirStock(5)                → Resta cantidad
$inv->estaVencido()                    → Vencimiento pasado?
$inv->esStockBajo()                    → Cantidad <= minimo?

// PaymentTransaction
$trans->isSuccessful()                 → Status == SUCCESS?
$trans->isFailed()                     → Status == FAILED?
$trans->markAsSuccess($metadata)       → Marca como exitosa
$trans->markAsFailed($error, $meta)    → Marca como fallida

// StripeConfig
$config->isEnabled()                   → enabled == true?
$config->isTestMode()                  → test_mode == true?
$config->getPublicKey()                → Retorna public_key
$config->getSecretKey()                → Retorna secret (desencriptado)
```

---

**Diagrama actualizado:** 30 de enero de 2026  
**Versión:** 2.0 (Post-actualización de modelos)  
**Estado:** ✅ COMPLETO Y CONSISTENTE
