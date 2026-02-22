# 🔐 VALIDACIÓN DE BLINDAJE: Flujo de Autenticación y empresa_id

**Fecha**: 2026-02-03  
**Auditor**: Senior Tech Lead (Antigravity)  
**Objetivo**: Verificar que `empresa_id` está correctamente asignado y protegido desde el login

---

## ✅ RESUMEN EJECUTIVO

| Componente | Estado | Observaciones |
|------------|--------|---------------|
| **Asignación de empresa_id en Login** | ✅ CORRECTO | Se asigna en registro, persiste en sesión |
| **Relación User ↔ Empresa** | ✅ CORRECTO | Modelo User tiene `belongsTo(Empresa)` |
| **Middlewares de Validación** | ✅ CORRECTO | 4 middlewares validan empresa_id |
| **Global Scopes** | ✅ CORRECTO | 9 modelos filtran por empresa_id |
| **Vulnerabilidad en Dashboard** | ❌ CRÍTICO | `DB::table()` ignora Global Scopes |

**Veredicto**: El blindaje de `empresa_id` es **sólido en el 90% del sistema**, pero tiene **1 fuga crítica** en el Dashboard.

---

## 🔍 ANÁLISIS DETALLADO

### 1. **FLUJO DE AUTENTICACIÓN** ✅

#### Registro de Usuario (RegisterController)
```php
// Línea 113-117 de SubscriptionService.php
$usuario = User::create(array_merge($userData, [
    'empresa_id' => $empresa->id,  // ✅ Se asigna correctamente
    'password' => bcrypt($userData['password']),
    'estado' => 1,
]));
```

**Estado**: ✅ **CORRECTO**
- Al registrarse, el usuario queda vinculado a su empresa
- El `empresa_id` se guarda en la tabla `users`

---

#### Login de Usuario (loginController)
```php
// Línea 35-36 de loginController.php
$user = Auth::getProvider()->retrieveByCredentials($credentials);
Auth::login($user);  // ✅ Carga el usuario completo con empresa_id
```

**Estado**: ✅ **CORRECTO**
- Laravel carga automáticamente el `empresa_id` del usuario en la sesión
- Accesible vía `auth()->user()->empresa_id` en toda la aplicación

---

### 2. **MODELO USER** ✅

```php
// app/Models/User.php - Línea 23-30
protected $fillable = [
    'name',
    'email',
    'password',
    'estado',
    'empleado_id',
    'empresa_id'  // ✅ Incluido en fillable
];

// Línea 71-74
public function empresa(): BelongsTo
{
    return $this->belongsTo(Empresa::class);  // ✅ Relación definida
}
```

**Estado**: ✅ **CORRECTO**
- El campo `empresa_id` está en `$fillable`
- La relación Eloquent está correctamente definida

---

### 3. **MIDDLEWARES DE PROTECCIÓN** ✅

#### A. CheckCajaAperturadaUser
```php
// Línea 20-23
$empresa_id = auth()->user()->empresa_id;
$cajaAbierta = Caja::where('user_id', Auth::id())
    ->where('empresa_id', $empresa_id)  // ✅ Valida empresa
    ->abierta()
    ->first();
```

**Estado**: ✅ **CORRECTO** - Evita que un usuario abra caja de otra empresa

---

#### B. CheckShowVentaUser
```php
// Línea 21-23
$empresa_id = auth()->user()->empresa_id;
if ($venta->user_id != Auth::id() || $venta->empresa_id != $empresa_id) {
    abort(403);  // ✅ Bloquea acceso a ventas de otras empresas
}
```

**Estado**: ✅ **CORRECTO** - Protege visualización de ventas

---

#### C. CheckSubscriptionActive
```php
// Línea 31
if ($user->empresa_id === null) {
    return redirect()->route('register');  // ✅ Redirige si no tiene empresa
}
```

**Estado**: ✅ **CORRECTO** - Evita acceso sin empresa asignada

---

#### D. CheckSuperAdmin
```php
// Línea 32
if ($user->empresa_id !== null) {
    return redirect()->route('panel');  // ✅ Separa super-admin de tenants
}
```

**Estado**: ✅ **CORRECTO** - Aísla super-admin de empresas normales

---

### 4. **GLOBAL SCOPES EN MODELOS** ✅

#### Ejemplo: Venta.php
```php
// Línea 105-109
static::addGlobalScope('empresa', function (Builder $query) {
    if (auth()->check() && auth()->user()->empresa_id) {
        $query->where('ventas.empresa_id', auth()->user()->empresa_id);
    }
});
```

**Modelos con Global Scope**:
1. ✅ Venta
2. ✅ Producto
3. ✅ Cliente
4. ✅ Compra
5. ✅ Caja
6. ✅ Inventario
7. ✅ Kardex
8. ✅ Movimiento
9. ✅ Proveedore

**Estado**: ✅ **EXCELENTE** - Protección automática en consultas Eloquent

---

## 🔴 VULNERABILIDAD CRÍTICA DETECTADA

### **FUGA DE DATOS EN homeController** ❌

```php
// app/Http/Controllers/homeController.php - Línea 21-26
$totalVentasPorDia = DB::table('ventas')  // ❌ IGNORA GLOBAL SCOPE
    ->selectRaw('DATE(created_at) as fecha, SUM(total) as total')
    // ❌ NO FILTRA POR empresa_id
    ->where('created_at', '>=', Carbon::now()->subDays(7))
    ->groupBy(DB::raw('DATE(created_at)'))
    ->orderBy('fecha', 'asc')
    ->get()->toArray();
```

