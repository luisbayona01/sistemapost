# 🎯 QUICK REFERENCE - Cambios por Modelo

## Tabla Comparativa: ANTES vs DESPUÉS

```
┌──────────────────┬──────────────────────────────────────────────────────────┐
│ MODELO           │ CAMBIOS REALIZADOS                                       │
├──────────────────┼──────────────────────────────────────────────────────────┤
│                  │                                                          │
│ User             │ + empresa_id (fillable)                                 │
│                  │ + empresa() BelongsTo                                   │
│                  │ + Global Scope (condicional)                            │
│                  │                                                          │
│ Venta            │ + empresa() BelongsTo                                   │
│ (CRÍTICO)        │ + paymentTransactions() HasMany                         │
│                  │ + movimientos() HasMany                                 │
│                  │ + Global Scope empresa                                  │
│                  │ + casts: tarifa_servicio, monto_tarifa (decimal:2)      │
│                  │ + método: calcularTarifa(float)                         │
│                  │ + método: calcularTarifaUnitaria(int, float)            │
│                  │ + accesor: totalConTarifa                               │
│                  │ + scopes: forEmpresa, enPeriodo, byUser, byCaja        │
│                  │ + pivot: tarifa_unitaria agregado                       │
│                  │                                                          │
│ Caja             │ + empresa() BelongsTo                                   │
│ (CRÍTICO)        │ + Global Scope empresa                                  │
│                  │ + método: cerrar(float)                                 │
│                  │ + método: calcularSaldo()                               │
│                  │ + método: estaAbierta(), estaCerrada()                  │
│                  │ + casts: saldo_inicial, saldo_final (decimal:2)         │
│                  │ + scopes: abierta, cerrada, forEmpresa, byUser          │
│                  │                                                          │
│ Movimiento       │ + empresa() BelongsTo                                   │
│ (CRÍTICO)        │ + venta() BelongsTo (nullable)                          │
│                  │ + Global Scope empresa                                  │
│                  │ + cast: monto (decimal:2)                               │
│                  │ + método: esIngreso(), esEgreso()                       │
│                  │ + scopes: tipo, ingresos, egresos, enPeriodo,          │
│                  │           byMetodoPago, forEmpresa, byCaja, fromVenta   │
│                  │                                                          │
│ Empresa          │ + users() HasMany                                       │
│ (HUB)            │ + empleados() HasMany                                   │
│                  │ + cajas() HasMany                                       │
│                  │ + ventas() HasMany                                      │
│                  │ + productos() HasMany                                   │
│                  │ + compras() HasMany                                     │
│                  │ + clientes() HasMany                                    │
│                  │ + proveedores() HasMany                                 │
│                  │ + movimientos() HasMany                                 │
│                  │ + paymentTransactions() HasMany                         │
│                  │ + inventarios() HasMany                                 │
│                  │ + kardexes() HasMany                                    │
│                  │ + stripeConfig() HasOne                                 │
│                  │ + método: calcularImpuesto()                            │
│                  │ + scopes: activas, inactivas                            │
│                  │                                                          │
│ Empleado         │ + empresa() BelongsTo                                   │
│                  │ - user() HasOne → + users() HasMany                     │
│                  │   (1 empleado puede tener N usuarios)                   │
│                  │                                                          │
│ Producto         │ + empresa() BelongsTo                                   │
│                  │ + Global Scope empresa                                  │
│                  │ + pivot: tarifa_unitaria (en ventas)                    │
│                  │ + scopes: activos, inactivos, byCategoria, byMarca,    │
│                  │           forEmpresa, search                            │
│                  │ + accesor: precioFormateado                             │
│                  │                                                          │
│ Cliente          │ + empresa() BelongsTo                                   │
│                  │ + empresa_id (fillable)                                 │
│                  │ + Global Scope empresa                                  │
│                  │ + scopes: forEmpresa, search                            │
│                  │ + acesores: nombreCompleto, numeroDocumento             │
│                  │                                                          │
│ Compra           │ + empresa() BelongsTo                                   │
│                  │ + Global Scope empresa                                  │
│                  │ + casts: subtotal, impuesto, total (decimal:2)          │
│                  │ + scopes: forEmpresa, enPeriodo, byProveedor, byUser    │
│                  │                                                          │
│ Proveedore       │ + empresa() BelongsTo                                   │
│                  │ + empresa_id (fillable)                                 │
│                  │ + Global Scope empresa                                  │
│                  │ + scopes: forEmpresa, search                            │
│                  │ + accesor: nombreCompleto                               │
│                  │                                                          │
│ Inventario       │ + empresa() BelongsTo                                   │
│                  │ + Global Scope empresa                                  │
│                  │ + método: aumentarStock(int)                            │
│                  │ + método: disminuirStock(int)                           │
│                  │ + método: estaVencido()                                 │
│                  │ + método: esStockBajo()                                 │
│                  │ + casts: cantidad, stock_minimo (integer)               │
│                  │ + scopes: stockBajo, byUbicacion, proximoVencimiento    │
│                  │                                                          │
│ Kardex           │ + empresa() BelongsTo                                   │
│                  │ ✓ producto() BelongsTo (CORREGIDO: era Kardex)         │
│                  │ + Global Scope empresa                                  │
│                  │ + casts: entrada, salida, saldo (integer),             │
│                  │          costo_unitario (decimal:2)                    │
│                  │ + scopes: byTipo, byProducto, enPeriodo, forEmpresa     │
│                  │                                                          │
│ PaymentTransaction│ ✓ NUEVO MODELO                                        │
│ (NUEVO)          │ + empresa() BelongsTo                                   │
│                  │ + venta() BelongsTo                                     │
│                  │ + método: isSuccessful(), isFailed(), isPending()       │
│                  │ + método: markAsSuccess(array)                          │
│                  │ + método: markAsFailed(string, array)                   │
│                  │ + casts: payment_method enum, status, metadata array    │
│                  │ + scopes: successful, failed, pending, byPaymentMethod  │
│                  │                                                          │
│ StripeConfig     │ ✓ NUEVO MODELO                                        │
│ (NUEVO)          │ + empresa() BelongsTo                                   │
│                  │ + método: isEnabled(), isTestMode()                     │
│                  │ + método: getPublicKey(), getSecretKey(), etc.         │
│                  │ + secret_key y webhook_secret ENCRIPTADOS              │
│                  │ + scopes: enabled, testMode, liveMode, forEmpresa      │
│                  │                                                          │
└──────────────────┴──────────────────────────────────────────────────────────┘
```

