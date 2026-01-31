# FASE 6: QUICK START - Guía Rápida de Implementación

## ⚡ Setup en 5 Pasos

### 1. Ejecutar Migraciones
```bash
php artisan migrate
```

**Tablas creadas**:
- `saas_plans` - Planes de suscripción
- Columnas nuevas en `empresa`

### 2. Ejecutar Seeders
```bash
php artisan db:seed
```

**Datos creados**:
- 3 planes SaaS (Básico, Profesional, Empresa)
- Rol `super-admin`
- 12+ permisos nuevos

### 3. Configurar Stripe (Opcional)

En `.env`:
```env
STRIPE_PUBLIC_KEY=pk_test_xxx
STRIPE_SECRET_KEY=sk_test_xxx
```

**Nota**: Sin Stripe, el registro fallará en `createSubscription()`. Para tests, mockear:

```php
// En seeder de prueba
$empresa->update([
    'stripe_customer_id' => 'cus_mock_123',
    'stripe_subscription_id' => 'sub_mock_123',
]);
```

### 4. Crear Super Admin Manual (Opcional)

```bash
php artisan tinker

> $user = User::create([
    'name' => 'Super Admin',
    'email' => 'superadmin@cinemapos.com',
    'password' => bcrypt('SecurePassword123'),
    'empresa_id' => null,
]);

> $user->assignRole('super-admin');

> quit
```

### 5. Limpiar Cache

```bash
php artisan config:clear
php artisan view:clear
php artisan cache:clear
```

---

## 🔗 URLs Clave

| URL | Descripción | Auth Requerida |
|-----|-------------|----------------|
| `/` | Landing page o panel | No |
| `/landing` | Landing page explícita | No |
| `/register` | Formulario registro empresa | No |
| `/login` | Login | No |
| `/admin` | Panel POS (empresa) | Sí - con suscripción activa |
| `/admin/super/dashboard` | Dashboard super admin | Sí - super-admin |
| `/admin/super/empresas` | Listar empresas | Sí - super-admin |

---

## 📋 Checklist Pre-Deploy

- [ ] Migraciones ejecutadas sin error
- [ ] Seeders ejecutados exitosamente
- [ ] Stripe API keys en `.env`
- [ ] Landing page accesible en `/`
- [ ] Registro funciona en `/register`
- [ ] Super admin logueado en `/admin/super/dashboard`
- [ ] Middleware CheckSuperAdmin en rutas
- [ ] Middleware CheckSubscriptionActive en admin routes
- [ ] Vistas con Tailwind CSS cargan correctamente
- [ ] Formulario registro valida correctamente

---

## 🔐 Usuarios de Prueba (Post-Seed)

Después de ejecutar `php artisan migrate --seed`:

```
Login Usuario:
Email: admin@gmail.com
Password: 12345678
Rol: administrador
Empresa: (asignada)

Nota: Usuario default NO es super-admin.
Para crear super-admin, ver "Crear Super Admin Manual" arriba.
```

---

## 🚨 Errores Comunes

### Error: "SQLSTATE[42S02]: Table or view not found"
**Solución**: Ejecutar migraciones: `php artisan migrate`

### Error: "Call to undefined method...Empresa::plan()"
**Solución**: Ejecutar migraciones y cache:clear: `php artisan migrate && php artisan cache:clear`

### Error: "Route not defined: register.create"
**Solución**: Actualizar rutas en web.php, ejecutar cache:clear

### Error: "Stripe API Key not configured"
**Solución**: Agregar STRIPE_SECRET_KEY en .env

---

## 📊 Datos Después de Seed

```sql
-- Planes SaaS creados
SELECT * FROM saas_plans;
-- OUTPUT: 3 filas (Básico, Profesional, Empresa)

-- Permisos creados
SELECT COUNT(*) FROM permissions;
-- OUTPUT: ~30+ permisos (incluyendo 12+ super-admin)

-- Rol super-admin creado
SELECT * FROM roles WHERE name = 'super-admin';
-- OUTPUT: 1 fila
```

---

## 🔍 Verificación Post-Deploy

```bash
# 1. Verificar modelos
php artisan tinker
> Empresa::first()->plan
> SaaSPlan::first()
> quit

# 2. Verificar rutas
php artisan route:list | grep register
php artisan route:list | grep super-admin

# 3. Verificar middleware
grep -r "CheckSuperAdmin" app/

# 4. Verificar vistas
ls resources/views/landing.blade.php
ls resources/views/auth/register.blade.php
ls resources/views/super-admin/
```

---

## 💾 Rollback (Si es necesario)

```bash
# Revertir última migración
php artisan migrate:rollback

# Revertir todo
php artisan migrate:reset

# Revertir y re-ejecutar
php artisan migrate:refresh --seed
```

---

## 📞 Contacto Soporte

- **Documentación Completa**: [FASE_6_IMPLEMENTACION.md](./FASE_6_IMPLEMENTACION.md)
- **Análisis Arquitectónico**: [FASE_6_ANALISIS.md](./FASE_6_ANALISIS.md)

---

**Última actualización**: 31 de enero de 2026  
**Versión**: 1.0

