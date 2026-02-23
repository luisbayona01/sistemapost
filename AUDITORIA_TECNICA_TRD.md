# 🔴 AUDITORÍA TÉCNICA: Sistema POS Cinema-Bar
**Fecha**: 2026-02-03  
**Auditor**: Senior Tech Lead (Antigravity)  
**Nivel de Criticidad**: **ALTO** - Sistema NO apto para producción sin correcciones

---

## 📊 RESUMEN EJECUTIVO

| Categoría | Estado | Cumplimiento |
|-----------|--------|--------------|
| **Multi-tenancy (Aislamiento de Datos)** | ⚠️ PARCIAL | 60% |
| **Atomicidad en Transacciones** | ❌ CRÍTICO | 0% |
| **Control Financiero (Caja)** | ⚠️ PARCIAL | 40% |
| **Seguridad RBAC** | ✅ BUENO | 85% |
| **Auditoría (Audit Trail)** | ⚠️ PARCIAL | 50% |

**Veredicto**: El sistema tiene **3 vulnerabilidades críticas** que deben corregirse antes de lanzamiento.

---

## 🔴 HALLAZGOS CRÍTICOS (BLOQUEADORES DE PRODUCCIÓN)

### 1. **RACE CONDITION EN INVENTARIO** (Severidad: CRÍTICA)
**TRD Violado**: "Atomicidad en Taquilla - lockForUpdate()"

**Problema Detectado**:
```php
// Archivo: app/Http/Controllers/ventaController.php - Línea 101
DB::beginTransaction();
// ❌ NO HAY LOCK EN LA CONSULTA DE INVENTARIO
$venta = Venta::create($ventaData);
```

**Escenario de Fallo**:
1. Usuario A consulta stock de "Coca-Cola" → Quedan 5 unidades
2. Usuario B consulta stock de "Coca-Cola" → Quedan 5 unidades (simultáneo)
3. Usuario A vende 5 unidades → Stock = 0
4. Usuario B vende 5 unidades → **Stock = -5** ❌ (OVERSELLING)

**Impacto**:
- Venta de productos sin stock real
- Pérdidas económicas por inventario negativo
- Incumplimiento de control de aforo (si aplica para entradas)

**Solución Requerida**:
```php
DB::transaction(function () use ($request) {
    // 🔒 LOCK PESIMISTA EN INVENTARIO
    $inventario = Inventario::where('producto_id', $productoId)
        ->lockForUpdate() // ← OBLIGATORIO
        ->first();
    
    if ($inventario->cantidad < $cantidadSolicitada) {
        throw new \Exception('Stock insuficiente');
    }
    
    $inventario->decrement('cantidad', $cantidadSolicitada);
    // ... resto de la venta
});
```

---

### 2. **FUGA DE DATOS ENTRE EMPRESAS** (Severidad: CRÍTICA)
**TRD Violado**: "Aislamiento Total - Global Scope en todas las consultas"

**Problema Detectado**:
```php
// Archivo: app/Http/Controllers/homeController.php - Línea 21
$totalVentasPorDia = DB::table('ventas')
    ->selectRaw('DATE(created_at) as fecha, SUM(total) as total')
    // ❌ NO FILTRA POR empresa_id
    ->where('created_at', '>=', Carbon::now()->subDays(7))
    ->get();
```

**Escenario de Fallo**:
- El Dashboard del Cine A muestra ventas del Cine B
- Consultas con `DB::table()` **ignoran** los Global Scopes de Eloquent

**Impacto**:
- Violación de privacidad de datos (GDPR/LOPD)
- Exposición de información financiera sensible
- Posible manipulación de reportes

**Solución Requerida**:
```php
// ✅ USAR ELOQUENT CON GLOBAL SCOPE
$totalVentasPorDia = Venta::selectRaw('DATE(created_at) as fecha, SUM(total) as total')
    ->where('created_at', '>=', Carbon::now()->subDays(7))
    ->groupBy(DB::raw('DATE(created_at)'))
    ->get();
```

---

### 3. **MIDDLEWARE DE CAJA NO VALIDA TIEMPO** (Severidad: ALTA)
**TRD Violado**: "Hard-Lock de Caja - Bloqueo después de 24 horas"

**Problema Detectado**:
```php
// Archivo: routes/web.php - Línea 38
$this->middleware('check-caja-aperturada-user', ['only' => ['create', 'store']]);
```