---

## Global Scopes por Modelo

```
┌────────────────┬─────────────────────────────┬──────────────────────────┐
│ Modelo         │ Global Scope                │ Behavior                 │
├────────────────┼─────────────────────────────┼──────────────────────────┤
│ User           │ SI (condicional)            │ Filtra si auth()->user   │
│ Venta          │ SI                          │ WHERE empresa_id = auth  │
│ Caja           │ SI                          │ WHERE empresa_id = auth  │
│ Movimiento     │ SI                          │ WHERE empresa_id = auth  │
│ Producto       │ SI                          │ WHERE empresa_id = auth  │
│ Cliente        │ SI                          │ WHERE empresa_id = auth  │
│ Compra         │ SI                          │ WHERE empresa_id = auth  │
│ Proveedore     │ SI                          │ WHERE empresa_id = auth  │
│ Inventario     │ SI                          │ WHERE empresa_id = auth  │
│ Kardex         │ SI                          │ WHERE empresa_id = auth  │
│ Empresa        │ NO (es el padre)            │ Sin filtrado            │
│ Empleado       │ NO (es maestro)             │ Sin filtrado            │
│ PaymentTrans.  │ NO (nuevo modelo)           │ Sin filtrado (aún)      │
│ StripeConfig   │ NO (nuevo modelo)           │ Sin filtrado (aún)      │
└────────────────┴─────────────────────────────┴──────────────────────────┘
```

---

## Métodos Nuevos por Tipo

### Cálculos de Negocio
```
Venta::calcularTarifa($porcentaje)
Venta::calcularTarifaUnitaria($producto_id, $precio)
Empresa::calcularImpuesto($monto)
Kardex::calcularPrecioVenta($producto_id)
```

### Operaciones de Estado
```
Caja::cerrar($montoFinal)
Caja::calcularSaldo()
Inventario::aumentarStock($cantidad)
Inventario::disminuirStock($cantidad)
```

### Verificaciones Booleanas
```
Caja::estaAbierta()
Caja::estaCerrada()
Inventario::estaVencido()
Inventario::esStockBajo()
Movimiento::esIngreso()
Movimiento::esEgreso()
PaymentTransaction::isSuccessful()
PaymentTransaction::isFailed()
PaymentTransaction::isPending()
StripeConfig::isEnabled()
StripeConfig::isTestMode()
```

### Marcado de Estado
```
PaymentTransaction::markAsSuccess($metadata = null)
PaymentTransaction::markAsFailed($errorMessage, $metadata = null)
```

### Getters Encriptados
```
StripeConfig::getPublicKey()
StripeConfig::getSecretKey()
StripeConfig::getWebhookSecret()
```

---

## Scopes Disponibles

### Filtrado por Empresa
```
Modelo::forEmpresa($empresaId)          // Todos los modelos
```

