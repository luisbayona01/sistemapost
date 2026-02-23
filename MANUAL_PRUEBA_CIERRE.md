# 📘 MANUAL DE PRUEBA - CIERRE DE CAJA PASO A PASO

## 🎯 Objetivo
Validar el flujo completo de apertura, ventas y cierre de caja con valores controlados y fáciles de seguir.

---

## 📋 ESCENARIO DE PRUEBA

### Valores Controlados
```
Base inicial:        $50,000
Ventas en efectivo:  $100,000
Ventas con tarjeta:  $50,000
Total vendido:       $150,000

Efectivo esperado:   $150,000 ($50k base + $100k ventas)
Tarjeta esperada:    $50,000
```

---

## 🔄 PASO 0: RESET (Solo si hay datos de prueba)

### Opción A: Reset por SQL (Recomendado)
1. Abre phpMyAdmin o tu cliente MySQL
2. Ejecuta el script: `RESET_CIERRE_PRUEBA.sql`
3. Verifica que los contadores estén en 0

### Opción B: Reset por Artisan (Alternativo)
```bash
php artisan tinker
```
```php
// Eliminar ventas de hoy
\App\Models\Venta::whereDate('fecha_hora', today())->delete();

// Eliminar cajas de hoy
\App\Models\Caja::whereDate('fecha_hora_apertura', today())->delete();

// Eliminar movimientos de hoy
\App\Models\Movimiento::whereDate('created_at', today())->delete();

// Verificar
echo "Ventas hoy: " . \App\Models\Venta::whereDate('fecha_hora', today())->count() . "\n";
echo "Cajas hoy: " . \App\Models\Caja::whereDate('fecha_hora_apertura', today())->count() . "\n";
```

---

## 📍 PASO 1: APERTURA DE CAJA

### 1.1 Acceder al POS
- URL: `http://localhost/sistemapost/pos`
- O desde el menú: **POS / Punto de Venta**

### 1.2 Modal de Apertura (debe aparecer automáticamente)
Si no tienes caja abierta, verás un modal:

```
┌─────────────────────────────────────┐
│  🔓 APERTURA DE CAJA                │
├─────────────────────────────────────┤
│  Monto Inicial en Efectivo:         │
│  $ [50000]                          │
│                                     │
│  [Confirmar Apertura]               │
└─────────────────────────────────────┘
```

**Acción**: Ingresa `50000` y confirma

**Resultado Esperado**:
- ✅ Mensaje: "Caja abierta correctamente con $50,000"
- ✅ Redirección al POS
- ✅ Puedes empezar a vender

---

## 📍 PASO 2: REALIZAR VENTAS DE PRUEBA

### 2.1 Venta en Efectivo ($100,000)

**Opción A: Venta de Confitería**
1. Selecciona categoría "Dulcería" o "Bebidas"
2. Agrega productos hasta llegar a $100,000
   - Ejemplo: 10 combos de $10,000 c/u
3. Método de pago: **EFECTIVO**
4. Click en **FINALIZAR VENTA**

**Opción B: Venta de Cinema**
1. Selecciona "Cinema" en el menú lateral
2. Elige una función
3. Selecciona asientos hasta $100,000
   - Ejemplo: 10 entradas de $10,000 c/u
4. Método de pago: **EFECTIVO**
5. Click en **FINALIZAR VENTA**

**Resultado Esperado**:
- ✅ Modal: "¡VENTA EXITOSA! $100,000"
- ✅ Opción de imprimir ticket

### 2.2 Venta con Tarjeta ($50,000)

Repite el proceso anterior pero:
- Total: $50,000
- Método de pago: **TARJETA**

**Resultado Esperado**:
- ✅ Modal: "¡VENTA EXITOSA! $50,000"

### 2.3 Verificación Rápida
En este punto deberías tener:
- 2 ventas registradas
- Total vendido: $150,000
- Efectivo: $100,000
- Tarjeta: $50,000

---

## 📍 PASO 3: CIERRE DE CAJA (EL MOMENTO CRÍTICO)

### 3.1 Acceder al Cierre

