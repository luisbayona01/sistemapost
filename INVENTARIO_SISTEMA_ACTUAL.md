# 📊 INVENTARIO TÉCNICO DEL SISTEMA - ESTADO ACTUAL
**Fecha:** 2026-02-07  
**Versión:** Post-Refactorización Cinema

---

## 1. ENTIDADES Y MODELOS EXISTENTES

### Módulo Cinema (Nuevo - Refactorizado)
| Modelo | Responsabilidad | Relaciones Clave |
|--------|----------------|------------------|
| `Pelicula` | Catálogo de películas | → `Funcion`, → `Distribuidor`, → `Empresa` |
| `Funcion` | Programación de horarios | → `Pelicula`, → `Sala`, → `FuncionAsiento`, → `PrecioEntrada` |
| `Sala` | Salas físicas del cine | → `Funcion`, → `Empresa` |
| `FuncionAsiento` | Estado de asientos por función | → `Funcion`, → `Venta` |
| `PrecioEntrada` | Tipos de entrada (General, Niño, 3D) | → `Funcion` |
| `Distribuidor` | Proveedores de películas | → `Pelicula` |

**Nota:** `Pelicula` ya NO es `Producto`. Separación completa.

---

### Módulo Ventas / Facturación
| Modelo | Responsabilidad | Relaciones Clave |
|--------|----------------|------------------|
| `Venta` | Transacción de venta unificada | → `Empresa`, → `Caja`, → `Cliente`, → `User`, → `Comprobante`, → `Producto` (pivot), → `PaymentTransaction`, → `FuncionAsiento` |
| `Producto` | Productos de confitería/retail | → `Categoria`, → `Marca`, → `Presentacione`, → `Inventario`, → `Venta` (pivot) |
| `Cliente` | Clientes del sistema | → `Venta` |
| `Comprobante` | Tipos de comprobante (Boleta, Factura) | → `Venta` |

**Campos Críticos de Control:** 
- `Venta.canal` (enum: `ventanilla`, `confiteria`, `web`)
- `Venta.tipo_venta` (enum: `FISICA`, `WEB`)
- `Venta.origen` (enum: `POS`, `WEB`)
- `Venta.estado_pago` (enum: `PENDIENTE`, `PAGADA`, `FALLIDA`, `CANCELADA`)

---

### Módulo Inventario
| Modelo | Responsabilidad | Relaciones Clave |
|--------|----------------|------------------|
| `Inventario` | Stock actual de productos | → `Producto` (1:1) |
| `Kardex` | Historial de movimientos | → `Producto`, → `User` |
| `Insumo` | Materias primas | → `InsumoLote`, → `Receta` |
| `InsumoLote` | Lotes de insumos | → `Insumo` |
| `InsumoSalida` | Salidas de insumos | → `Insumo` |
| `Receta` | Fórmulas de producción | → `Producto`, → `Insumo` |
| `AuditoriaInventario` | Auditorías ciegas | → `Empresa`, → `User` |
| `AuditoriaDetalle` | Detalle de auditorías | → `AuditoriaInventario`, → `Producto` |

---

### Módulo Caja / Pagos
| Modelo | Responsabilidad | Relaciones Clave |
|--------|----------------|------------------|
| `Caja` | Sesión de caja (apertura/cierre) | → `User`, → `Empresa`, → `Venta`, → `Movimiento` |
| `Movimiento` | Transacciones de caja | → `Caja`, → `Venta`, → `User` |
| `PaymentTransaction` | Transacciones de pago externas (Stripe) | → `Venta`, → `User` |

---

### Módulo Compras / Proveedores
| Modelo | Responsabilidad | Relaciones Clave |
|--------|----------------|------------------|
| `Compra` | Compras a proveedores | → `Proveedore`, → `Empresa`, → `User` |
| `Proveedore` | Proveedores | → `Compra`, → `Empresa` |

---

### Módulo Catálogos / Maestros
| Modelo | Responsabilidad | Relaciones Clave |
|--------|----------------|------------------|
| `Categoria` | Categorías de productos | → `Producto`, → `Caracteristica` |
| `Marca` | Marcas | → `Producto` |
| `Presentacione` | Presentaciones (unidad, caja, etc.) | → `Producto` |
| `Caracteristica` | Características de categorías | → `Categoria` |

---

### Módulo Empresa / Usuarios
| Modelo | Responsabilidad | Relaciones Clave |
|--------|----------------|------------------|
| `Empresa` | Tenant principal (multi-tenant) | → `User`, → `Venta`, → `Producto`, → `Pelicula`, etc. |
| `User` | Usuarios del sistema | → `Empresa`, → `Venta`, → `Caja` |
| `Empleado` | Empleados (datos adicionales) | → `Empresa` |

