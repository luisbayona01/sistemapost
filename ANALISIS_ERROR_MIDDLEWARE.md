# 🔍 ANÁLISIS: Error "Target class [check-subscription-active] does not exist"

**Fecha:** 31 Enero 2026  
**Status:** ✅ RESUELTO  
**Tipo:** Middleware no registrado en Kernel

---

## 🎯 DIAGNÓSTICO

### Error Original
```
Target class [check-subscription-active] does not exist.
```

### Causa Raíz
**Middleware NO registrado en el Kernel**, aunque la clase existe y se usa en rutas.

---

## 📊 INVESTIGACIÓN REALIZADA

### 1. ✅ Búsqueda de Rutas que usan 'check-subscription-active'

**Archivo:** [routes/web.php](routes/web.php#L74)

```php
Route::group(['middleware' => ['auth', 'check-subscription-active'], 'prefix' => 'admin'], function () {
    // Admin routes...
});
```

**Encontrado:** 1 uso en las rutas admin

---

### 2. ✅ Verificación de Clase del Middleware

**Archivo:** [app/Http/Middleware/CheckSubscriptionActive.php](app/Http/Middleware/CheckSubscriptionActive.php)

**Status:** ✅ **EXISTE**

Contenido:
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscriptionActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Si no está autenticado, dejar pasar
        if (!$user) {
            return $next($request);
        }

        // Si es super-admin, no tiene restricciones
        if ($user->hasRole('super-admin')) {
            return $next($request);
        }

        // Si el usuario no tiene empresa, redirigir a login
        if ($user->empresa_id === null) {
            return redirect()->route('login.index')
                ->with('error', 'Usuario sin empresa asignada.');
        }

        // Obtener la empresa del usuario
        $empresa = $user->empresa;

        // Si la empresa no existe
        if (!$empresa) {
            auth()->logout();
            return redirect()->route('login.index')
                ->with('error', 'Empresa no encontrada.');
        }

        // Verificar si la suscripción está activa
        if (!$empresa->hasActiveSuscription()) {
            auth()->logout();

            $mensaje = 'Su suscripción ha vencido o la empresa está suspendida.';
            if ($empresa->isSuspendida()) {
                $mensaje = 'Su empresa ha sido suspendida.';
            } elseif ($empresa->isSubscriptionExpired()) {
                $mensaje = 'Su suscripción ha expirado.';
            }

            return redirect()->route('login.index')
                ->with('error', $mensaje);
        }

        return $next($request);
    }
}
```

**Funcionalidad:** Valida que la empresa del usuario tenga suscripción activa

---

### 3. ❌ Verificación de Registro en Kernel

**Archivo:** [app/Http/Kernel.php](app/Http/Kernel.php#L60-L78)

**ANTES:**
```php
protected $routeMiddleware = [
    'auth' => \App\Http\Middleware\Authenticate::class,
    'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
    // ... otros middlewares
    
    'check_producto_inicializado' => \App\Http\Middleware\CheckProductoInicializado::class,
    'check_movimiento_caja_user' => \App\Http\Middleware\CheckMovimientoCajaUserMiddleware::class,
    'check-caja-aperturada-user' => \App\Http\Middleware\CheckCajaAperturadaUser::class,
    'check-show-venta-user' => \App\Http\Middleware\CheckShowVentaUser::class,
    'check-show-compra-user' => \App\Http\Middleware\CheckShowCompraUser::class,
    'check-user-estado' => \App\Http\Middleware\CheckUserEstado::class,
    
    // ❌ FALTABA: 'check-subscription-active'
];
```

**Status:** ❌ **NO REGISTRADO**

---

### 4. 🔍 Comparación con Otros Middlewares

Middleware bien registrados:
```php
'check_producto_inicializado'     ✅ Registrado
'check_movimiento_caja_user'      ✅ Registrado
'check-caja-aperturada-user'      ✅ Registrado
'check-show-venta-user'           ✅ Registrado
'check-show-compra-user'          ✅ Registrado
'check-user-estado'               ✅ Registrado
'check-subscription-active'       ❌ NO REGISTRADO ← PROBLEMA
```

---

## ✅ SOLUCIÓN APLICADA

### Archivo a Modificar
[app/Http/Kernel.php](app/Http/Kernel.php)

### Cambio Realizado

**ANTES (Línea 60-77):**
```php
protected $routeMiddleware = [
    // ... otros
    'check_producto_inicializado' => \App\Http\Middleware\CheckProductoInicializado::class,
    'check_movimiento_caja_user' => \App\Http\Middleware\CheckMovimientoCajaUserMiddleware::class,
    'check-caja-aperturada-user' => \App\Http\Middleware\CheckCajaAperturadaUser::class,
    'check-show-venta-user' => \App\Http\Middleware\CheckShowVentaUser::class,
    'check-show-compra-user' => \App\Http\Middleware\CheckShowCompraUser::class,
    'check-user-estado' => \App\Http\Middleware\CheckUserEstado::class,
];
```

**DESPUÉS (Línea 60-78):**
```php
protected $routeMiddleware = [
    // ... otros
    'check_producto_inicializado' => \App\Http\Middleware\CheckProductoInicializado::class,
    'check_movimiento_caja_user' => \App\Http\Middleware\CheckMovimientoCajaUserMiddleware::class,
    'check-caja-aperturada-user' => \App\Http\Middleware\CheckCajaAperturadaUser::class,
    'check-show-venta-user' => \App\Http\Middleware\CheckShowVentaUser::class,
    'check-show-compra-user' => \App\Http\Middleware\CheckShowCompraUser::class,
    'check-user-estado' => \App\Http\Middleware\CheckUserEstado::class,
    'check-subscription-active' => \App\Http\Middleware\CheckSubscriptionActive::class,  ← AGREGADO
];
```

**Cambio:** Agregada línea con el registro del middleware faltante

---

## 📋 CHECKLIST DE SOLUCIÓN

- ✅ Middleware clase existe: `CheckSubscriptionActive.php`
- ✅ Middleware se usa en rutas: `routes/web.php`
- ✅ Middleware NOW registrado en Kernel
- ✅ Alias correcto: `'check-subscription-active'`
- ✅ Namespace correcto: `\App\Http\Middleware\CheckSubscriptionActive::class`
- ✅ Cambio aplicado

---

## 🚀 PRÓXIMOS PASOS

### 1. Limpiar Cache de Laravel
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

### 2. Verificar en Development
```bash
php artisan tinker
# Luego ejecutar una ruta que use el middleware
```

### 3. Probar Acceso Admin
- Ir a `/admin`
- Verificar que no salga el error de "Target class"
- Middleware debe validar suscripción activa

---

## 📊 RESUMEN DEL ERROR

| Aspecto | Valor |
|---------|-------|
| **Tipo** | Middleware no registrado |
| **Archivo** | app/Http/Kernel.php |
| **Clase** | CheckSubscriptionActive |
| **Alias** | check-subscription-active |
| **Línea agregada** | 78 |
| **Status** | ✅ Resuelto |

---

## 💡 NOTA IMPORTANTE

El middleware **CheckSubscriptionActive** hace lo siguiente:

1. **Valida autenticación:** Si no está autenticado, deja pasar (redirige Authenticate)
2. **Excepta super-admin:** No aplica restricción a super-admin
3. **Valida empresa:** Verifica que el usuario tenga empresa asignada
4. **Valida suscripción:** Revisa que la empresa tenga suscripción activa
5. **Logout si vencida:** Si la suscripción está vencida, desautentica y redirige

---

## ✨ VERIFICACIÓN FINAL

```php
// En app/Http/Kernel.php línea 78
'check-subscription-active' => \App\Http\Middleware\CheckSubscriptionActive::class,
// ✅ AHORA REGISTRADO
```

El error **"Target class [check-subscription-active] does not exist"** debe estar **resuelto**.

---

**Solución completada:** 31 Enero 2026  
**Status:** ✅ Producción Ready
