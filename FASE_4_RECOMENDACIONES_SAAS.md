# FASE 4: Recomendaciones Hardening SaaS - CinemaPOS

**Contexto:** Post-auditoría POS, pre-producción  
**Objetivo:** Mejoras sin breaking changes (próximas fases)  
**Target:** SaaS multi-tenant, POS crítico  
**Nivel Seguridad:** Enterprise Ready

---

## 🔒 HARDENING MULTIEMPRESA

### 1. **Request Validation Middleware**

**Estado Actual:**
- Global scope en modelos (✅ BIEN)
- Middleware de autorización por ruta (✅ BIEN)
- Validación empresa en middleware (✅ RECIENTE)

**Mejora Propuesta:**
```php
// app/Http/Middleware/EnsureUserBelongsToEmpresa.php
class EnsureUserBelongsToEmpresa
{
    public function handle(Request $request, Closure $next)
    {
        // Validar que empresa_id en request = auth user empresa_id
        if ($request->filled('empresa_id') && 
            $request->empresa_id != auth()->user()->empresa_id) {
            abort(403, 'No perteneces a esta empresa');
        }
        return $next($request);
    }
}
```

**Aplicación:**
- POST/PUT en modelos sensibles (caja, venta)
- Previene inyección de empresa_id

**Prioridad:** 🟡 ALTA | **Esfuerzo:** 1h

---

### 2. **API Rate Limiting por Empresa**

**Estado Actual:**
- Sin rate limiting

**Propuesta:**
```php
// config/rate-limiting.php
'venta-create' => '100:1440', // 100 ventas/día por usuario
'caja-open' => '1:1440',       // 1 caja/día por usuario
'inventory-update' => '500:1',  // 500 actualizaciones/minuto
```

**Implementación:**
```php
// En controller:
$this->middleware('throttle:venta-create')->only(['store']);
```

**Prioridad:** 🟡 ALTA | **Esfuerzo:** 2h

---

## 📊 AUDITORÍA Y COMPLIANCE

### 3. **Activity Log Completo**

**Estado Actual:**
- Parcial en controladores

**Mejora:**
```php
// Registrar TODAS las acciones críticas
ActivityLogService::log('Acción', 'Módulo', [
    'user_id' => Auth::id(),
    'empresa_id' => auth()->user()->empresa_id,
    'ip' => request()->ip(),
    'cambios' => $changes, // JSON de qué cambió
    'timestamp' => now(),
]);
```

**Puntos Críticos:**
- Creación/eliminación de caja
- Cada venta
- Cierre de caja
- Cambio de inventario
- Accesos denegados (403/401)

**Prioridad:** 🟢 CRÍTICA | **Esfuerzo:** 4h

---

### 4. **Audit Trail (BD separada)**

**Propuesta:**
```sql
CREATE TABLE audit_logs (
    id BIGINT PRIMARY KEY,
    user_id BIGINT,
    empresa_id BIGINT,
    action VARCHAR(100),
    module VARCHAR(50),
    old_values JSON,
    new_values JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    status_code INT,
    response_time_ms INT,
    created_at TIMESTAMP
);

CREATE INDEX idx_audit_empresa ON audit_logs(empresa_id, created_at);
CREATE INDEX idx_audit_user ON audit_logs(user_id, created_at);
```

**Queries Soportadas:**
- "¿Quién creó la venta XYZ?"
- "¿Qué pasó con el inventario en las últimas 24h?"
- "¿Cuántas transacciones fallidas hoy?"

**Prioridad:** 🟡 ALTA | **Esfuerzo:** 6h (con migration + API)

---

## 🛡️ SEGURIDAD DE DATOS

### 5. **Encryption at Rest (Sensible Data)**

**Propuesta:**
```php
// Solo para producción
// app/Casts/EncryptedCast.php

Venta::class:
  - 'numero_comprobante' → Encriptar (GDPR compliance)
  - 'monto_recibido' → Opcional
  - 'vuelto_entregado' → Opcional

Movimiento::class:
  - 'monto' → Encriptar
```