---

### Módulo SaaS / Suscripciones / Configuración
| Modelo | Responsabilidad | Relaciones Clave |
|--------|----------------|------------------|
| `SaaSPlan` | Planes de suscripción | → `Empresa` |
| `StripeConfig` | Configuración de Stripe | → `Empresa` |
| `BusinessConfiguration` | Configuración modular (Multi-negocio) | → `Empresa` |

---

### Otros
| Modelo | Responsabilidad |
|--------|----------------|
| `ActivityLog` | Registro de actividad del sistema |
| `Persona` | Datos personales genéricos |
| `Documento` | Tipos de documento |
| `Ubicacione` | Ubicaciones geográficas |
| `Moneda` | Monedas |

---

## 2. FLUJOS FUNCIONALES COMPLETOS

### ✅ Cinema - Gestión de Películas
1. Crear película (`PeliculaController::store`)
2. Asignar distribuidor
3. Programar funciones (`FuncionController::store`)
4. Asignar sala y horario
5. Definir precios por tipo de entrada
6. Generar mapa de asientos automáticamente

### ✅ Cinema - Venta de Entradas
1. Seleccionar función desde POS
2. Ver mapa de asientos (`CinemaController::showSeatMap`)
3. Seleccionar asientos (múltiples)
4. Reservar temporalmente (5 min) (`CinemaController::reservarAsiento`)
5. Procesar venta (`CinemaController::venderAsiento`)
   - Crea `Venta` con `canal='ventanilla'`
   - Confirma asientos (`FuncionAsiento::estado='vendido'`)
   - Registra en caja
6. Generar ticket PDF (`CinemaController::exportarTicket`)

### ✅ POS - Venta Mixta (Cinema + Confitería)
1. Agregar entradas al carrito (`CashierController::agregarBoleto`)
2. Agregar productos de confitería (`CashierController::agregarProducto`)
   - AJAX sin recarga de página
3. Finalizar venta única (`CashierController::finalizarVenta`)
   - Procesa ambos tipos en una sola transacción
   - Confirma asientos
   - Descuenta inventario
   - Registra en caja

### ✅ WEB - Venta Online (E-commerce / App)
1. Crear venta en estado `PENDIENTE` (`VentaService::procesarVentaWeb`)
   - Define `tipo_venta='WEB'`, `origen='WEB'`
   - Solo admite `metodo_pago='STRIPE'`
2. Crear Intento de Pago en Stripe (`StripePaymentService::createPaymentIntent`)
   - Genera `PaymentTransaction` en estado `PENDING`
3. Confirmación vía Webhook (`StripePaymentService::handleWebhook`)
   - Al recibir éxito: `PaymentTransaction -> SUCCESS` (Inmutable)
   - Al recibir éxito: `Venta -> PAGADA`
   - Se disparan eventos de inventario y notificación.

### ✅ Inventario - Gestión de Stock
1. Crear producto (`ProductoController::store`)
2. Registrar entrada de inventario
3. Venta automática descuenta stock (Listener: `ReduceStockOnSale`)
4. Auditoría ciega (`AuditoriaInventario`)
5. Ajuste de inventario post-auditoría
6. Kardex automático

### ✅ Caja - Apertura/Cierre
1. Apertura de caja (`CajaController::store`)
2. Registro automático de ventas en `Movimiento`
3. Ingresos/Egresos manuales
4. Cierre de caja con cuadre
5. Reporte de caja

### ✅ Compras
1. Crear compra (`compraController::store`)
2. Asociar proveedor
3. Incrementar inventario automáticamente

### ✅ Reportes
1. Reporte consolidado (`ConsolidatedReportController`)
   - Ventas por canal (ventanilla, confiteria, web)
2. Reporte de cinema (`CinemaReportController`)
   - Ingresos taquilla
   - Ocupación promedio
   - Top películas
3. Reporte de confitería (`ConcessionsReportController`)
   - Ingresos snacks
   - Ticket promedio
   - Top productos

### ✅ Configuración Modular (Multi-negocio)
1. Definir tipo de negocio (`cinema`, `restaurant`, etc.)
2. Habilitar/Deshabilitar módulos (`cinema`, `pos`, `inventory`, `reports`, `api`)
3. Control de acceso mediante Middleware `CheckModuleEnabled`
4. Menú lateral (Sidebar) dinámico basado en configuración activa

---

## 3. FLUJOS PARCIALMENTE IMPLEMENTADOS