**Opción A: Desde el POS**
1. Click en el botón de engranaje **⚙️ AJUSTES** (esquina superior derecha)
2. Click en **🔒 Cerrar Caja**

**Opción B: Desde el Panel Admin**
1. Ve a **Cajas → Estado de Cajas**
2. Busca tu caja ABIERTA
3. Click en **Cerrar Caja**

### 3.2 Formulario de Cierre

Verás un formulario con 3 secciones:

#### SECCIÓN 1: RESUMEN DEL TURNO (Solo lectura)
```
┌─────────────────────────────────────┐
│ Ventas Entradas (Cine):  $100,000  │ (o $0 si no vendiste cine)
│ Ventas Dulcería:         $50,000   │ (o $150k si todo fue confitería)
│ ─────────────────────────────────── │
│ Ventas con Tarjeta:      $50,000   │
│ ─────────────────────────────────── │
│ EFECTIVO ESPERADO:       $150,000  │ ⭐ ESTE ES EL NÚMERO CLAVE
└─────────────────────────────────────┘
```

**¿De dónde sale $150,000?**
```
Base inicial:     $50,000
+ Ventas efectivo: $100,000
─────────────────────────
= Efectivo esperado: $150,000
```

#### SECCIÓN 2: VALIDACIÓN DE EFECTIVO (Obligatorio)
```
┌─────────────────────────────────────┐
│ 💵 Efectivo Contado en Caja         │
│ $ [___________]                     │
│                                     │
│ ℹ️ Incluye el fondo inicial del     │
│    cambio en tu conteo final.       │
└─────────────────────────────────────┘
```

**Acción**: 
- Cuenta el dinero físico en la caja
- **ESCENARIO IDEAL**: Ingresa `150000`
- **ESCENARIO FALTANTE**: Ingresa `145000` (faltarían $5,000)
- **ESCENARIO SOBRANTE**: Ingresa `155000` (sobrarían $5,000)

#### SECCIÓN 3: VALIDACIÓN DE TARJETA (Opcional)
```
┌─────────────────────────────────────┐
│ 💳 Total Vouchers Datáfono          │
│ $ [50000] ← Pre-llenado             │
│                                     │
│ ℹ️ Sistema espera: $50,000.         │
│    Verifica que coincida con tus    │
│    vouchers físicos.                │
└─────────────────────────────────────┘
```

**Acción**:
- Cuenta tus vouchers del datáfono
- **ESCENARIO IDEAL**: Deja `50000` (ya está pre-llenado)
- **ESCENARIO VOUCHER FALTANTE**: Cambia a `45000`

#### SECCIÓN 4: OBSERVACIONES (Opcional)
```
┌─────────────────────────────────────┐
│ 📝 Observaciones / Novedades        │
│ [_________________________________] │
│ [_________________________________] │
└─────────────────────────────────────┘
```

**Acción**: 
- Si hay diferencia, explica: "Billete falso de $5,000"
- Si todo está bien, deja en blanco

### 3.3 Confirmar Cierre

Click en: **✅ CONFIRMAR CIERRE**

**Resultado Esperado**:
- ✅ Mensaje: "Caja cerrada exitosamente. Arqueo completado."
- ✅ Redirección al reporte de cierre

---

## 📍 PASO 4: REVISAR REPORTE DE CIERRE

### 4.1 Reporte Individual

Verás 5 columnas:

```
┌──────────────┬──────────────┬──────────────┬──────────────┬──────────┐
│ Efectivo     │ Efectivo     │ Diferencia   │ Diferencia   │ Estado   │
│ Esperado     │ Declarado    │ Efectivo     │ Tarjeta      │          │
├──────────────┼──────────────┼──────────────┼──────────────┼──────────┤
│ $150,000     │ $150,000     │ $0           │ $0           │ CUADRADA │
└──────────────┴──────────────┴──────────────┴──────────────┴──────────┘
```

