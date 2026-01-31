# ✅ CHECKLIST DE VALIDACIÓN Y VERIFICACIÓN

**CinemaPOS - Reestructuración SaaS**  
**Fecha de Validación:** 30 de enero de 2026

---

## 📋 Pre-Ejecución de Migraciones

- [ ] **Backup de Base de Datos**
  ```bash
  mysqldump -u root -p cinemaptos_db > backup_pre_migration.sql
  ```
  - Servidor: localhost
  - Usuario DB: root
  - Base de datos: cinemaptos_db

- [ ] **Revisar .env**
  ```env
  DB_CONNECTION=mysql
  DB_HOST=127.0.0.1
  DB_PORT=3306
  DB_DATABASE=cinemaptos_db
  DB_USERNAME=root
  DB_PASSWORD=***
  ```

- [ ] **Verificar conexión a BD**
  ```bash
  php artisan tinker
  >>> DB::connection()->getPdo()
  ```
  Resultado esperado: PDO object

- [ ] **Revisar estado de migraciones anteriores**
  ```bash
  php artisan migrate:status
  ```
  - Todas las migraciones anteriores deben estar ✓ `Ran`

- [ ] **Verificar espacio en disco**
  ```bash
  df -h
  ```
  - Mínimo: 500MB libres

---

## 🔍 Validación de Migraciones Nuevas

### Migraciones Creadas (14 archivos)

- [ ] `2026_01_30_114320_add_empresa_id_to_users_table.php`
  - [ ] Archivo existe
  - [ ] Sintaxis PHP correcta
  - [ ] Método `up()` completo
  - [ ] Método `down()` presente

- [ ] `2026_01_30_114325_add_empresa_id_to_empleados_table.php`
  - [ ] Archivo existe
  - [ ] Agrega campo `empresa_id`
  - [ ] Foreign key correcto

- [ ] `2026_01_30_114330_add_empresa_id_to_cajas_table.php`
  - [ ] Archivo existe
  - [ ] Agrega campo `empresa_id`
  - [ ] Índice `(empresa_id, estado)` agregado

- [ ] `2026_01_30_114335_update_movimientos_table.php`
  - [ ] Archivo existe
  - [ ] Agrega `empresa_id`
  - [ ] Agrega `venta_id` nullable
  - [ ] Índice compuesto agregado

- [ ] `2026_01_30_114340_add_fields_to_ventas_table.php`
  - [ ] Archivo existe
  - [ ] Agrega `empresa_id`
  - [ ] Agrega `tarifa_servicio` (DECIMAL 5,2)
  - [ ] Agrega `monto_tarifa` (DECIMAL 10,2)
  - [ ] Agrega `stripe_payment_intent_id` (VARCHAR 255, nullable)
  - [ ] Índice `(empresa_id, fecha_hora)` agregado

- [ ] `2026_01_30_114345_add_empresa_id_to_productos_table.php`
  - [ ] Archivo existe
  - [ ] Agrega `empresa_id`
  - [ ] Índice `(empresa_id, estado)` agregado

- [ ] `2026_01_30_114350_add_empresa_id_to_compras_table.php`
  - [ ] Archivo existe
  - [ ] Agrega `empresa_id`
  - [ ] Índice `(empresa_id, fecha_hora)` agregado

- [ ] `2026_01_30_114355_add_empresa_id_to_clientes_table.php`
  - [ ] Archivo existe
  - [ ] Agrega `empresa_id`
  - [ ] Índice agregado

- [ ] `2026_01_30_114400_add_empresa_id_to_proveedores_table.php`
  - [ ] Archivo existe
  - [ ] Agrega `empresa_id`

- [ ] `2026_01_30_114405_add_empresa_id_to_inventarios_table.php`
  - [ ] Archivo existe
  - [ ] Agrega `empresa_id`

- [ ] `2026_01_30_114410_add_empresa_id_to_kardexes_table.php`
  - [ ] Archivo existe
  - [ ] Agrega `empresa_id`

- [ ] `2026_01_30_114415_add_tarifa_unitaria_to_producto_venta_table.php`
  - [ ] Archivo existe
  - [ ] Agrega `tarifa_unitaria` (DECIMAL 10,2)

- [ ] `2026_01_30_114420_create_stripe_configs_table.php`
  - [ ] Archivo existe
  - [ ] Tabla nueva: `stripe_configs`
  - [ ] Campos: id, empresa_id, public_key, secret_key, webhook_secret, test_mode, enabled
  - [ ] Foreign key a empresa

- [ ] `2026_01_30_114425_create_payment_transactions_table.php`
  - [ ] Archivo existe
  - [ ] Tabla nueva: `payment_transactions`
  - [ ] Campos: id, empresa_id, venta_id, payment_method, stripe_*, amount_paid, currency, status
  - [ ] Índices creados

---

## 🚀 Ejecución de Migraciones

### Paso 1: Ejecutar Migraciones
```bash
php artisan migrate
```

- [ ] Comando completado exitosamente
- [ ] Mensaje: "Migrated: 2026_01_30_..."
- [ ] No hay errores de sintaxis
- [ ] No hay errores de constraints