### ⚠️ Gestión de Insumos y Recetas
- **Existe:** Modelos `Insumo`, `Receta`, `InsumoLote`, `InsumoSalida`
- **Falta:** 
  - Controladores completos para CRUD
  - Descuento automático de insumos al vender productos con receta
  - Cálculo de costo real basado en recetas

### ⚠️ Reportes Avanzados
- **Existe:** Reportes básicos por canal
- **Falta:**
  - Gráficos interactivos
  - Exportación a Excel/PDF
  - Análisis de rentabilidad por producto
  - Proyecciones de ventas

### ⚠️ Notificaciones en Tiempo Real
- **Existe:** Evento `AsientoBloqueado` (broadcasting)
- **Falta:**
  - Configuración de Pusher/Soketi
  - Laravel Echo en frontend
  - Notificaciones de stock bajo
  - Alertas de caja

### ✅ Multi-Método de Pago e Integración Stripe
- **Entidad Única:** `PaymentTransaction` (Sustituye a `VentaPago`)
- **Seguridad:** Transacciones `SUCCESS` son inmutables (no se pueden editar ni borrar).
- **Reglas de Dominio:** 
  - Venta WEB: Prohibido EFECTIVO, Prohibido pago manual (requiere transacción confirmada).
  - Venta FISICA: Prohibido STRIPE (en este flujo de Fase 2).
- **Webhook:** Sincronización automática de estado `PAGADA` tras confirmación de Stripe.

---

## 4. MÓDULOS CLARAMENTE SEPARADOS

### ✅ Separación Arquitectónica

#### Cinema (Independiente)
- **Modelos:** `Pelicula`, `Funcion`, `Sala`, `FuncionAsiento`, `PrecioEntrada`
- **Controladores:** `CinemaController`, `FuncionController`, `PeliculaController`
- **Services:** `CinemaService`, `TicketService`
- **Actions:** `ProcesarVentaCinemaAction`
- **Identificador:** `Venta.canal = 'ventanilla'`

#### Retail / Confitería (Independiente)
- **Modelos:** `Producto`, `Inventario`, `Categoria`, `Marca`
- **Controladores:** `ProductoController`, `ventaController`
- **Services:** `ProductoService`
- **Identificador:** `Venta.canal = 'confiteria'`

#### Ventas (Unificado)
- **Modelos:** `Venta`, `PaymentTransaction`
- **Services:** `VentaService` (procesa ambos canales)
- **Scopes:** `boleteria()`, `confiteria()`, `web()`, `fisicas()`

#### Inventario (Transversal)
- **Modelos:** `Inventario`, `Kardex`, `Insumo`, `Receta`
- **Controladores:** `InventarioControlller`, `InsumoController`
- **Listeners:** `ReduceStockOnSale`

#### Caja (Transversal)
- **Modelos:** `Caja`, `Movimiento`
- **Controladores:** `CajaController`, `MovimientoController`
- **Scopes:** `porCaja()`, `porEmpresa()`

#### Modularidad / SaaS (Control Central)
- **Modelos:** `BusinessConfiguration`, `SaaSPlan`, `Empresa`
- **Middleware:** `CheckModuleEnabled`
- **Helpers:** `ModuleHelper`
- **Función:** Activa/Desactiva rutas y menús dinámicamente.

---

## 5. QUÉ NO EXISTE TODAVÍA

### Funcionalidades Faltantes
1. **Reservas Web (E-commerce)**
   - Frontend público para compra de entradas
   - Pasarela de pago integrada
   - Confirmación por email

2. **Gestión de Empleados Completa**
   - Asistencia
   - Nómina
   - Comisiones

3. **CRM / Fidelización**
   - Programa de puntos
   - Membresías
   - Descuentos por cliente

4. **Marketing**
   - Campañas de email
   - Promociones automáticas
   - Cupones de descuento
### FASE 3: MÚSCULO OPERATIVO (IMPLEMENTADO)
El sistema ha evolucionado de un esqueleto técnico a una herramienta de gestión diaria robusta.

#### 1. Inventario Operativo e Inmutable
*   **Kardex Unificado**: Se ha centralizado el seguimiento de movimientos tanto para **Productos** (retail) como para **Insumos** (materia prima). Todo cambio de stock (Venta, Compra, Ajuste, Auditoría) genera un registro ineditable en el Kardex.
*   **Ajustes Controlados**: Se implementó un flujo formal de ajustes manuales donde es obligatorio especificar el motivo (Merma, Daño, Error de Conteo, Vencimiento), eliminando la edición "a mano" del stock.
*   **Auditorías Ciegas**: Se completó el ciclo de auditoría donde se compara el stock teórico vs físico, aplicando ajustes automáticos y registrando la valorización de las diferencias encontradas.