**Implementación:**
```php
// app/Models/Venta.php
protected $encrypted = ['numero_comprobante'];
```

**Prioridad:** 🟡 MEDIA | **Esfuerzo:** 3h

---

### 6. **API Secrets Rotation**

**Estado Actual:**
- .env file (estático)

**Propuesta:**
```php
// Guardar secretos en bóveda (AWS Secrets Manager / HashiCorp Vault)
$secret = \Illuminate\Support\Facades\Crypt::decrypt(
    env('STRIPE_SECRET') // Rotado diariamente
);
```

**Beneficios:**
- Rotación automática
- Sin redeploy
- Auditable

**Prioridad:** 🟢 CRÍTICA (pre-prod) | **Esfuerzo:** 8h

---

## ⚡ PERFORMANCE & CACHING

### 7. **Query Caching - Datos Empresariales**

**Estado Actual:**
- Sin caché
- N+1 queries posibles

**Propuesta:**
```php
// En modelos
public function getEmpresaAttribute()
{
    return cache()->remember("empresa:{$this->empresa_id}", 3600, fn() =>
        Empresa::find($this->empresa_id)
    );
}

// En listeners
Movimiento::where('caja_id', $caja->id)->cache('movimientos:' . $caja->id, 300);
```

**Caché Invalidación:**
```php
// En Observer
public function updated(Empresa $empresa)
{
    cache()->forget("empresa:{$empresa->id}");
}
```

**Prioridad:** 🟡 ALTA | **Esfuerzo:** 4h

---

### 8. **Índices de BD Faltantes**

**Propuesta:**
```sql
-- Índices críticos para performance
CREATE INDEX idx_venta_empresa_user 
  ON ventas(empresa_id, user_id, created_at);

CREATE INDEX idx_caja_empresa_user 
  ON cajas(empresa_id, user_id, estado);

CREATE INDEX idx_movimiento_caja 
  ON movimientos(caja_id, tipo, created_at);

CREATE INDEX idx_inventario_producto 
  ON inventario(producto_id, empresa_id);

-- Para reporting
CREATE INDEX idx_venta_fecha 
  ON ventas(empresa_id, fecha_hora);
```

**Impact:** 10-50x más rápido en reportes

**Prioridad:** 🟢 CRÍTICA | **Esfuerzo:** 1h

---

## 🧪 TESTING STRATEGY

### 9. **Integration Tests (Flujo Completo)**

**Estado Actual:**
- Feature tests básicos

**Propuesta:**
```php
// tests/Integration/PosWorkflowTest.php
class PosWorkflowTest extends TestCase
{
    public function test_flujo_completo_venta()
    {
        // 1. Abrir caja
        // 2. Crear venta con 3 productos
        // 3. Verificar:
        //    - Movimiento creado (1x)
        //    - Inventario descontado (1x)
        //    - Caja saldo correcto
        //    - Kardex registrado
        //    - ActivityLog completo
    }
}
```

**Prioridad:** 🟡 ALTA | **Esfuerzo:** 8h

---

### 10. **Load Testing - Caja Simultánea**

**Propuesta:**
```php
// tests/Load/CajaLoadTest.php
/**
 * Test: 10 usuarios vendiendo simultáneamente
 * Verificar:
 * - Sin race conditions
 * - Stock consistente
 * - Transacciones atómicas
 */
```

**Tool:** Artillery / JMeter

**Prioridad:** 🟡 MEDIA (pre-producción) | **Esfuerzo:** 6h

---

## 📋 COMPLIANCE & REGULATIONS

### 11. **GDPR Compliance**

**Implementaciones:**
```php
// 1. Right to be forgotten
Route::delete('/user/{user}/delete-data', [UserController::class, 'purge']);

// 2. Data export
Route::get('/user/{user}/export-data', [UserController::class, 'export']);

// 3. Consent tracking
$user->consents()->create(['type' => 'data_processing']);

// 4. Retention policy
User::where('deleted_at', '<', now()->subYears(3))->forceDelete();
```

**Prioridad:** 🟢 CRÍTICA (Legal) | **Esfuerzo:** 12h

---