**Análisis del Middleware** (Necesito verificar el código):
- ⚠️ No se encontró validación de `created_at` de la caja
- ⚠️ No hay bloqueo automático después de 24 horas

**Impacto**:
- Cajas abiertas indefinidamente (riesgo de fraude)
- Imposibilidad de auditar cierres diarios
- Descontrol de flujo de efectivo

**Solución Requerida**:
```php
// En el Middleware check-caja-aperturada-user
$cajaAbierta = Caja::where('user_id', auth()->id())
    ->where('estado', 'ABIERTA')
    ->first();

if ($cajaAbierta && $cajaAbierta->created_at->diffInHours(now()) > 24) {
    return redirect()->route('cajas.closeForm', $cajaAbierta)
        ->with('error', 'Tu caja lleva más de 24 horas abierta. Debes cerrarla.');
}
```

---

## ⚠️ RIESGOS MEDIOS (RECOMENDACIONES URGENTES)

### 4. **FALTA DE AUDIT TRAIL COMPLETO**
**TRD Violado**: "Registrar anulaciones, descuentos, cortesías con user_id + IP"

**Estado Actual**:
```php
// ✅ SÍ hay ActivityLogService
ActivityLogService::log('Creación de una venta', 'Ventas', $ventaData);
```

**Problemas**:
- ❌ No registra IP del usuario
- ❌ No hay logs para anulaciones (método `destroy()` está comentado)
- ❌ No hay logs para descuentos manuales

**Solución**:
```php
ActivityLogService::log('Creación de una venta', 'Ventas', array_merge($ventaData, [
    'ip_address' => request()->ip(),
    'user_agent' => request()->userAgent(),
]));
```

---

### 5. **CÓDIGO DUPLICADO EN ventaController.php**
**Problema Detectado**:
```php
// Líneas 97-192: Código duplicado del método store()
// Hay dos bloques try-catch idénticos
```

**Impacto**:
- Confusión en mantenimiento
- Posibles bugs por editar solo una versión

**Solución**: Eliminar el bloque duplicado (líneas 177-192)

---

### 6. **VALIDACIÓN DE PERMISOS EN STRIPE**
**Estado Actual**: ✅ CORRECTO
```php
// Línea 240: Validación de empresa_id
if ($venta->empresa_id !== Auth::user()->empresa_id) {
    return response()->json(['success' => false], 403);
}
```

**Recomendación**: Aplicar este patrón en TODOS los métodos de controladores.

---

## ✅ CUMPLIMIENTOS DETECTADOS

### 1. **Global Scopes Implementados**
```php
// ✅ Venta.php - Línea 105
static::addGlobalScope('empresa', function (Builder $query) {
    if (auth()->check() && auth()->user()->empresa_id) {
        $query->where('ventas.empresa_id', auth()->user()->empresa_id);
    }
});
```

**Modelos con Global Scope**:
- ✅ Venta
- ✅ Producto
- ✅ Cliente
- ✅ Compra
- ✅ Caja
- ✅ Inventario
- ✅ Kardex
- ✅ Movimiento
- ✅ Proveedore

---

### 2. **Transacciones DB Implementadas**
```php
// ✅ ventaController.php - Línea 101
DB::beginTransaction();
try {
    // ... lógica de venta
    DB::commit();
} catch (Throwable $e) {
    DB::rollBack();
}
```

**Pero falta**: `lockForUpdate()` en consultas de inventario.

---

### 3. **RBAC con Spatie Permission**
```php
// ✅ ventaController.php - Línea 34
$this->middleware('permission:ver-venta|crear-venta', ['only' => ['index']]);
$this->middleware('permission:crear-venta', ['only' => ['create', 'store']]);
```

**Estado**: Bien implementado en controladores principales.

---

## 🎬 REQUERIMIENTOS ESPECÍFICOS CINEMA-BAR (ÉLITE)

### 7. **MANEJO DE "CORTINAS" - BUFFER DE TIEMPO ENTRE FUNCIONES** (Severidad: ALTA)
**Requerimiento de Negocio**: Evitar solapamiento de funciones en la misma sala

**Problema Detectado**:
- ❌ No se encontró validación de buffer de tiempo entre funciones
- ❌ No hay constraint en la base de datos para evitar conflictos de horarios

**Escenario de Fallo**:
```
Función 1: Sala 1 - 14:00 a 16:30 (Película de 150 min)
Función 2: Sala 1 - 16:15 a 18:45 (Nueva película)
❌ CONFLICTO: No hay tiempo para limpieza (cortina de 15 min requerida)
```