#### 2. Gestión de Costos y Gastos
*   **Costeo Real**: Los productos calculan su rentabilidad basándose en el costo de sus insumos, merma esperada y gastos operativos fijos.
*   **Gastos Operacionales**: Módulo para el registro de gastos fijos (Agua, Luz, Gas, Internet) asociados a periodos mensuales para análisis posterior de rentabilidad neta.
*   **FIFO (Insumos)**: Las recetas descuentan stock de insumos utilizando el método First-In-First-Out, asegurando una valoración precisa del inventario basada en lotes de compra reales.

#### 3. Reportes Operativos
*   **Inventario Valorizado**: Visión en tiempo real del capital invertido en almacén (Insumos + Productos).
*   **Análisis de Ventas**: Desglose por día, canal (POS vs WEB) y producto.
*   **Marginalidad**: Listado de productos con su utilidad bruta, margen porcentual y ránking de desempeño (Top/Bottom).

---

### REGLAS DE DOMINIO VIGENTES (RESUMEN)
1.  **Venta WEB**: Método Obligatorio = STRIPE. Estado Inicial = PENDIENTE. Cierre automático vía Webhook. No toca caja física.
2.  **Venta FÍSICA**: No permite STRIPE. Requiere Caja Abierta. Origen = POS.
3.  **Transacciones**: Las `PaymentTransaction` con estado `SUCCESS` son inmutables (no se pueden editar ni borrar).
4.  **Kardex**: Es la fuente de verdad del inventario. Ningún stock se mueve sin un registro asociado.

6. **Integraciones Externas**
   - Facturación electrónica (SII Chile)
   - Contabilidad (Quickbooks, etc.)
   - Sistemas de pago locales (Transbank)

7. **Móvil**
   - App nativa
   - PWA

8. **BI / Analytics**
   - Dashboard ejecutivo
   - KPIs en tiempo real
   - Machine Learning para predicciones

---

## 6. ESTADO GENERAL DEL SISTEMA

### ✅ ES UN MVP FUNCIONAL
**Sí.** El sistema puede:
- Gestionar películas y funciones
- Vender entradas con selección de asientos
- Vender productos de confitería
- Procesar ventas mixtas (cinema + confitería)
- Controlar inventario
- Manejar caja
- Generar reportes básicos

### ✅ ESTÁ ESTABLE PARA DEMO
**Sí, con correcciones recientes:**
- ✅ Separación `Pelicula` vs `Producto` completada
- ✅ Reportes corregidos (sin errores de `distribuidor_id`)
- ✅ POS con AJAX funcional
- ✅ Venta mixta operativa
- ⚠️ Requiere datos de prueba (seeders ejecutados)

### ✅ ESTÁ LISTO PARA EXTENDERSE
**Sí.** Arquitectura permite:
- Agregar nuevos canales de venta (`canal='web'`)
- Implementar nuevos tipos de productos
- Extender reportes sin romper existentes
- Agregar nuevas salas/funciones
- Integrar servicios externos (Stripe ya preparado)

---

## 📌 ARQUITECTURA CLAVE

### Patrón Multi-Tenant
- Filtro global por `empresa_id` en todos los modelos principales
- Aislamiento de datos por empresa

### Patrón Service Layer
- `VentaService`: Lógica de negocio de ventas
- `CinemaService`: Lógica de reservas/confirmación
- `ProductoService`: Gestión de productos
- `TicketService`: Generación de tickets PDF

### Patrón Action
- `ProcesarVentaCinemaAction`: Orquesta venta de cinema (coordina VentaService + CinemaService)

### Event-Driven
- `CreateVentaEvent`: Disparado al crear venta
- `CreateVentaDetalleEvent`: Disparado al agregar detalle
- `AsientoBloqueado`: Broadcasting (preparado, no activo)

### Listeners
- `ReduceStockOnSale`: Descuenta inventario automáticamente

---

## 🔑 PUNTOS CRÍTICOS DE INTEGRACIÓN

1. **Venta Mixta:** `CashierController::finalizarVenta()`
   - Coordina productos + boletos en una sola transacción
   - Usa `VentaService` + `CinemaService`

2. **Confirmación de Asientos:** `CinemaService::confirmarVenta()`
   - Valida bloqueo temporal
   - Marca asiento como vendido
   - Asocia a venta

3. **Descuento de Inventario:** Listener `ReduceStockOnSale`
   - Escucha `CreateVentaDetalleEvent`
   - Descuenta stock con lock optimista

4. **Registro en Caja:** `VentaService::registrarMovimiento()`
   - Automático al crear venta
   - Tipo: `VENTA`

---

**FIN DEL INVENTARIO**
