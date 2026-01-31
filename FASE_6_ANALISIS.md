# FASE 6: ANÁLISIS ARQUITECTÓNICO - SaaS Multiempresa, Billing y Super Admin

**Fecha**: 31 de enero de 2026  
**Estado**: Análisis Inicial Completado

---

## 1. HALLAZGOS CLAVE DEL PROYECTO ACTUAL

### ✅ Fortalezas Identificadas

1. **Sistema de Multiempresa Consolidado**
   - Campo `empresa_id` en tablas clave (usuarios, productos, ventas, cajas, etc.)
   - Relaciones BelongsTo/HasMany bien definidas
   - Middleware de validación por empresa existente

2. **Sistema de Roles y Permisos**
   - Uso de Spatie Permissions (HasRoles trait en User)
   - Rol actual: "administrador" con permisos granulares
   - Estructura lista para agregar nuevos roles

3. **Integración Stripe Básica**
   - Modelo StripeConfig por empresa
   - PaymentTransaction para transacciones
   - StripeWebhookController existente
   - Pago de ventas integrado

4. **Auth Completo**
   - LoginController / LogoutController
   - Middleware Authenticate funcional
   - CheckUserEstado middleware

### ⚠️ Áreas a Considerar

1. **Landing Page**: Actualmente ruta `/` va directo a panel (requiere auth)
2. **Registro de Empresas**: No existe flujo de onboarding
3. **Suscripciones**: No hay modelo de suscripción ni planes
4. **Super Admin**: Rol no existe; usuarios siempre tienen empresa_id

---

## 2. ARQUITECTURA PROPUESTA - FASE 6

### 2.1 SUPER ADMIN (No multiempresa)

```
Usuario Super Admin:
  ├── empresa_id = NULL (sin empresa asignada)
  ├── Rol = 'super-admin'
  ├── Permisos especiales:
  │   ├── crear-empresa
  │   ├── editar-empresa
  │   ├── suspender-empresa
  │   ├── ver-metricas-globales
  │   └── ver-suscripciones
  └── Rutas propias:
      ├── /admin/super/empresas
      ├── /admin/super/subscriptions
      └── /admin/super/metrics
```

### 2.2 FLUJO DE REGISTRO DE EMPRESA (Onboarding)

```
Usuario Anónimo
    ↓
Landing Page (/) - Selecciona Plan
    ↓
GET /register - Formulario
    ↓
POST /register
    ├── Validar datos
    ├── Crear Empresa (plan_id, tarifa_servicio_porcentaje)
    ├── Crear Usuario Admin de empresa
    ├── Crear Suscripción en Stripe
    ├── Guardar stripe_subscription_id en Empresa
    └── Redirigir a panel (login automático)
    ↓
Usuario Admin en Dashboard (solo su empresa)
```

### 2.3 MODELO DE COBRO (SaaS Realista)

```
SUSCRIPCIÓN MENSUAL (Stripe Subscriptions):
├── Plan Básico: 299.000 COP / mes
│   └── stripe_price_id: price_xxxxx
├── Plan Pro: 399.000 COP / mes
│   └── stripe_price_id: price_xxxxx
└── Características: Trial 14 días, auto-renovación

TARIFA POR TRANSACCIÓN (Fee interno):
├── Porcentaje: 2-5% configurableEditable
├── Guardado en Empresa:
│   ├── tarifa_servicio_porcentaje (decimal 2 decimales)
│   └── tarifa_servicio_monto (suma real por período)
└── Aplicado en cada venta:
    └── total_venta = subtotal + (subtotal * tarifa_porcentaje / 100)
```

### 2.4 TABLAS NUEVAS / MODIFICADAS

**Tabla: saas_plans** (nueva)
```sql
id (PK)
nombre (Basic, Pro)
stripe_price_id
precio_mensual_cop
descripcion
caracteristicas (JSON)
created_at, updated_at
```

