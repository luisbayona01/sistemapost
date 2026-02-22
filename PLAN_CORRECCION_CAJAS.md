# 🔴 PLAN DE CORRECCIÓN — Sistema de Cajas Obligatorio

## PROBLEMA DETECTADO
El sistema permite ventas sin caja abierta, lo cual rompe la auditoría y contabilidad.

## SOLUCIÓN IMPLEMENTADA

### 1. Middleware de Validación de Caja (`EnsureCajaAbierta`)
- Bloquea acceso al POS si no hay caja abierta
- Redirige a pantalla de apertura de caja
- Mensaje claro: "Debes abrir una caja antes de vender"

### 2. Apertura Automática Simplificada
- **ANTES**: Apertura automática con $0 (línea 369-381 CashierController)
- **DESPUÉS**: Middleware obliga apertura manual
- **BENEFICIO**: Auditoría clara, responsabilidad del cajero

### 3. Validación en `finalizarVenta()`
- Doble check: Middleware + validación en controlador
- Error 422 si no hay caja: "No hay caja abierta. Contacta al administrador."

### 4. Seeders para Testing
- Crear cajeros genéricos: `cajero1@test.com`, `cajero2@test.com`
- Crear cajas predefinidas: "Caja Principal", "Caja Secundaria"

## ARCHIVOS A MODIFICAR
1. `app/Http/Middleware/EnsureCajaAbierta.php` (CREAR)
2. `app/Http/Kernel.php` (Registrar middleware)
3. `routes/web.php` (Aplicar middleware a rutas POS)
4. `app/Http/Controllers/POS/CashierController.php` (Eliminar apertura auto, reforzar validación)
5. `database/seeders/CajerosSeeder.php` (CREAR)
6. `resources/views/pos/sin-caja.blade.php` (CREAR - Vista de error amigable)

## FLUJO CORRECTO
1. Usuario con rol "cajero" inicia sesión
2. Middleware detecta que no hay caja abierta
3. Redirige a `/admin/cajas` con mensaje
4. Cajero abre caja manualmente (con base inicial)
5. Puede acceder al POS y vender
6. Al cerrar caja, se bloquea nuevamente el POS

## PRIORIDAD 2 - Precio Entrada
- Auditar línea 102-116 de `CashierController`
- Unificar precio: `precioBase + tarifaFija` (sin sumas invisibles)
- Verificar que el precio en carrito = precio en BD

## PRÓXIMOS PASOS
1. Crear middleware
2. Aplicar a rutas
3. Eliminar apertura automática
4. Crear seeders de prueba
5. Testing manual