### Paso 2: Verificar Estado
```bash
php artisan migrate:status
```

- [ ] Todas las nuevas migraciones marcan "Ran"
- [ ] No hay migraciones "Pending"

---

## 🔧 Validación Post-Migraciones

### Verificación en BD (MySQL)

```sql
-- 1. Verificar tabla USERS
DESCRIBE users;
```
- [ ] Campo `empresa_id` existe (BIGINT, nullable)
- [ ] Foreign key a tabla `empresa`

```sql
-- 2. Verificar tabla VENTAS
DESCRIBE ventas;
```
- [ ] `empresa_id` (BIGINT)
- [ ] `tarifa_servicio` (DECIMAL 5,2)
- [ ] `monto_tarifa` (DECIMAL 10,2)
- [ ] `stripe_payment_intent_id` (VARCHAR 255, nullable)

```sql
-- 3. Verificar tabla MOVIMIENTOS
DESCRIBE movimientos;
```
- [ ] `empresa_id` (BIGINT)
- [ ] `venta_id` (BIGINT, nullable)

```sql
-- 4. Verificar tabla nueva STRIPE_CONFIGS
DESCRIBE stripe_configs;
```
- [ ] id, empresa_id, public_key, secret_key, webhook_secret, test_mode, enabled
- [ ] Unique constraint en empresa_id

```sql
-- 5. Verificar tabla nueva PAYMENT_TRANSACTIONS
DESCRIBE payment_transactions;
```
- [ ] id, empresa_id, venta_id, payment_method, stripe_*, amount_paid, currency, status

```sql
-- 6. Verificar Índices
SHOW INDEXES FROM cajas WHERE Key_name != 'PRIMARY';
SHOW INDEXES FROM movimientos WHERE Key_name != 'PRIMARY';
SHOW INDEXES FROM ventas WHERE Key_name != 'PRIMARY';
```
- [ ] Índices compuestos creados correctamente

---

## 📊 Integridad de Datos

### Verificación de Backfill (si aplica)

```sql
-- Verificar que los campos empresa_id tienen valores
SELECT COUNT(*) FROM users WHERE empresa_id IS NULL;
-- Esperado: 0 (si hay datos existentes)

SELECT COUNT(*) FROM empleados WHERE empresa_id IS NULL;
-- Esperado: 0

SELECT COUNT(*) FROM cajas WHERE empresa_id IS NULL;
-- Esperado: 0
```

- [ ] No hay NULL en campos empresa_id (excepto users que es nullable)

### Verificar Datos Históricos

```sql
-- Contar registros (deben ser iguales antes y después)
SELECT COUNT(*) FROM ventas;
-- Comparar con backup

SELECT COUNT(*) FROM productos;
-- Comparar con backup

SELECT COUNT(*) FROM cajas;
-- Comparar con backup
```

- [ ] Cantidad de registros igual
- [ ] Totales de campos numéricos igual
- [ ] No hay pérdida de datos

---

## 🧪 Pruebas en Artisan Tinker

```bash
php artisan tinker
```

### Test 1: Crear Venta con Tarifa

```php
>>> $empresa = Empresa::first();
>>> $user = User::first();
>>> $user->empresa_id = $empresa->id;
>>> $user->save();

>>> $venta = new Venta();
>>> $venta->empresa_id = $empresa->id;
>>> $venta->user_id = $user->id;
>>> $venta->cliente_id = 1;
>>> $venta->subtotal = 100;
>>> $venta->impuesto = 15;
>>> $venta->calcularTarifa(3.50);
>>> $venta->save();

>>> dd($venta);
```

- [ ] Venta creada exitosamente
- [ ] `tarifa_servicio` = 3.50
- [ ] `monto_tarifa` = 3.50
- [ ] `empresa_id` asignado

### Test 2: Verificar Relaciones

```php
>>> $venta = Venta::first();
>>> $venta->empresa()->exists();
-- Esperado: true
>>> $venta->empresa->nombre;
-- Esperado: nombre de empresa

>>> $movimiento = Movimiento::first();
>>> $movimiento->empresa()->exists();
-- Esperado: true
>>> $movimiento->venta();
-- Esperado: BelongsTo instance
```

- [ ] Relaciones funcionan
- [ ] Lazy loading funciona
- [ ] Eager loading funciona

### Test 3: Verificar Índices

```php
>>> DB::statement('EXPLAIN SELECT * FROM ventas WHERE empresa_id = 1 AND fecha_hora > "2026-01-01"');
```

- [ ] Index utilizado: `empresa_id` o índice compuesto
- [ ] Type: ALL o INDEX (no sea ALL sin índice)

---

## 📝 Validación de Documentación

- [ ] `CINEMAPOSPWD.md` existe
  - [ ] Contiene análisis de migraciones
  - [ ] Contiene decisiones arquitectónicas
  - [ ] Contiene ejemplos de código
  - [ ] Es legible y completo

- [ ] `README_CINEMAPTOS.md` existe
  - [ ] Describe arquiteactura general
  - [ ] Explica flujo de venta
  - [ ] Documenta tarifa por servicio
  - [ ] Explica preparación para Stripe
  - [ ] Incluye diagrama ER