**Tabla: empresa** (modificaciones)
```sql
+ plan_id (FK -> saas_plans)
+ stripe_subscription_id
+ stripe_customer_id
+ estado_suscripcion (enum: active, cancelled, past_due)
+ fecha_proximo_pago
+ tarifa_servicio_porcentaje (decimal 5,2 default 2.5)
+ tarifa_servicio_monto (decimal 15,2 default 0)
+ estado (enum: activa, suspendida) -- para admin manual
```

**Tabla: users** (sin cambios en BD, pero lógica)
```sql
empresa_id = NULL para super-admin
empresa_id = 123 para usuarios de empresa 123
```

---

## 3. ESTRUCTURA DE CARPETAS Y ARCHIVOS A CREAR

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   │   ├── RegisterController.php (NUEVO)
│   │   │   └── OAuthController.php (para futuros logins)
│   │   ├── SuperAdmin/
│   │   │   ├── DashboardController.php (NUEVO)
│   │   │   └── EmpresasController.php (NUEVO)
│   │   └── homeController.php (MODIFICAR)
│   └── Middleware/
│       ├── CheckSuperAdmin.php (NUEVO)
│       └── CheckSubscriptionActive.php (NUEVO)
├── Models/
│   ├── SaaSPlan.php (NUEVO)
│   ├── Empresa.php (MODIFICAR)
│   └── User.php (sin cambios)
├── Services/
│   └── SubscriptionService.php (NUEVO)
├── Policies/
│   ├── SuperAdminPolicy.php (NUEVO)
│   └── EmpresaPolicy.php (MODIFICAR)
└── Events/ & Listeners/
    └── SubscriptionCreated.php (NUEVO - para auditoría)

database/
├── migrations/
│   ├── 2026_01_31_000001_create_saas_plans_table.php (NUEVO)
│   └── 2026_01_31_000002_add_subscription_fields_to_empresas.php (NUEVO)
└── seeders/
    ├── SaaSPlanSeeder.php (NUEVO)
    └── PermissionSeeder.php (MODIFICAR - agregar permisos super-admin)

resources/
├── views/
│   ├── landing.blade.php (NUEVO - landing page)
│   ├── auth/
│   │   └── register.blade.php (NUEVO - formulario registro empresa)
│   └── super-admin/
│       ├── dashboard.blade.php (NUEVO)
│       └── empresas/
│           ├── index.blade.php (NUEVO)
│           └── show.blade.php (NUEVO)