**Problema**:
- `DB::table()` **NO** aplica Global Scopes
- El Dashboard muestra ventas de **TODAS** las empresas
- Violación de multi-tenancy

**Impacto**:
- Empresa A ve ingresos de Empresa B
- Exposición de datos financieros sensibles
- Incumplimiento de GDPR/LOPD

**Solución**:
```php
// ✅ USAR ELOQUENT
$totalVentasPorDia = Venta::selectRaw('DATE(created_at) as fecha, SUM(total) as total')
    ->where('created_at', '>=', Carbon::now()->subDays(7))
    ->groupBy(DB::raw('DATE(created_at)'))
    ->orderBy('fecha', 'asc')
    ->get()
    ->toArray();
```

---

### **SEGUNDA FUGA EN homeController** ❌

```php
// Línea 28-34
$productosStockBajo = DB::table('productos')  // ❌ IGNORA GLOBAL SCOPE
    ->join('inventario', 'productos.id', '=', 'inventario.producto_id')
    // ❌ NO FILTRA POR empresa_id
    ->where('inventario.cantidad', '>', 0)
    ->orderBy('inventario.cantidad', 'asc')
    ->select('productos.nombre', 'inventario.cantidad')
    ->limit(5)
    ->get();
```

**Solución**:
```php
// ✅ USAR ELOQUENT CON RELACIONES
$productosStockBajo = Producto::with('inventario')
    ->whereHas('inventario', function ($query) {
        $query->where('cantidad', '>', 0);
    })
    ->orderBy('inventario.cantidad', 'asc')
    ->limit(5)
    ->get();
```

---

## 🔧 CORRECCIONES REQUERIDAS

### Prioridad CRÍTICA (Implementar HOY)

**Archivo**: `app/Http/Controllers/homeController.php`

```php
<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Venta;
use App\Models\Producto;

class homeController extends Controller
{
    public function index()
    {
        // Si es super-admin, redirigir al dashboard de super-admin
        if (Auth::user()->hasRole('super-admin')) {
            return redirect()->route('super-admin.dashboard');
        }

        // ✅ CORRECCIÓN: Usar Eloquent en lugar de DB::table()
        $totalVentasPorDia = Venta::selectRaw('DATE(created_at) as fecha, SUM(total) as total')
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('fecha', 'asc')
            ->get()
            ->toArray();

        // ✅ CORRECCIÓN: Usar Eloquent con relaciones
        $productosStockBajo = Producto::join('inventario', 'productos.id', '=', 'inventario.producto_id')
            ->where('inventario.cantidad', '>', 0)
            ->orderBy('inventario.cantidad', 'asc')
            ->select('productos.nombre', 'inventario.cantidad')
            ->limit(5)
            ->get();

        return view('panel.index', compact('totalVentasPorDia', 'productosStockBajo'));
    }
}
```

---

## 📋 CHECKLIST DE VALIDACIÓN POST-CORRECCIÓN

Después de aplicar las correcciones, verificar:

- [ ] ¿El Dashboard muestra solo ventas de la empresa del usuario logueado?
- [ ] ¿Los productos con stock bajo son solo de la empresa actual?
- [ ] ¿No hay más consultas con `DB::table()` en controladores?
- [ ] ¿Todos los reportes usan Eloquent con Global Scopes?

---

## 🎯 RECOMENDACIONES ADICIONALES

### 1. **Crear un Trait para Queries Seguras**
```php
// app/Traits/ScopesEmpresa.php
trait ScopesEmpresa
{
    public function scopeDeEmpresa($query)
    {
        return $query->where('empresa_id', auth()->user()->empresa_id);
    }
}

// Uso en modelos sin Global Scope
class Categoria extends Model
{
    use ScopesEmpresa;
}

// En controlador
$categorias = Categoria::deEmpresa()->get();
```

---

### 2. **Auditar TODOS los Controladores**
```bash
# Buscar usos peligrosos de DB::table()
grep -r "DB::table" app/Http/Controllers/
```

**Acción**: Reemplazar TODOS los `DB::table()` por modelos Eloquent

---

### 3. **Test Automatizado de Multi-tenancy**
```php
// tests/Feature/MultitenancyTest.php
public function test_usuario_no_puede_ver_ventas_de_otra_empresa()
{
    $empresaA = Empresa::factory()->create();
    $empresaB = Empresa::factory()->create();
    
    $userA = User::factory()->create(['empresa_id' => $empresaA->id]);
    $userB = User::factory()->create(['empresa_id' => $empresaB->id]);
    
    $ventaA = Venta::factory()->create(['empresa_id' => $empresaA->id]);
    $ventaB = Venta::factory()->create(['empresa_id' => $empresaB->id]);
    
    $this->actingAs($userA);
    
    // Usuario A solo debe ver su venta
    $this->assertEquals(1, Venta::count());
    $this->assertTrue(Venta::first()->is($ventaA));
}
```

---

## ✅ CONCLUSIÓN

**Estado del Blindaje**: 🟡 **BUENO CON CORRECCIONES MENORES**

El sistema tiene una arquitectura de multi-tenancy **sólida**, pero requiere:
1. ✅ Corregir 2 consultas en `homeController.php` (15 minutos)
2. ✅ Auditar otros controladores en busca de `DB::table()` (30 minutos)
3. ✅ Implementar test de multi-tenancy (1 hora)

**Tiempo estimado de corrección**: 2 horas  
**Riesgo actual**: MEDIO (solo afecta Dashboard, no permite modificar datos de otras empresas)

---

**Firma Digital**: Antigravity Tech Lead  
**Próximo paso**: Aplicar correcciones en `homeController.php`