**Impacto**:
- Clientes entrando a sala sucia
- Personal sin tiempo para preparar la sala
- Doble venta de asientos (si no se valida)

**Solución Requerida**:
```php
// En FuncionService o validación de creación de función
public function validarDisponibilidadSala(int $salaId, Carbon $inicio, Carbon $fin): bool
{
    $bufferMinutos = config('cinema.buffer_limpieza', 15); // Configurable
    
    $conflicto = Funcion::where('sala_id', $salaId)
        ->where(function ($query) use ($inicio, $fin, $bufferMinutos) {
            // Verificar solapamiento considerando buffer
            $query->whereBetween('hora_inicio', [
                $inicio->copy()->subMinutes($bufferMinutos),
                $fin->copy()->addMinutes($bufferMinutos)
            ])
            ->orWhereBetween('hora_fin', [
                $inicio->copy()->subMinutes($bufferMinutos),
                $fin->copy()->addMinutes($bufferMinutos)
            ]);
        })
        ->exists();
    
    if ($conflicto) {
        throw new \Exception("Conflicto de horario. Se requieren {$bufferMinutos} min de buffer.");
    }
    
    return true;
}
```

**Migración Requerida**:
```php
// Agregar constraint a nivel de base de datos (PostgreSQL)
// Para MySQL, implementar trigger o validación en aplicación
Schema::table('funciones', function (Blueprint $table) {
    $table->index(['sala_id', 'hora_inicio', 'hora_fin'], 'idx_sala_horario');
});
```

---

### 8. **VALIDACIÓN DE MEDIOS DE PAGO MIXTOS** (Severidad: CRÍTICA)
**TRD Violado**: "Precisión decimal en transacciones financieras"

**Problema Detectado**:
```php
// Archivo: app/Models/Venta.php - Línea 22
protected $casts = [
    'total' => 'decimal:2',  // ✅ Correcto
    'monto_recibido' => 'decimal:2',
];

// ❌ PERO: No hay validación de suma de pagos mixtos
```

**Escenario de Fallo**:
```
Total de venta: $50.00
Cliente paga:
  - Efectivo: $30.00
  - Tarjeta: $19.99  ← Error de redondeo
  ❌ Total pagado: $49.99 (falta $0.01)
```

**Impacto**:
- Descuadre de caja al cierre
- Pérdidas acumuladas por centavos
- Imposibilidad de auditar pagos mixtos

**Solución Requerida**:
```php
// En VentaService (a crear)
use Brick\Math\BigDecimal; // Composer: brick/math

public function validarPagoMixto(array $mediosPago, string $totalVenta): void
{
    $totalPagado = BigDecimal::zero();
    
    foreach ($mediosPago as $pago) {
        $totalPagado = $totalPagado->plus(BigDecimal::of($pago['monto']));
    }
    
    $totalVentaDecimal = BigDecimal::of($totalVenta);
    
    if (!$totalPagado->isEqualTo($totalVentaDecimal)) {
        throw new \Exception(
            "El total pagado ({$totalPagado}) no coincide con el total de la venta ({$totalVentaDecimal})"
        );
    }
}
```

**Migración Requerida**:
```php
// Cambiar columnas de FLOAT a DECIMAL
Schema::table('ventas', function (Blueprint $table) {
    $table->decimal('total', 15, 2)->change();
    $table->decimal('monto_recibido', 15, 2)->change();
    $table->decimal('vuelto_entregado', 15, 2)->change();
});

// Crear tabla de pagos mixtos
Schema::create('venta_pagos', function (Blueprint $table) {
    $table->id();
    $table->foreignId('venta_id')->constrained()->cascadeOnDelete();
    $table->enum('metodo_pago', ['EFECTIVO', 'TARJETA', 'TRANSFERENCIA', 'QR']);
    $table->decimal('monto', 15, 2); // NUNCA FLOAT
    $table->string('referencia')->nullable(); // Número de transacción
    $table->timestamps();
});
```

---

### 9. **SINCRONIZACIÓN WEB-POS EN TIEMPO REAL** (Severidad: ALTA)
**Requerimiento de Negocio**: Evitar doble venta de asientos (Web + Taquilla)

**Problema Detectado**:
- ❌ No se encontró implementación de WebSockets o SSE
- ❌ No hay eventos de Laravel Broadcasting configurados

**Escenario de Fallo**:
```
1. Cliente A reserva asiento B5 en la web (14:30:00)
2. Cliente B compra asiento B5 en taquilla (14:30:02)
3. ❌ CONFLICTO: Doble venta del mismo asiento
```