routes/
└── web.php (MODIFICAR - agregar rutas de landing, register, super-admin)
```

---

## 4. ORDEN DE IMPLEMENTACIÓN

### FASE A: Fundación (Roles, Modelos, BD)
1. ✅ Crear migraciones para SaaSPlan y campos en Empresa
2. ✅ Crear modelo SaaSPlan
3. ✅ Actualizar modelo Empresa
4. ✅ Crear seeder SaaSPlanSeeder
5. ✅ Agregar permisos super-admin
6. ✅ Crear seeder para rol super-admin

### FASE B: Landing + Registro
1. ✅ Crear landing.blade.php (Tailwind)
2. ✅ Crear RegisterController
3. ✅ Crear register.blade.php (Tailwind)
4. ✅ Actualizar homeController para redirigir según rol

### FASE C: Servicios y Middlewares
1. ✅ Crear SubscriptionService
2. ✅ Crear CheckSuperAdmin middleware
3. ✅ Crear CheckSubscriptionActive middleware

### FASE D: Super Admin Panel
1. ✅ Crear SuperAdmin/DashboardController
2. ✅ Crear SuperAdmin/EmpresasController
3. ✅ Crear vistas super-admin
4. ✅ Crear Policies

### FASE E: Cierre
1. ✅ Documentar FASE_6.md
2. ✅ Tests básicos
3. ✅ README actualizado

---

## 5. CONSIDERACIONES TÉCNICAS

### 5.1 Seguridad
- Super admin NO debe tener empresa_id
- Middleware CheckSuperAdmin valida `auth()->user()->empresa_id === null`
- Queries de empresa deben filtrar por usuario->empresa_id (no super-admin)
- Stripe API keys por empresa (StripeConfig existente funciona)

### 5.2 Compatibilidad
- NO modificar migraciones existentes
- NO eliminar roles/permisos actuales
- Agregar nuevos permisos sin afectar administrador
- User puede tener tanto empresa_id como super-admin role

### 5.3 Stripe Integration
- Usar Stripe Customer para cada empresa
- Stripe Subscription para suscripción mensual
- NO usar Connect (modelo interno de fee)
- Webhook para actualizar estado_suscripcion

### 5.4 Fee por Transacción
- Se guarda en Empresa.tarifa_servicio_porcentaje
- En Venta: total = subtotal * (1 + tarifa_porcentaje/100)
- Se registra en ActivityLog para auditoría

---

## 6. MIGRACIONES REQUERIDAS

### A) Crear tabla saas_plans
```php
Schema::create('saas_plans', function (Blueprint $table) {
    $table->id();
    $table->string('nombre')->unique();
    $table->string('stripe_price_id');
    $table->decimal('precio_mensual_cop', 15, 2);
    $table->text('descripcion')->nullable();
    $table->json('caracteristicas')->nullable();
    $table->timestamps();
});
```

### B) Agregar campos a empresa
```php
Schema::table('empresa', function (Blueprint $table) {
    $table->foreignId('plan_id')->nullable()->constrained('saas_plans');
    $table->string('stripe_subscription_id')->nullable()->unique();
    $table->string('stripe_customer_id')->nullable();
    $table->enum('estado_suscripcion', ['active', 'cancelled', 'past_due'])->default('active');
    $table->timestamp('fecha_proximo_pago')->nullable();
    $table->decimal('tarifa_servicio_porcentaje', 5, 2)->default(2.50);
    $table->decimal('tarifa_servicio_monto', 15, 2)->default(0);
    $table->enum('estado', ['activa', 'suspendida'])->default('activa');
});
```

---

## 7. PERMISOS NUEVOS

```php
super-admin permissions:
- crear-empresa
- editar-empresa
- suspender-empresa
- ver-metricas-globales
- ver-suscripciones-todas
- administrar-planes

super-admin role:
- Tiene TODOS los permisos anteriores
- NO pertenece a empresa
- Puede impersonar usuario de cualquier empresa
```

---

## 8. RUTAS PROPUESTAS

```
Landing & Auth:
GET  /                          → landing page (sin auth)
GET  /register?plan=basic       → registro de empresa
POST /register                  → crear empresa + suscripción

Login Existente:
GET  /login                     → formulario login
POST /login                     → validar credenciales

Dashboard Principal (según rol):
GET  /admin                     → homeController (multi-path)
     ├─ Super admin → super-admin dashboard
     └─ Admin empresa → panel POS actual

Super Admin Routes:
GET  /admin/super/dashboard     → estadísticas globales
GET  /admin/super/empresas      → listado empresas
GET  /admin/super/empresas/{id} → detalle empresa + facturación
POST /admin/super/empresas/{id}/suspender
POST /admin/super/empresas/{id}/activar
GET  /admin/super/subscriptions → estado suscripciones Stripe
```

---

## 9. PUNTOS CRÍTICOS A VALIDAR

- ✅ Super admin sin empresa_id no accede a recursos de empresa
- ✅ Usuario empresa no puede ver otras empresas
- ✅ Suscripción vencida = bloqueo de acceso
- ✅ Fee se calcula correctamente en ventas
- ✅ Stripe webhook actualiza estado_suscripcion
- ✅ Landing page SEO-friendly (sin auth requerido)
- ✅ Compatibilidad con usuarios existentes

---

## SIGUIENTE PASO

Implementar en orden:
1. Migraciones y seeders
2. Landing page
3. Registro de empresa + SubscriptionService
4. Middlewares de seguridad
5. Super admin panel
6. Tests

