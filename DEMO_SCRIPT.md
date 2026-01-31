# CinemaPOS - Demo Comercial (5 minutos)

**Objetivo:** Demostrar el flujo completo de un POS moderno con pagos con tarjeta integrados.  
**Tiempo:** 5 minutos  
**Flujo:** Login → Abrir Caja → Realizar Venta → Pagar con Stripe → Cerrar Caja  

---

## Pre-Demo Setup (Antes de la sesión)

### 1. Base de Datos Limpia

```bash
# En la terminal, ejecuta:
php artisan migrate:fresh
php artisan db:seed --class=DemoSeeder
```

**Resultado esperado:**
```
✅ Demo seeder completado!
   Empresa: Cinema Fénix
   Admin: admin@cinefenix.local / password123
   Stripe: MODO TEST habilitado
```

### 2. Servidor en Ejecución

```bash
# Terminal 1:
php artisan serve

# Terminal 2 (opcional, para webhooks):
stripe listen --forward-to http://localhost:8000/webhooks/stripe
```

### 3. Preparar Navegador

- Abrir: http://localhost:8000
- Tener a mano datos de tarjeta de prueba
- Limpiar cookies si es primera vez

---

## Demo Script - 5 Minutos

### MINUTO 0:00 - Introduce CinemaPOS

**Qué decir:**
> "Vamos a ver CinemaPOS en acción. Es un sistema POS moderno diseñado para cines, pero funciona para cualquier negocio. Vamos a hacer una transacción completa: desde abrir la caja hasta procesar un pago con tarjeta."

**Acción:**
- Mostrar pantalla de login
- Señalar diseño limpio (Tailwind CSS), profesional

---

### MINUTO 0:30 - Paso 1: Login

**Acción:**
```
1. Haz clic en campo Email
2. Escribe: admin@cinefenix.local
3. Haz clic en campo Contraseña
4. Escribe: password123
5. Haz clic en "Inicia sesión"
```

**Qué decir:**
> "Sistema multi-empresa. Cada negocio tiene sus propios datos, usuarios y configuración. Aquí estamos usando las credenciales de demostración."

**Resultado esperado:**
- ✅ Dashboard/Panel visible
- Muestra empresa: "Cinema Fénix"
- Barra lateral con opciones

---

### MINUTO 1:00 - Paso 2: Abrir Caja

**Navegación:**
```
Sidebar → Cajas → [Botón] "Aperturar caja"
```

**Acción:**
```
1. Haz clic en campo "Saldo inicial"
2. Escribe: 100 (dólares de fondo de caja)
3. Haz clic en "Aperturar caja"
```

**Qué decir:**
> "Toda venta inicia con la apertura de caja. Es un control auditado - sabemos exactamente cuándo se abre, cuándo se cierra, y qué pasó en el medio."

**Resultado esperado:**
- ✅ Página vuelve a "Cajas"
- Nueva caja en estado "aperturada" (verde)
- Saldo inicial: $100

---

### MINUTO 1:45 - Paso 3: Realizar Venta

**Navegación:**
```
Sidebar → Ventas → [Botón] "Crear venta"
```

**Acción - Datos Generales:**
```
1. Cliente: Selecciona cualquiera (ej: "Juan Pérez")
2. Comprobante: Factura (o Recibo)
3. Método de pago: Stripe (importante para la demostración)
```

**Acción - Agregar Productos:**
```
1. Busca en campo de producto (ej: escribe "Palomita" o similar)
2. Selecciona un producto
3. Cantidad: 2
4. Haz clic en [+] para agregar
5. Repite 1-4 para agregar otro producto
```

**Qué decir:**
> "El sistema mantiene inventario en tiempo real. Cada producto tiene presentaciones, precios, y se controla automáticamente. Vamos a vender dos items diferentes."

**Resultado esperado:**
- ✅ Tabla de productos con cantidades
- Subtotal, impuesto, total calculados automáticamente
- Total visible en la parte inferior

---