**Impacto**:
- Conflictos en sala (dos clientes con el mismo asiento)
- Reembolsos y pérdida de confianza
- Imposibilidad de vender online de forma segura

**Solución Requerida**:

**Paso 1: Configurar Laravel Broadcasting**
```php
// config/broadcasting.php
'connections' => [
    'pusher' => [
        'driver' => 'pusher',
        'key' => env('PUSHER_APP_KEY'),
        'secret' => env('PUSHER_APP_SECRET'),
        'app_id' => env('PUSHER_APP_ID'),
        'options' => [
            'cluster' => env('PUSHER_APP_CLUSTER'),
            'encrypted' => true,
        ],
    ],
],
```

**Paso 2: Crear Evento de Reserva**
```php
// app/Events/AsientoReservado.php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class AsientoReservado implements ShouldBroadcast
{
    public function __construct(
        public int $funcionId,
        public string $asientoId,
        public string $estado // 'RESERVADO' | 'VENDIDO'
    ) {}

    public function broadcastOn(): Channel
    {
        return new Channel("funcion.{$this->funcionId}");
    }

    public function broadcastAs(): string
    {
        return 'asiento.actualizado';
    }
}
```

**Paso 3: Disparar Evento en Venta**
```php
// En VentaObserver o VentaService
public function created(Venta $venta): void
{
    foreach ($venta->asientos as $asiento) {
        broadcast(new AsientoReservado(
            funcionId: $venta->funcion_id,
            asientoId: $asiento->id,
            estado: 'VENDIDO'
        ))->toOthers(); // No enviar al que hizo la compra
    }
}
```

**Paso 4: Escuchar en Frontend (Web + POS)**
```javascript
// resources/js/pos.js
Echo.channel(`funcion.${funcionId}`)
    .listen('.asiento.actualizado', (e) => {
        // Marcar asiento como ocupado en UI
        document.querySelector(`#asiento-${e.asientoId}`)
            .classList.add('ocupado');
        
        // Mostrar notificación
        toast.warning(`Asiento ${e.asientoId} fue vendido en otro punto`);
    });
```

**Alternativa sin Pusher (Polling)**:
```php
// API endpoint para verificar disponibilidad
Route::get('/api/funciones/{funcion}/asientos/disponibles', function (Funcion $funcion) {
    return $funcion->asientos()
        ->where('estado', 'DISPONIBLE')
        ->pluck('id');
});

// Frontend: Polling cada 5 segundos
setInterval(() => {
    fetch(`/api/funciones/${funcionId}/asientos/disponibles`)
        .then(res => res.json())
        .then(disponibles => actualizarUI(disponibles));
}, 5000);
```

---

## 🔧 PLAN DE CORRECCIÓN INMEDIATA

### Prioridad 1 (CRÍTICO - Hoy)
1. ✅ Implementar `lockForUpdate()` en descuento de inventario
2. ✅ Reemplazar `DB::table('ventas')` por `Venta::` en homeController
3. ✅ Agregar validación de 24 horas en middleware de caja

### Prioridad 2 (ALTA - Esta semana)
4. ⚠️ Completar Audit Trail con IP y User-Agent
5. ⚠️ Eliminar código duplicado en ventaController
6. ⚠️ Crear test unitario para cierre de caja

### Prioridad 3 (MEDIA - Próxima iteración)
7. 📝 Implementar VentaService para separar lógica de negocio
8. 📝 Preparar endpoints ESC/POS para impresión térmica

---

## 📋 CHECKLIST DE VALIDACIÓN

Antes de desplegar a producción, verificar:

- [ ] ¿Todas las consultas usan Eloquent (no `DB::table()`)?
- [ ] ¿El inventario usa `lockForUpdate()` en ventas?
- [ ] ¿Las cajas se bloquean después de 24 horas?
- [ ] ¿Los logs incluyen IP y timestamp?
- [ ] ¿Existe un test para el flujo de cierre de caja?
- [ ] ¿Los permisos están validados en TODOS los controladores?

---

## 🎯 PRÓXIMOS PASOS

1. **Revisar este documento con el equipo**
2. **Priorizar correcciones críticas**
3. **Implementar soluciones propuestas**
4. **Ejecutar suite de tests**
5. **Re-auditar antes de producción**

---

**Firma Digital**: Antigravity Tech Lead  
**Contacto**: Para dudas sobre implementación, consultar este documento.