### 12. **PCI DSS (Payment Card Data)**

**Nota:** NO implementar Stripe aún, pero preparar:

```php
// NUNCA guardar PAN (número de tarjeta)
// NUNCA guardar CVV
// SOLO guardar último 4 dígitos + token

// En transacción:
PaymentTransaction::create([
    'venta_id' => $venta->id,
    'last_four' => '4242',
    'token' => $stripe_token, // Tokenizado
    // NO: 'card_number' o 'cvv'
]);
```

**Prioridad:** 🟢 CRÍTICA (Stripe) | **Esfuerzo:** 0 (futura fase)

---

## 🚨 MONITORING & ALERTING

### 13. **Error Tracking (Sentry / Rollbar)**

```php
// config/sentry.php
Sentry::init([
    'dsn' => env('SENTRY_DSN'),
    'environment' => env('APP_ENV'),
    'release' => app('version'),
    'before_send' => function ($event, $hint) {
        // No loguear datos sensibles
        if (str_contains($event->getTransactionName(), 'payment')) {
            return null;
        }
        return $event;
    }
]);
```

**Alertas:**
- Error rate > 1%
- Response time > 500ms
- Database connection failures
- Stock inconsistencies

**Prioridad:** 🟡 ALTA (prod) | **Esfuerzo:** 2h

---

### 14. **Health Checks**

```php
// routes/health.php
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'database' => DB::connection()->getPdo() ? 'ok' : 'fail',
        'cache' => Cache::put('health_check', 1, 10) ? 'ok' : 'fail',
        'queue' => Queue::connection() ? 'ok' : 'fail',
    ]);
});
```

**Prioridad:** 🟡 MEDIA | **Esfuerzo:** 1h

---

## 📝 ROADMAP DE IMPLEMENTACIÓN

### Fase 4.1 (1-2 sprints)
- [x] Validaciones defensivas
- [x] Feature tests
- [ ] Activity logging completo
- [ ] Request validation middleware

### Fase 4.2 (2-3 sprints)
- [ ] Audit trail en BD
- [ ] Rate limiting
- [ ] Índices de BD
- [ ] Integration tests

### Fase 4.3 (1 sprint)
- [ ] Encryption at rest
- [ ] Error tracking (Sentry)
- [ ] Health checks

### Fase 4.4 (2 sprints - GDPR)
- [ ] Data export
- [ ] Retention policies
- [ ] Consent tracking
- [ ] Right to be forgotten

---

## ✅ PRE-PRODUCTION CHECKLIST

- [ ] Todos los Feature Tests pasan
- [ ] Todos los Integration Tests pasan
- [ ] Load test < 500ms response time
- [ ] Activity logging para todas las acciones críticas
- [ ] Índices de BD creados y optimizados
- [ ] Rate limiting activo
- [ ] Sentry/Rollbar configurado
- [ ] Health checks funcionando
- [ ] Backup strategy definido
- [ ] Disaster recovery tested
- [ ] GDPR compliance auditado
- [ ] PCI DSS compliance (opcional pre-Stripe)

---

## 📊 ESTIMACIÓN TOTAL

| Fase | Horas | Sprint | Criticidad |
|------|-------|--------|------------|
| 4.1 | 10h | Current | 🔴 CRÍTICA |
| 4.2 | 20h | +2 sprint | 🟡 ALTA |
| 4.3 | 12h | +1 sprint | 🟡 ALTA |
| 4.4 | 16h | +2 sprint | 🟢 CRÍTICA (Legal) |
| **TOTAL** | **58h** | **~5 sprints** | - |

---

## 🎯 CONCLUSIÓN

El POS es funcional pero requiere **hardening antes de producción**. Los cambios defensivos actuales (Fase 4) son **foundation** pero no suficientes para:

1. ✅ Evitar crashes
2. ✅ Validar multiempresa
3. ⚠️ Auditoría completa
4. ⚠️ Compliance legal
5. ⚠️ Security enterprise

**Recomendación:** Implementar Fase 4.1 + 4.2 antes de prod. Fase 4.3-4.4 pueden ser paralelas con feature development.