### MINUTO 3:00 - Paso 4: Guardar Venta

**Acción:**
```
1. Scroll hacia abajo hasta ver [Botón] "Guardar venta"
2. Haz clic en "Guardar venta"
```

**Resultado esperado:**
- ✅ Página redirige a detalle de venta
- Badge de estado: "PENDIENTE" (amarillo)
- Alert box: "Esta venta está lista para procesar el pago"

**Qué decir:**
> "La venta se registra automáticamente. Ahora está pendiente de pago. Veamos cómo procesar el pago con Stripe - sin dejar el sistema."

---

### MINUTO 3:30 - Paso 5: Pagar con Stripe

**Acción:**
```
1. Haz clic en botón azul: "Pagar con Stripe"
2. Espera 2-3 segundos (modal cargando)
```

**Resultado esperado:**
- ✅ Modal hermoso con gradiente azul
- Muestra: "Confirmar Pago"
- Total a pagar en grande
- Card Element listo para input
- Test card info visible en la parte inferior

**Qué decir:**
> "Nota que los datos de tarjeta se ingresan directamente - nunca pasa por nuestro servidor. Stripe maneja la criptografía. Es PCI Level 1 compliant."

---

### MINUTO 3:50 - Paso 6: Ingresar Tarjeta de Prueba

**Acción:**
```
1. Haz clic en "Información de la Tarjeta"
2. Campo Número: 4242 4242 4242 4242
3. Campo Mes/Año: 12/25 (o futuro)
4. Campo CVC: 123
```

**Resultado esperado:**
- ✅ Card Element muestra validación en tiempo real
- Iconos de Visa/Mastercard aparecen
- No hay errores

**Qué decir:**
> "Tarjeta de prueba Stripe. Completamente segura, no carga nada real. En producción usaríamos las claves de Stripe en vivo."

---

### MINUTO 4:10 - Paso 7: Confirmar Pago

**Acción:**
```
1. Haz clic en botón "Pagar Ahora"
2. Observa spinner de carga (2-3 segundos)
```

**Resultado esperado:**
- ✅ Spinner animado
- Mensaje: "¡Pago completado exitosamente!" (verde)
- Modal se cierra automáticamente
- Página refresca

**Qué decir:**
> "El pago se procesó instantáneamente. Stripe cobra la tarjeta, confirmamos el PaymentIntent, y el webhook actualiza el estado automáticamente."

---

### MINUTO 4:35 - Verificación Final

**Resultado esperado después de actualización:**
- ✅ Badge de estado: "PAGADA" (verde)
- Ya no hay botón "Pagar con Stripe"
- Venta completamente procesada

**Qué decir:**
> "La venta pasó de PENDIENTE a PAGADA. El dinero es nuestro. Ahora cerremos la caja para ver el resumen."

---

### MINUTO 4:50 - Paso 8: Cerrar Caja

**Navegación:**
```
Sidebar → Cajas → [Tabla] → [Botón] "Cerrar"
```

**Acción:**
```
1. Busca la caja que abrimos al inicio (estado: "aperturada")
2. Haz clic en botón rojo "Cerrar"
3. En modal: Haz clic en "Confirmar"
```

**Resultado esperado:**
- ✅ Caja en estado "cerrada" (rojo)
- Saldo final calculado: $100 + (venta con impuestos)
- Auditoría completa del día

**Qué decir:**
> "La caja se cierra con un resumen automático. Tenemos la trazabilidad completa: cuándo se abrió, qué se vendió, cuándo se cerró. Perfecto para auditoría."

---

## Demo Time Breakdown

| Acción | Minuto | Duración | Status |
|--------|--------|----------|--------|
| Introducción | 0:00 | 0:30 | Hablar |
| Login | 0:30 | 0:30 | Acciones |
| Abrir Caja | 1:00 | 0:45 | Acciones |
| Realizar Venta | 1:45 | 1:15 | Acciones |
| Guardar Venta | 3:00 | 0:30 | Resultado |
| Pagar (Stripe Modal) | 3:30 | 0:20 | Demostración |
| Ingresar Tarjeta | 3:50 | 0:20 | Acciones |
| Confirmar Pago | 4:10 | 0:25 | Observar |
| Cerrar Caja | 4:35 | 0:25 | Acciones |
| **TOTAL** | **0:00** | **5:00** | **✅** |