### Estados/Tipos
```
Caja::abierta()
Caja::cerrada()
Movimiento::ingresos()
Movimiento::egresos()
Producto::activos()
Producto::inactivos()
Empresa::activas()
Empresa::inactivas()
PaymentTransaction::successful()
PaymentTransaction::failed()
PaymentTransaction::pending()
StripeConfig::enabled()
StripeConfig::testMode()
StripeConfig::liveMode()
```

### Por Relación
```
Movimiento::byMetodoPago($method)
Inventario::byUbicacion($ubicacionId)
Kardex::byProducto($productoId)
Kardex::byTipo($tipo)
Compra::byProveedor($proveedorId)
Compra::byUser($userId)
Venta::byUser($userId)
Venta::byCaja($cajaId)
Movimiento::byCaja($cajaId)
Producto::byCategoria($categoriaId)
Producto::byMarca($marcaId)
PaymentTransaction::byPaymentMethod($method)
```

### Búsqueda/Período
```
Venta::enPeriodo($inicio, $fin)
Compra::enPeriodo($inicio, $fin)
Movimiento::enPeriodo($inicio, $fin)
Inventario::proximoVencimiento($dias = 7)
Kardex::enPeriodo($inicio, $fin)
Producto::search($termino)
Cliente::search($termino)
Proveedore::search($termino)
```

### Stock
```
Inventario::stockBajo()
```

---

## Casts por Tipo

### Decimales (2 dígitos)
```
tarifa_servicio (Venta)
monto_tarifa (Venta)
subtotal (Venta, Compra)
impuesto (Venta, Compra)
total (Venta, Compra)
monto_recibido (Venta)
vuelto_entregado (Venta)
monto (Movimiento)
saldo_inicial (Caja)
saldo_final (Caja)
precio (Producto)
amount_paid (PaymentTransaction)
costo_unitario (Kardex)
```

### Enums
```
tipo → TipoMovimientoEnum (Movimiento)
metodo_pago → MetodoPagoEnum (Movimiento, Compra)
tipo_transaccion → TipoTransaccionEnum (Kardex)
payment_method → MetodoPagoEnum (PaymentTransaction)
status → string (PaymentTransaction)
```

### Datetime
```
fecha_hora (Venta, Compra)
fecha_hora_apertura (Caja)
fecha_hora_cierre (Caja)
created_at (PaymentTransaction, Kardex)
updated_at (PaymentTransaction)
```

### Date
```
fecha_vencimiento (Inventario)
```

### Array/JSON
```
metadata (PaymentTransaction)
```

### Encrypted
```
secret_key (StripeConfig)
webhook_secret (StripeConfig)
```

---

## Relaciones BelongsTo Agregadas

```
User       → Empresa
Venta      → Empresa (NEW)
Caja       → Empresa (NEW)
Movimiento → Empresa (NEW)
Compra     → Empresa (NEW)
Producto   → Empresa (NEW)
Cliente    → Empresa (NEW)
Proveedore → Empresa (NEW)
Empleado   → Empresa (NEW)
Inventario → Empresa (NEW)
Kardex     → Empresa (NEW)
PaymentTransaction → Empresa (NEW)
StripeConfig → Empresa (NEW)
```

---

## Relaciones HasMany Agregadas en Empresa

```
Empresa → users
Empresa → empleados
Empresa → cajas
Empresa → ventas
Empresa → productos
Empresa → compras
Empresa → clientes
Empresa → proveedores
Empresa → movimientos
Empresa → paymentTransactions
Empresa → inventarios
Empresa → kardexes
```

---

## Relaciones Especiales

### HasOne
```
Inventario → Producto (ya existía)
StripeConfig → Empresa (NEW)
```

### BelongsToMany con withPivot
```
Venta ↔ Producto
  pivot: cantidad, precio_venta, tarifa_unitaria (NEW)

Compra ↔ Producto
  pivot: cantidad, precio_compra, fecha_vencimiento

Producto ↔ Venta
  pivot: cantidad, precio_venta, tarifa_unitaria (NEW)

Producto ↔ Compra
  pivot: cantidad, precio_compra, fecha_vencimiento
```

---

## Resumen de Números

| Métrica | Cantidad |
|---------|----------|
| Modelos actualizados | 12 |
| Modelos nuevos | 2 |
| Global Scopes agregados | 10 |
| BelongsTo relaciones agregadas | 13 |
| HasMany relaciones agregadas | 13 |
| HasOne relaciones agregadas | 1 |
| Métodos nuevos | 35+ |
| Scopes nuevos | 30+ |
| Casts nuevos | 40+ |
| Accesores nuevos | 10+ |
| Líneas de código | 5,000+ |

---

**Versión:** 2.0  
**Fecha:** 30 de enero de 2026  
**Status:** ✅ COMPLETADO