**Si declaraste $145,000 (faltante)**:
```
┌──────────────┬──────────────┬──────────────┬──────────────┬──────────────┐
│ $150,000     │ $145,000     │ -$5,000      │ $0           │ CON          │
│              │              │ (FALTANTE)   │              │ DIFERENCIA   │
└──────────────┴──────────────┴──────────────┴──────────────┴──────────────┘
```

### 4.2 Descargar PDF

Click en **📄 Descargar PDF**

El PDF mostrará dos secciones:

#### ARQUEO DE EFECTIVO
```
(+) Saldo Inicial / Base:        $50,000
(+) Ingresos Efectivo (Ventas):  $100,000
(+) Ingresos Manuales:           $0
(-) Gastos / Egresos:            $0
─────────────────────────────────────────
(=) EFECTIVO ESPERADO EN CAJA:   $150,000

Monto Declarado:                 $150,000
Diferencia:                      $0 ✅
```

#### ARQUEO DE TARJETA/DATÁFONO
```
Vouchers Declarados:             $50,000
Ventas con Tarjeta (Sistema):    $50,000
Diferencia:                      $0 ✅
```

---

## 📍 PASO 5: CONSOLIDADO DEL DÍA (Admin)

### 5.1 Acceder al Consolidado

- Ve a **Cajas → Cerrar el Día**
- O desde "Estado de Cajas" → botón **Cerrar el Día**

### 5.2 Vista Consolidada

```
┌─────────────────────────────────────────────────────────┐
│              CONSOLIDADO DEL DÍA - 14/02/2026           │
├─────────────────────────────────────────────────────────┤
│                                                         │
│  Total Entradas (Cine):    $100,000                    │
│  Total Dulcería:           $50,000                     │
│                                                         │
├─────────────────────────────────────────────────────────┤
│              ARQUEO CONSOLIDADO                         │
├───────────────────────────┬─────────────────────────────┤
│ 💵 EFECTIVO               │ 💳 TARJETA/DATÁFONO         │
│ $150,000                  │ $50,000                     │
│ Diferencia: $0            │ Diferencia: $0              │
└───────────────────────────┴─────────────────────────────┘
│                                                         │
│  Total Gran Recaudado: $150,000                        │
└─────────────────────────────────────────────────────────┘
```

**✅ YA NO VERÁS LA CONFUSA "Diferencia Total: -$112,000"**

---

## ✅ CHECKLIST DE VALIDACIÓN

Después de completar todos los pasos, verifica:

- [ ] ✅ La apertura registró correctamente la base de $50,000
- [ ] ✅ Las ventas se registraron (2 ventas: $100k efectivo + $50k tarjeta)
- [ ] ✅ El formulario de cierre muestra "Efectivo Esperado: $150,000"
- [ ] ✅ El formulario de cierre tiene campo de tarjeta pre-llenado con $50,000
- [ ] ✅ El reporte muestra 5 columnas (no 4)
- [ ] ✅ El PDF tiene dos secciones de arqueo (efectivo y tarjeta)
- [ ] ✅ El consolidado del día muestra diferencias separadas
- [ ] ✅ NO aparece "Diferencia Total" confusa

---

## 🐛 TROUBLESHOOTING

### Problema: No veo el campo de tarjeta
**Solución**: Verifica que la migración se ejecutó:
```bash
php artisan migrate:status
```
Busca: `2026_02_14_214400_add_tarjeta_fields_to_cajas_table`

### Problema: Sigo viendo "Diferencia Total: -$112,000"
**Solución**: Limpia la caché de vistas:
```bash
php artisan view:clear
php artisan cache:clear
```

### Problema: El efectivo esperado no coincide
**Solución**: Verifica que la base inicial se registró correctamente:
```sql
SELECT id, saldo_inicial, fecha_hora_apertura 
FROM cajas 
WHERE DATE(fecha_hora_apertura) = CURDATE();
```

---

## 📞 SOPORTE

Si después de seguir estos pasos aún hay confusión:
1. Toma captura de pantalla del formulario de cierre
2. Toma captura del consolidado del día
3. Comparte los valores que ves vs los esperados

**Fecha de creación**: 14/02/2026 16:55
**Versión**: 1.0 - Fase 4.5