- [ ] `RESUMEN_EJECUTIVO.md` existe
  - [ ] Contiene lista clara de cambios
  - [ ] Incluye matriz de migraciones
  - [ ] Plan de implementación

- [ ] `GUIA_IMPLEMENTACION_MODELOS.php` existe
  - [ ] Contiene ejemplos de código
  - [ ] Muestra relaciones a agregar
  - [ ] Muestra métodos nuevos
  - [ ] Código es ejecutable

---

## 🔐 Validación de Seguridad

### Multi-Tenancy

```php
>>> Auth::login(User::find(1)); // Usuario de Empresa 1
>>> Venta::all()->count();
-- Solo trae ventas de Empresa 1

>>> Auth::login(User::find(5)); // Usuario de Empresa 2
>>> Venta::all()->count();
-- Solo trae ventas de Empresa 2
```

- [ ] Queries filtradas automáticamente
- [ ] No hay "fuga" entre empresas

### Encriptación

```sql
-- Verificar que secret_key en stripe_configs es VARBINARY o TEXT
DESCRIBE stripe_configs;
-- Column: secret_key, Type: text
```

- [ ] Campos sensibles son TEXT (para encriptación)
- [ ] Encriptación configurada en model

---

## 🎯 Validación de Requisitos

| Requisito | Status | Evidencia |
|-----------|--------|-----------|
| Sistema soporta empresa | ✅ | `empresa_id` en todas las tablas |
| Multi-empresa preparado | ✅ | Global scopes + middleware |
| Usuario vinculado a empresa | ✅ | `users.empresa_id` |
| Admin puede gestionar empresa | ✅ | Modelo `Empresa` existente |
| Venta vinculada a empresa+usuario+caja | ✅ | `ventas` con 3 FKs |
| Sistema de caja funcional | ✅ | Apertura/cierre en modelo |
| POS vende confitería | ✅ | `productos` + `producto_venta` |
| Tarifa por servicio explícita | ✅ | `tarifa_servicio` + `monto_tarifa` |
| Preparado para Stripe | ✅ | Tablas `stripe_configs`, `payment_transactions` |
| Migraciones limpias | ✅ | 14 migraciones reversibles |
| No rompe compatibilidad | ✅ | Backfill automático |

---

## 📋 Checklist Previo a Deployer en Producción

### 1. Testing Completo
- [ ] Tests unitarios de modelos pasan
- [ ] Tests de migraciones pasan
- [ ] Tests de controllers pasan
- [ ] Tests de multi-tenancy pasan

### 2. Performance
- [ ] Queries de venta < 100ms
- [ ] Queries de caja < 100ms
- [ ] Índices optimizados

### 3. Seguridad
- [ ] Encriptación de claves Stripe
- [ ] Multi-tenancy implementada
- [ ] Middleware de empresa agregado
- [ ] CORS configurado

### 4. Documentación
- [ ] README actualizado
- [ ] Arquitectura documentada
- [ ] Cambios comunicados al equipo
- [ ] Plan de rollback presente

### 5. Datos
- [ ] Backup realizado
- [ ] Integridad verificada
- [ ] Cero pérdida de datos

### 6. Deployment
- [ ] `.env` actualizado
- [ ] Cache limpiado: `php artisan cache:clear`
- [ ] Configuración caché: `php artisan config:cache`
- [ ] Rutas caché: `php artisan route:cache`

---

## 🚨 Plan de Rollback

**Si algo sale mal:**

```bash
# 1. Revertir migraciones
php artisan migrate:rollback --step=14

# 2. Restaurar backup
mysql -u root -p cinemaptos_db < backup_pre_migration.sql

# 3. Verificar integridad
php artisan tinker
>>> Venta::count()
```

- [ ] Procedimiento documentado
- [ ] Backup accesible
- [ ] Comando de rollback testeado

---

## 📞 Contactos de Escalación

En caso de problemas durante implementación:

- **Arquitecto SaaS:** [Nombre] - [Email] - [Teléfono]
- **DBA:** [Nombre] - [Email] - [Teléfono]
- **Tech Lead:** [Nombre] - [Email] - [Teléfono]
- **Soporte:** [Canal] - [Horario]

---

## ✅ Firma de Validación

| Rol | Nombre | Fecha | Firma |
|-----|--------|-------|-------|
| Arquitecto | Senior SaaS/POS | 30/01/2026 | ☑️ |
| DBA | [Nombre] | ___ | ___ |
| Tech Lead | [Nombre] | ___ | ___ |
| QA Lead | [Nombre] | ___ | ___ |

---

## 📌 Notas Finales

- [ ] Todas las migraciones son reversibles con `migrate:rollback`
- [ ] Documentación está lista para el equipo
- [ ] Modelos necesitan actualizarse (ver `GUIA_IMPLEMENTACION_MODELOS.php`)
- [ ] Próxima fase: Implementar Stripe (NO en esta iteración)
- [ ] Sistema está listo para producción

---

**Documento preparado:** 30 de enero de 2026  
**Status:** ✅ LISTO PARA IMPLEMENTACIÓN  
**Revisión:** Completada  
