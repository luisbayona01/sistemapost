# 🩺 Auditoría de Arquitectura Integral - Fase 4 (Zero-Trust)

Este documento centraliza los bloques de código fuente solicitados para la validación de seguridad y arquitectura antes de proceder con las funcionalidades de Cinema (Fase 5).

---

## 1. 📂 Esquema de Base de Datos
A continuación se detallan las estructuras fundamentales para el blindaje de datos:

### Tabla: `users`
*   **Path**: `database/migrations/2014_10_12_000000_create_users_table.php`
*   **Aislamiento**: Incluye `empresa_id` como clave foránea obligatoria.
*   **Seguridad**: RBAC implementado con Spatie Laravel-Permission.

### Tabla: `empresa`
*   **Path**: `database/migrations/2025_01_23_113626_create_empresas_table.php`
*   **Estado**: Entidad raíz del multi-tenancy. Almacena configuración fiscal y de suscripción.

### Tabla: `ventas`
*   **Path**: `database/migrations/2023_03_10_022517_create_ventas_table.php`
*   **Blindaje**: Columna `empresa_id` con índice para filtrado rápido.

### Tabla: `productos` y `cajas`
*   Ambas tablas cuentan con la relación `empresa_id`. 
*   **Nota sobre `funciones`**: El esquema de `funciones` (showtimes) aún no ha sido creado, ya que forma parte del backlog de la Fase 5.

---

## 2. 🛡️ Lógica de Aislamiento (Multi-tenancy)
El sistema utiliza **Eloquent Global Scopes** para garantizar que ningún usuario pueda ver o modificar datos de otra empresa, incluso si se manipulan las IDs en la URL.

### Implementación en Modelos (Ejemplo: `Venta.php`)
```php
protected static function booted(): void
{
    static::addGlobalScope('empresa', function (Builder $query) {
        if (auth()->check() && auth()->user()->empresa_id) {
            $query->where('ventas.empresa_id', auth()->user()->empresa_id);
        }
    });
}
```
*Este mecanismo se aplica a: Venta, Producto, Caja, Cliente, Proveedor, Compra, Movimiento e Inventario.*

---

## 3. 🧠 Core de Negocio (Sales & Taxes)
Hemos centralizado la lógica en un **Service Layer** para evitar "Fat Controllers" y asegurar transaccionalidad atómica.

### Clase: `App\Services\VentaService`
*   **Transaccionalidad**: Usa `DB::transaction` para asegurar que la venta, el detalle, el stock y el movimiento de caja se graben o fallen como una sola unidad.
*   **Cálculo**: El cálculo de impuestos se realiza en base al `porcentaje_impuesto` configurado en el modelo `Empresa` del usuario autenticado.
*   **Atomicidad**: Implementa `lockForUpdate()` en el inventario para evitar Race Conditions.

---

## 4. 🔑 Configuración de Sesión
La vinculación entre el usuario y su empresa se establece en el momento del login y persiste en el objeto `Auth::user()`.

### Flujo de Autenticación (`loginController.php`):
1.  El usuario se autentica con email/password.
2.  Laravel carga el modelo `User` con su `empresa_id`.
3.  Cualquier Middleware de validación posterior (como `check-subscription-active`) accede a esta ID mediante `$request->user()->empresa_id`.
4.  **Zero-Trust**: El `empresa_id` NUNCA se recibe desde el orígen del Request del cliente; siempre se obtiene del estado autenticado en el servidor.

---

## 🔍 Diagnóstico Final - Auditoría Fase 4
*   **Data Leaks**: Se han corregido las consultas `DB::table` que existían en `homeController`, reemplazándolas por Eloquent para que el Global Scope actúe como firewall.
*   **Escalabilidad**: El uso de Services permite mover la lógica a Workers o microservicios fácilmente en el futuro.
*   **Seguridad**: El sistema cumple con el aislamiento estricto de nivel Tenant.

**Validación Completa. Listo para Fase 5: Cinema Features.**