---

## Key Selling Points (Durante Demo)

### Seguridad
> "Los datos de tarjeta no tocan nuestro servidor. Stripe maneja todo con PCI Level 1 compliance."

### Automatización
> "Inventario se actualiza en tiempo real. Impuestos se calculan automáticamente. Auditoría completa."

### Multi-Tenancy
> "Cada cine/negocio tiene su propia instancia segura. Totalmente aislado."

### Integración Stripe
> "Pagos instantáneos con confirmación automática. Los webhooks cierran el circuito sin intervención manual."

### UX Moderna
> "Diseño responsivo, Tailwind CSS. Funciona igual en desktop, tablet, móvil."

---

## Qué Mostrar vs. No Mostrar

### ✅ MOSTRAR
- [x] Login funciona
- [x] Apertura de caja
- [x] Creación de venta
- [x] Selección de productos
- [x] Cálculo automático de totales
- [x] Modal de pago profesional
- [x] Card Element con validación
- [x] Pago exitoso y actualización de estado
- [x] Cierre de caja

### ❌ NO MOSTRAR
- [ ] Seeders / Migraciones
- [ ] Código fuente
- [ ] Base de datos
- [ ] Configuración técnica
- [ ] Logs o errores (si hay)
- [ ] Administración de usuarios/roles
- [ ] Inventario inicial (complejo)

---

## Troubleshooting Rápido

### Problema: "Connection refused"
**Solución:** `php artisan serve` en Terminal 1

### Problema: Tarjeta rechazada
**Solución:** Usa 4242 4242 4242 4242, cualquier fecha futura, cualquier CVC

### Problema: Modal de pago no abre
**Solución:** Verifica que Stripe está configurado en DB. Revisa console (F12) para errores JS

### Problema: Pago dice "error"
**Solución:** Verifica que Stripe Config tiene claves de test válidas

### Problema: Caja no aparece
**Solución:** Refresca (Ctrl+R). A veces tarda un segundo.

---

## Notas Importante

1. **Test Mode Stripe:** Todas las transacciones son de PRUEBA. No cobrar dinero real.

2. **Data Resets:** Después de cada demo, puedes resetear con:
   ```bash
   php artisan migrate:fresh --seed --seeder=DemoSeeder
   ```

3. **Timezones:** El sistema usa UTC. En logs verás horas en UTC.

4. **Permisos:** El usuario admin tiene todos los permisos. Perfecto para demo.

5. **Emails:** No se envían emails en local (está deshabilitado). En producción sí.

---

## Post-Demo Discussion Points

**Pregunta:** "¿Qué les pareció?"

**Puntos para mencionar si surgen preguntas:**

- **Multi-tenancy:** "Cada cine es completamente independiente. Datos seguros."
- **Escalabilidad:** "Funciona desde 1 hasta 1000+ sucursales. Mismo código."
- **Customización:** "Podemos cambiar logos, colores, campos según necesidad."
- **Pagos:** "Soportamos Stripe hoy. Fácil agregar más métodos."
- **Móvil:** "Responsive. Funciona en iPad, tablets, cualquier dispositivo."
- **Soporte:** "Equipo técnico 24/7. Documentación completa."

---

## Summary

**What they saw:**
1. Modern, professional UX
2. Real-time inventory
3. Secure card payments (Stripe)
4. Complete audit trail
5. Fast, intuitive workflow

**Time invested:** 5 minutes  
**Impact:** High - working, production-quality demo  
**Next step:** "¿Quieren probarlo ustedes?" (hands-on trial)

---

**Última actualización:** 30 enero 2026  
**Versión:** 1.0  
**Estado:** Listo para producción  
