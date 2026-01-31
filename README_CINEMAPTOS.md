# CinemaPOS - Punto de Venta SaaS para Confiterías de Cines

[![Laravel](https://img.shields.io/badge/Laravel-12.0-FF2D20?logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?logo=php&logoColor=white)](https://www.php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-00758F?logo=mysql&logoColor=white)](https://www.mysql.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

## 📋 Descripción General

**CinemaPOS** es un sistema de punto de venta SaaS especializado para confiterías de cines. Diseñado como multi-tenancy desde el inicio, permite gestionar múltiples sucursales con:

- ✅ Sistema de caja con apertura y cierre
- ✅ Venta de productos de confitería
- ✅ Tarifa por servicio explícita y auditable
- ✅ Integración preparada para Stripe
- ✅ Reportes y auditoría completa
- ✅ Control de inventario
- ✅ Gestión de usuarios por empresa

---

## 🏗️ Arquitectura del Sistema

### Modelo de Datos

El sistema está estructurado con los siguientes componentes principales:

```
┌─────────────────────────────────────────────────────────┐
│                     EMPRESA                              │
│  (Cinema - Centro de operaciones)                        │
│  - Datos generales (RUC, razón social)                   │
│  - Moneda predeterminada                                 │
│  - Configuración de tarifa por servicio                  │
└─────────────────────────────────────────────────────────┘
         ↓           ↓           ↓           ↓
┌─────────────┐ ┌──────────┐ ┌───────┐ ┌──────────┐
│ EMPLEADOS   │ │ CAJAS    │ │CLIENTES│ │PRODUCTOS │
│ (Cajeros)   │ │ (Registros)         │ (Confitería)
└─────────────┘ └──────────┘ └───────┘ └──────────┘
     ↓               ↓
  ┌─────────────────────────┐
  │      VENTAS             │
  │  - Cada venta vinculada  │
  │    a empresa + caja      │
  │  - Con tarifa explícita  │
  └─────────────────────────┘
        ↓           ↓
┌─────────────────┐  ┌─────────────────────┐
│  MOVIMIENTOS    │  │ PRODUCT_VENTA       │
│  (Caja)         │  │ (Detalles de venta) │
│  - VENTA        │  │ - Tarifa unitaria   │
│  - RETIRO       │  │ - Cantidad          │
│  - DEPOSITO     │  │ - Precio            │
└─────────────────┘  └─────────────────────┘
        ↓
┌─────────────────────────┐
│ PAYMENT_TRANSACTIONS    │
│ - CASH                  │
│ - CARD                  │
│ - STRIPE (futuro)       │
└─────────────────────────┘
```

### Tablas Principales

| Tabla | Propósito | Multiempresa |
|-------|-----------|--------------|
| `empresa` | Datos de la sucursal (cine) | N/A - padre |
| `users` | Autenticación de usuarios | ✅ `empresa_id` |
| `empleados` | Cajeros y personal | ✅ `empresa_id` |
| `cajas` | Puntos de venta (registros) | ✅ `empresa_id` |
| `productos` | Catálogo de confitería | ✅ `empresa_id` |
| `ventas` | Historial de transacciones | ✅ `empresa_id` + tarifa |
| `movimientos` | Flujo de caja | ✅ `empresa_id` + venta_id |
| `payment_transactions` | Registro de pagos | ✅ `empresa_id` |
| `stripe_configs` | Configuración Stripe | ✅ por empresa |

---

## 🔄 Flujo de Venta POS

### 1️⃣ Inicio de Sesión

```
Usuario (Cajero)
    ↓
Autentica con email + password
    ↓
Sistema carga User → Empresa
    ↓
Sesión contiene empresa_id
    ↓
Todos los queries se filtran automáticamente por empresa_id
```

**Código de Ejemplo:**
```php
// En middleware o en modelo User
public function empresa(): BelongsTo {
    return $this->belongsTo(Empresa::class);
}

// En cualquier query posterior
Venta::where('empresa_id', Auth::user()->empresa_id)->get();
```

### 2️⃣ Apertura de Caja

```
Cajero inicia sesión
    ↓
Hace clic en "Abrir Caja"
    ↓
Sistema registra en DB:
    - empresa_id = Auth::user()->empresa_id
    - user_id = Auth::user()->id
    - fecha_hora_apertura = NOW()
    - saldo_inicial = (cantidad en efectivo ingresada)
    - estado = true
    ↓
Caja está lista para ventas
```

**Estructura BD:**
```sql
INSERT INTO cajas (
    empresa_id, 
    nombre, 
    fecha_hora_apertura, 
    saldo_inicial, 
    estado, 
    user_id
) VALUES (1, 'Caja 1', '2026-01-30 14:30:00', 500.00, true, 5);
```

### 3️⃣ Proceso de Venta

#### A. Cajero selecciona productos

```
Producto seleccionado:
  - Código: POP001
  - Nombre: Popcorn Mediano
  - Precio: 25.00 (ya incluye costo base)
  - Cantidad: 2
```

#### B. Sistema calcula totales

```
VENTA:
├─ Subtotal = 25.00 × 2 = 50.00
├─ Impuesto = 50.00 × 15% = 7.50
├─ Tarifa Servicio = 3.50% (configurado por empresa)
├─ Monto Tarifa = 50.00 × 3.50% = 1.75
└─ TOTAL = 50.00 + 7.50 + 1.75 = 59.25
```

**Fórmula de Cálculo (en código):**
```php
// En modelo Venta o en servicio
$subtotal = $productos->sum(fn($p) => $p->precio * $p->cantidad);
$impuesto = $empresa->porcentaje_impuesto; // de tabla empresa
$tarifa_servicio = $empresa->tarifa_servicio_defecto; // 3.50

$monto_impuesto = ($subtotal * $impuesto) / 100;
$monto_tarifa = ($subtotal * $tarifa_servicio) / 100;
$total = $subtotal + $monto_impuesto + $monto_tarifa;
```

#### C. Registra en BD

```sql
-- Tabla VENTAS
INSERT INTO ventas (
    empresa_id,
    cliente_id,
    user_id,
    caja_id,
    comprobante_id,
    numero_comprobante,
    metodo_pago,
    fecha_hora,
    subtotal,
    impuesto,
    total,
    monto_recibido,
    vuelto_entregado,
    tarifa_servicio,        ← NUEVO: porcentaje
    monto_tarifa,           ← NUEVO: monto calculado
    stripe_payment_intent_id ← NUEVO: para Stripe
) VALUES (...);

-- Tabla PRODUCTO_VENTA (pivot con detalle)
INSERT INTO producto_venta (
    venta_id,
    producto_id,
    cantidad,
    precio_venta,
    tarifa_unitaria    ← NUEVO: para auditoría
) VALUES (1, 5, 2, 25.00, 1.75);

-- Tabla MOVIMIENTOS (movimiento de caja)
INSERT INTO movimientos (
    empresa_id,
    caja_id,
    venta_id,           ← NUEVO: trazabilidad
    tipo,
    descripcion,
    monto,              ← Usa el TOTAL (59.25)
    metodo_pago
) VALUES (1, 1, 1, 'VENTA', 'Venta POS', 59.25, 'EFECTIVO');
```

#### D. Pago y Cierre de Venta

**Opción 1: Efectivo**
```
Monto Total: 59.25
Monto Recibido: 100.00
    ↓
Vuelto = 100.00 - 59.25 = 40.75
    ↓
Registra en MOVIMIENTOS
    ↓
Actualiza saldo de caja
```

**Opción 2: Tarjeta (futuro con Stripe)**
```
Sistema prepara PAYMENT_TRANSACTION
    ↓
Crea PaymentIntent en Stripe
    ↓
Guarda stripe_payment_intent_id en VENTAS
    ↓
Usuario completa pago en terminal/app
    ↓
Webhook confirma
    ↓
Actualiza payment_transaction status = 'SUCCESS'
```

### 4️⃣ Cierre de Caja

```
Cajero hace clic "Cerrar Caja"
    ↓
Sistema suma todos MOVIMIENTOS de la caja del día:
    - Venta 1: +59.25 (VENTA)
    - Venta 2: +45.00 (VENTA)
    - Retiro: -10.00 (RETIRO)
    ↓
saldo_final = saldo_inicial + total_movimientos
            = 500.00 + 59.25 + 45.00 - 10.00
            = 594.25
    ↓
Registra fecha_hora_cierre = NOW()
    ↓
estado = false
    ↓
Caja cerrada, reporte generado
```

**Query para calcular cierre:**
```php
$movimientos = Movimiento::where('caja_id', $cajaId)
    ->where('created_at', '>=', $caja->fecha_hora_apertura)
    ->get();

$monto_movimientos = $movimientos->sum(fn($m) => 
    $m->tipo === 'VENTA' ? $m->monto : -$m->monto
);

$caja->saldo_final = $caja->saldo_inicial + $monto_movimientos;
```

---

## 💰 Tarifa por Servicio (Service Fee)

### Concepto

La tarifa por servicio es un porcentaje adicional sobre el subtotal de la venta. Es común en:
- Establecimientos con pago con tarjeta
- Pedidos a domicilio
- Servicios adicionales

**Ejemplo Real:**
```
Cliente compra Popcorn + Bebida = 50.00 (subtotal)
Impuesto (15%) = 7.50
Tarifa por servicio (3.50%) = 1.75
─────────────────────────
TOTAL A PAGAR = 59.25
```

### Almacenamiento en BD

```sql
-- Tabla VENTAS
ALTER TABLE ventas ADD (
    tarifa_servicio DECIMAL(5,2) COMMENT 'Porcentaje, ej: 3.50',
    monto_tarifa DECIMAL(10,2) COMMENT 'Monto calculado'
);

-- Tabla EMPRESA (opcional, para tarifa por defecto)
ALTER TABLE empresa ADD 
    tarifa_servicio_defecto DECIMAL(5,2) DEFAULT 3.00;

-- Tabla PRODUCTO_VENTA (para registro detallado)
ALTER TABLE producto_venta ADD 
    tarifa_unitaria DECIMAL(10,2) COMMENT 'Tarifa aplicada a este item';
```

### Cálculo Programático

```php
// Modelo Venta
class Venta extends Model {
    
    /**
     * Calcula y asigna tarifa por servicio
     */
    public function calcularTarifa($porcentaje = null) {
        $porcentaje = $porcentaje ?? $this->empresa->tarifa_servicio_defecto;
        
        $this->tarifa_servicio = $porcentaje;
        $this->monto_tarifa = ($this->subtotal * $porcentaje) / 100;
        
        return $this->monto_tarifa;
    }
    
    /**
     * Accesor para total (subtotal + impuesto + tarifa)
     */
    public function getTotalAttribute() {
        return $this->subtotal + $this->impuesto + $this->monto_tarifa;
    }
    
    /**
     * Scope para sumar montos de tarifa por período
     */
    public function scopeTarifaEnPeriodo($query, $desde, $hasta) {
        return $query
            ->whereBetween('created_at', [$desde, $hasta])
            ->sum('monto_tarifa');
    }
}
```

### Reportes de Tarifa

```php
// Controller para reportes
public function reporteTarifa(Request $request) {
    $empresa = Auth::user()->empresa;
    
    $ventas = Venta::where('empresa_id', $empresa->id)
        ->whereBetween('created_at', [
            $request->desde, 
            $request->hasta
        ])
        ->get();
    
    $total_tarifas = $ventas->sum('monto_tarifa');
    $cantidad_ventas = $ventas->count();
    $tarifa_promedio = $total_tarifas / $cantidad_ventas;
    
    return response()->json([
        'total_tarifas' => $total_tarifas,
        'cantidad_ventas' => $cantidad_ventas,
        'tarifa_promedio' => $tarifa_promedio,
        'detalle' => $ventas->map(fn($v) => [
            'venta_id' => $v->id,
            'tarifa_servicio' => $v->tarifa_servicio,
            'monto_tarifa' => $v->monto_tarifa,
            'fecha' => $v->fecha_hora
        ])
    ]);
}
```

---

## 💳 Preparación para Stripe

### Estado Actual

✅ **Listo para implementar:**
- Campo `stripe_payment_intent_id` en tabla `ventas`
- Tabla `stripe_configs` para almacenar claves por empresa
- Tabla `payment_transactions` para registrar todos los pagos
- Estructura de modelos preparada

❌ **NO implementado aún:**
- SDK de Stripe
- Controladores de pago
- Webhooks
- Lógica de captura

### Arquitectura de Stripe

```
┌─────────────────┐
│   Frontend      │
│   (POS Client)  │
└────────┬────────┘
         │ 1. Usuario selecciona pagar con tarjeta
         ↓
┌──────────────────────────┐
│   Backend CinemaPOS      │
│  (REST API / Laravel)    │
└────────┬─────────────────┘
         │ 2. Crea PaymentIntent
         ↓
    ┌────────────────────┐
    │  Stripe API        │
    │  (Live/Test)       │
    └────────┬───────────┘
             │ 3. Retorna PaymentIntent
             ↓
┌────────────────────────────────┐
│  stripe_configs               │
├─ empresa_id = 1              │
├─ public_key = pk_live_...    │
├─ secret_key = sk_live_... ✓  │
├─ webhook_secret = whsec_..  │
└────────────────────────────────┘
         │ 4. Frontend usa public_key
         ↓
┌──────────────────────────┐
│  Stripe Elements / SDK   │
│  (Card form)             │
└────────┬─────────────────┘
         │ 5. Usuario ingresa tarjeta
         ↓
    ┌────────────────────┐
    │  Stripe API        │
    │  Confirma pago     │
    └────────┬───────────┘
             │ 6. Webhook notifica backend
             ↓
┌──────────────────────────┐
│  payment_transactions    │
├─ status = 'SUCCESS'     │
├─ stripe_charge_id = ... │
└──────────────────────────┘
         │ 7. Marca venta como pagada
         ↓
┌──────────────────────────┐
│  ventas                  │
├─ status = 'PAID'        │
└──────────────────────────┘
```

### Tabla `stripe_configs`

```sql
CREATE TABLE stripe_configs (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    empresa_id BIGINT UNIQUE NOT NULL,
    public_key VARCHAR(255) NOT NULL,
    secret_key TEXT NOT NULL,           -- Encriptada
    webhook_secret TEXT NOT NULL,       -- Encriptada
    test_mode BOOLEAN DEFAULT true,
    enabled BOOLEAN DEFAULT false,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

**Uso en código (cuando se implemente):**
```php
class StripePaymentService {
    protected $config;
    
    public function __construct($empresaId) {
        $this->config = StripeConfig::where('empresa_id', $empresaId)
            ->firstOrFail();
        
        \Stripe\Stripe::setApiKey(
            decrypt($this->config->secret_key)
        );
    }
    
    public function crearPaymentIntent($venta) {
        return \Stripe\PaymentIntent::create([
            'amount' => (int)($venta->total * 100),
            'currency' => 'usd',
            'metadata' => [
                'venta_id' => $venta->id,
                'empresa_id' => $venta->empresa_id
            ]
        ]);
    }
}
```

### Tabla `payment_transactions`

```sql
CREATE TABLE payment_transactions (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    empresa_id BIGINT NOT NULL,
    venta_id BIGINT NOT NULL,
    payment_method ENUM('CASH', 'CARD', 'STRIPE', 'OTHER'),
    stripe_payment_intent_id VARCHAR(255) NULL,
    stripe_charge_id VARCHAR(255) NULL,
    amount_paid DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'USD',
    status ENUM('PENDING', 'SUCCESS', 'FAILED', 'REFUNDED', 'CANCELLED'),
    metadata JSON NULL,
    error_message TEXT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Próximos Pasos (Fase 2)

1. **Instalar SDK:**
   ```bash
   composer require stripe/stripe-php
   ```

2. **Crear Servicio:**
   ```php
   app/Services/StripePaymentService.php
   ```

3. **Crear Controlador:**
   ```php
   app/Http/Controllers/StripePaymentController.php
   ```

4. **Endpoints a implementar:**
   - `POST /api/stripe/create-payment-intent`
   - `POST /api/stripe/confirm-payment`
   - `POST /webhook/stripe` (webhook endpoint)

5. **Configurar en env:**
   ```env
   STRIPE_PUBLIC_KEY=pk_live_...
   STRIPE_SECRET_KEY=sk_live_...
   STRIPE_WEBHOOK_SECRET=whsec_...
   ```

---

## 🏢 Gestión de Empresa y Usuarios

### Modelo de Multi-Tenancy

CinemaPOS usa **row-level security** mediante `empresa_id`:

```
┌─────────────────────────────────┐
│      EMPRESA 1: Cinema ABC      │
├─────────────────────────────────┤
│ - 3 usuarios (cajeros)          │
│ - 5 cajas                       │
│ - 50 productos                  │
│ - 2500 ventas                   │
└─────────────────────────────────┘

┌─────────────────────────────────┐
│      EMPRESA 2: Cinema XYZ      │
├─────────────────────────────────┤
│ - 5 usuarios (cajeros)          │
│ - 8 cajas                       │
│ - 75 productos                  │
│ - 5000 ventas                   │
└─────────────────────────────────┘

Separación: @BD automática con WHERE empresa_id
Aislamiento: Usuario de Empresa 1 no ve datos de Empresa 2
```

### Flujo de Login

```php
// LoginController
public function login(Request $request) {
    $validated = $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);
    
    if (!Auth::attempt($validated)) {
        return response()->json(['error' => 'Invalid'], 401);
    }
    
    $user = Auth::user();
    
    // CLAVE: Cargar empresa_id
    $user->load('empresa');
    
    // Guardar en sesión para filtros posteriores
    session(['empresa_id' => $user->empresa_id]);
    
    return response()->json([
        'user' => $user,
        'empresa' => $user->empresa,
        'token' => $user->createToken('api_token')->plainTextToken
    ]);
}
```

### Middleware de Multi-Tenancy

```php
// app/Http/Middleware/EnsureEmpresaAccess.php
class EnsureEmpresaAccess {
    public function handle($request, $next) {
        if (!Auth::check()) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }
        
        $user = Auth::user();
        
        if (!$user->empresa_id) {
            return response()->json(['error' => 'No empresa assigned'], 403);
        }
        
        // Inyecta empresa_id en todas las queries posteriores
        request()->merge(['empresa_id' => $user->empresa_id]);
        
        return $next($request);
    }
}
```

### Scopes en Modelos

```php
// app/Models/Venta.php
class Venta extends Model {
    
    /**
     * Scope global para filtrar por empresa actual
     */
    public static function booted() {
        static::addGlobalScope('empresa', function ($query) {
            $query->where('empresa_id', Auth::user()->empresa_id ?? 1);
        });
    }
    
    /**
     * Scope manual para queries específicas
     */
    public function scopeForEmpresa($query, $empresaId) {
        return $query->where('empresa_id', $empresaId);
    }
}
```

### Gestión de Permisos

```php
// Usar Spatie Laravel Permission
// Ya está instalado en composer.json

// Asignar roles a usuario
$user->assignRole('cashier');           // Cajero
$user->assignRole('manager');           // Gerente
$user->assignRole('admin');             // Admin sistema

// Permiso específico
$user->givePermissionTo('ver_reportes');
$user->givePermissionTo('gestionar_caja');

// Verificar en controllers
if ($user->hasRole('cashier')) {
    // Usuario es cajero
}

if ($user->can('gestionar_caja')) {
    // Puede gestionar caja
}
```

---

## 📊 Reportes y Auditoría

### Reportes Disponibles

1. **Reporte de Ventas por Período**
   ```php
   $ventas = Venta::whereBetween('created_at', [$desde, $hasta])->get();
   ```

2. **Reporte de Tarifa Recaudada**
   ```php
   $tarifas = Venta::sum('monto_tarifa');
   ```

3. **Cierre de Caja**
   ```php
   $cierre = Caja::with('movimientos')->find($cajaId);
   ```

4. **Kardex de Inventario**
   ```php
   $kardex = Kardex::where('empresa_id', $empresaId)->get();
   ```

### Activity Log

Tabla `activity_logs` registra:
- Quién hizo cambios
- Qué cambió
- Cuándo cambió
- En qué empresa

```sql
INSERT INTO activity_logs (
    user_id, 
    empresa_id, 
    loggable_type,  -- 'App\Models\Venta'
    loggable_id,    -- ID de venta
    event,          -- 'created', 'updated', 'deleted'
    properties      -- JSON con cambios
);
```

---

## 🔒 Seguridad

### Validaciones

- ✅ `empresa_id` validado en cada request
- ✅ Usuarios solo ven datos de su empresa
- ✅ Contraseñas encriptadas (bcrypt)
- ✅ Tokens Sanctum para API
- ✅ Rate limiting en endpoints críticos

### Encriptación

```php
// Campos sensibles encriptados
protected $encrypted = [
    'stripe_configs.secret_key',
    'stripe_configs.webhook_secret',
];

// En modelo
public function getSecretKeyAttribute($value) {
    return decrypt($value);
}

public function setSecretKeyAttribute($value) {
    $this->attributes['secret_key'] = encrypt($value);
}
```

### Auditoría de Cambios

Cada cambio en datos críticos se registra:
- Quién lo hizo
- Qué cambió
- Cuándo
- En qué empresa

---

## 🚀 Instalación y Setup

### Requisitos

- PHP 8.2+
- MySQL 8.0+
- Composer
- Node.js (para frontend)

### Pasos

1. **Clonar y dependencias:**
   ```bash
   git clone ...
   cd Punto-de-Venta
   composer install
   npm install
   ```

2. **Configurar env:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Base de datos:**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

4. **Servidor:**
   ```bash
   php artisan serve
   npm run dev
   ```

---

## 📝 Migraciones Implementadas

### Phase 1: Multi-Tenancy (Enero 2026)

| Migración | Descripción |
|-----------|-------------|
| `2026_01_30_114320_add_empresa_id_to_users_table` | Vincula usuarios a empresa |
| `2026_01_30_114325_add_empresa_id_to_empleados_table` | Empleados por empresa |
| `2026_01_30_114330_add_empresa_id_to_cajas_table` | Cajas por empresa |
| `2026_01_30_114335_update_movimientos_table` | Movimientos con venta_id |
| `2026_01_30_114340_add_fields_to_ventas_table` | Tarifa + Stripe fields |
| `2026_01_30_114345_add_empresa_id_to_productos_table` | Productos por empresa |
| `2026_01_30_114350_add_empresa_id_to_compras_table` | Compras por empresa |
| `2026_01_30_114355_add_empresa_id_to_clientes_table` | Clientes por empresa |
| `2026_01_30_114400_add_empresa_id_to_proveedores_table` | Proveedores por empresa |
| `2026_01_30_114405_add_empresa_id_to_inventarios_table` | Inventario por empresa |
| `2026_01_30_114410_add_empresa_id_to_kardexes_table` | Kardex por empresa |
| `2026_01_30_114415_add_tarifa_unitaria_to_producto_venta_table` | Tarifa en detalle |
| `2026_01_30_114420_create_stripe_configs_table` | Config Stripe por empresa |
| `2026_01_30_114425_create_payment_transactions_table` | Registro de transacciones |

---

## 📞 Soporte y Contacto

Para preguntas sobre la arquitectura o implementación:
- Contactar arquitecto responsable
- Revisar documentación técnica en `CINEMAPOSPWD.md`

---

## 📄 Licencia

MIT License - Ver archivo `LICENSE`

---

**Versión:** 1.0  
**Último Update:** 30 de enero de 2026  
**Arquitecto:** Senior SaaS/POS  
**Estado:** Producción
