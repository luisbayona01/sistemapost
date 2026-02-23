# 🎬 Cinema API - Documentación Completa

## 📋 Índice
1. [Introducción](#introducción)
2. [API Pública (Sin Autenticación)](#api-pública)
3. [API Administrativa (Con Autenticación)](#api-administrativa)
4. [Ejemplos de Integración](#ejemplos-de-integración)
5. [Códigos de Respuesta](#códigos-de-respuesta)

---

## Introducción

Esta API está diseñada con arquitectura **API-First** para permitir la integración con cualquier frontend (sitio web, app móvil, kiosco, etc.) de forma completamente desacoplada.

### Base URL
```
Producción: https://tudominio.com/api
Desarrollo: http://localhost:8000/api
```

### Formato de Respuesta
Todas las respuestas siguen este formato JSON:

```json
{
  "success": true|false,
  "message": "Mensaje descriptivo",
  "data": { ... }
}
```

---

## API Pública

### 🎥 Obtener Cartelera

**Endpoint:** `GET /api/cinema/cartelera`

**Descripción:** Obtiene todas las películas actualmente en cartelera con su información completa.

**Respuesta:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "titulo": "Avatar: El Camino del Agua",
      "sinopsis": "Jake Sully vive con su nueva familia...",
      "duracion": "192 min",
      "clasificacion": "PG-13",
      "genero": "Ciencia Ficcion",
      "afiche": "https://tudominio.com/storage/productos/avatar.jpg",
      "trailer_url": "https://youtube.com/watch?v=...",
      "distribuidor": "20th Century Studios",
      "fecha_estreno": "2026-01-15",
      "fecha_fin": "2026-03-15"
    }
  ]
}
```

**Ejemplo de uso (JavaScript):**
```javascript
fetch('https://tudominio.com/api/cinema/cartelera')
  .then(res => res.json())
  .then(data => {
    data.data.forEach(pelicula => {
      console.log(pelicula.titulo, pelicula.afiche);
    });
  });
```

---

### 📅 Obtener Funciones de una Película

**Endpoint:** `GET /api/cinema/peliculas/{id}/funciones`

**Descripción:** Obtiene todos los horarios disponibles de una película específica.

**Parámetros:**
- `{id}` - ID de la película

**Respuesta:**
```json
{
  "success": true,
  "data": [
    {
      "id": 15,
      "fecha_hora": "2026-02-05 18:00",
      "sala": {
        "id": 1,
        "nombre": "Sala 1 (76)",
        "capacidad": 76
      },
      "asientos_disponibles": 45,
      "precios": [
        {
          "id": 1,
          "tipo": "General",
          "precio": 30000
        },
        {
          "id": 2,
          "tipo": "Niños/Tercera Edad",
          "precio": 25000
        }
      ]
    }
  ]
}
```

**Ejemplo de uso (JavaScript):**
```javascript
const peliculaId = 1;
fetch(`https://tudominio.com/api/cinema/peliculas/${peliculaId}/funciones`)
  .then(res => res.json())
  .then(data => {
    // Mostrar horarios disponibles
    data.data.forEach(funcion => {
      console.log(`${funcion.fecha_hora} - ${funcion.asientos_disponibles} disponibles`);
    });
  });
```

---

### 💺 Obtener Mapa de Asientos

**Endpoint:** `GET /api/cinema/funciones/{id}/asientos`

**Descripción:** Obtiene el mapa completo de asientos de una función, indicando cuáles están disponibles u ocupados.

**Parámetros:**
- `{id}` - ID de la función

**Respuesta:**
```json
{
  "success": true,
  "data": {
    "funcion_id": 15,
    "sala": "Sala 1 (76)",
    "capacidad_total": 76,
    "asientos_disponibles": 45,
    "mapa": [
      {
        "fila": "A",
        "num": "1",
        "col": 1,
        "tipo": "asiento",
        "disponible": true
      },
      {
        "fila": "A",
        "num": "2",
        "col": 2,
        "tipo": "asiento",
        "disponible": false
      },
      {
        "fila": "A",
        "num": null,
        "col": 3,
        "tipo": "pasillo",
        "disponible": null
      }
    ]
  }
}
```

**Ejemplo de uso (JavaScript):**
```javascript
const funcionId = 15;
fetch(`https://tudominio.com/api/cinema/funciones/${funcionId}/asientos`)
  .then(res => res.json())
  .then(data => {
    const mapa = data.data.mapa;
    mapa.forEach(seat => {
      if (seat.tipo === 'asiento') {
        const estado = seat.disponible ? 'libre' : 'ocupado';
        console.log(`${seat.fila}${seat.num}: ${estado}`);
      }
    });
  });
```

---

### 💰 Obtener Precios

**Endpoint:** `GET /api/cinema/precios`

**Descripción:** Obtiene la lista de precios activos (controlados desde el admin).

**Respuesta:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "nombre": "General",
      "precio": 30000,
      "descripcion": "Precio estándar"
    },
    {
      "id": 2,
      "nombre": "Niños/Tercera Edad",
      "precio": 25000,
      "descripcion": "Descuento especial"
    }
  ]
}
```

---

### 🔒 Reservar Asientos

**Endpoint:** `POST /api/cinema/reservar`

**Descripción:** Reserva temporalmente asientos por 15 minutos mientras el usuario completa el pago.

**Body (JSON):**
```json
{
  "funcion_id": 15,
  "asientos": ["A1", "A2", "A3"]
}
```

**Respuesta Exitosa:**
```json
{
  "success": true,
  "message": "Asientos reservados exitosamente",
  "data": {
    "asientos_reservados": ["A1", "A2", "A3"],
    "expira_en_minutos": 15
  }
}
```

**Respuesta de Error:**
```json
{
  "success": false,
  "message": "El asiento A2 no está disponible"
}
```

**Ejemplo de uso (JavaScript):**
```javascript
fetch('https://tudominio.com/api/cinema/reservar', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
  },
  body: JSON.stringify({
    funcion_id: 15,
    asientos: ['A1', 'A2']
  })
})
.then(res => res.json())
.then(data => {
  if (data.success) {
    console.log('Reserva exitosa. Tienes 15 minutos para pagar.');
  }
});
```

---

### ✅ Confirmar Compra

**Endpoint:** `POST /api/cinema/confirmar-compra`

**Descripción:** Confirma la compra después de procesar el pago. Genera el ticket con QR.

**Body (JSON):**
```json
{
  "funcion_id": 15,
  "asientos": ["A1", "A2"],
  "precio_entrada_id": 1,
  "metodo_pago": "tarjeta",
  "referencia_pago": "TXN_123456789"
}
```

**Respuesta:**
```json
{
  "success": true,
  "message": "Compra confirmada exitosamente",
  "data": {
    "venta_id": 456,
    "ticket_url": "https://tudominio.com/tickets/456.pdf",
    "qr_code": "https://tudominio.com/qr/456.png"
  }
}
```

---

## API Administrativa

**Autenticación:** Todos los endpoints requieren token de Sanctum.

### Header requerido:
```
Authorization: Bearer {token}
```

---

### 🎬 Gestión de Películas

#### Listar Películas
**Endpoint:** `GET /api/admin/peliculas`

**Query Params:**
- `estado` - Filtrar por estado (cartelera, proximamente, archivada)
- `search` - Buscar por nombre
- `per_page` - Resultados por página (default: 15)

**Respuesta:** Paginación estándar de Laravel

---

#### Crear Película
**Endpoint:** `POST /api/admin/peliculas`

**Body:**
```json
{
  "nombre": "Título de la película",
  "sinopsis": "Descripción completa...",
  "duracion": "120 min",
  "clasificacion": "PG-13",
  "genero": "Accion",
  "trailer_url": "https://youtube.com/...",
  "distribuidor_id": 1,
  "estado_pelicula": "cartelera",
  "fecha_estreno": "2026-02-10",
  "fecha_fin_exhibicion": "2026-04-10"
}
```

---

#### Actualizar Película
**Endpoint:** `PUT /api/admin/peliculas/{id}`

**Body:** Mismos campos que crear (todos opcionales)

---

### 📅 Gestión de Funciones

#### Listar Funciones
**Endpoint:** `GET /api/admin/funciones`

**Query Params:**
- `fecha_desde` - Filtrar desde fecha
- `fecha_hasta` - Filtrar hasta fecha
- `per_page` - Resultados por página

---

#### Crear Función
**Endpoint:** `POST /api/admin/funciones`

**Body:**
```json
{
  "sala_id": 1,
  "producto_id": 5,
  "fecha_hora": "2026-02-10 18:00:00",
  "precio": 30000,
  "precios_entrada": [1, 2]
}
```

**Nota:** Los asientos se generan automáticamente según la configuración de la sala.

---

#### Actualizar Función
**Endpoint:** `PUT /api/admin/funciones/{id}`

**Body:**
```json
{
  "fecha_hora": "2026-02-10 20:00:00",
  "force_update": false
}
```

**Validación:** Si hay asientos vendidos, retorna error 400 con `requires_confirmation: true`. Enviar `force_update: true` para forzar.

---

#### Eliminar Función
**Endpoint:** `DELETE /api/admin/funciones/{id}`

**Validación:** Bloqueado si hay ventas. Retorna error 400.

---

### 💰 Gestión de Precios

#### Listar Precios
**Endpoint:** `GET /api/admin/precios`

---

#### Actualizar Precio
**Endpoint:** `PUT /api/admin/precios/{id}`

**Body:**
```json
{
  "nombre": "General",
  "precio": 35000,
  "activo": true
}
```

**Nota:** Los cambios se reflejan inmediatamente en la API pública.

---

### 📊 Reportes

#### Reporte de Ventas
**Endpoint:** `GET /api/admin/reportes/ventas`

**Query Params:**
```
fecha_desde=2026-02-01
fecha_hasta=2026-02-28
pelicula_id=5 (opcional)
sala_id=1 (opcional)
```

**Respuesta:**
```json
{
  "success": true,
  "data": {
    "periodo": {
      "desde": "2026-02-01",
      "hasta": "2026-02-28"
    },
    "total_ventas": 450,
    "total_ingresos": 13500000,
    "ocupacion_promedio": 68.5,
    "peliculas_mas_vendidas": [...]
  }
}
```

---

## Ejemplos de Integración

### Ejemplo 1: Sitio Web con HTML/CSS/JS Puro

```html
<!DOCTYPE html>
<html>
<head>
    <title>Cinema Paraíso</title>
</head>
<body>
    <div id="cartelera"></div>

    <script>
        // Cargar cartelera
        fetch('https://tudominio.com/api/cinema/cartelera')
            .then(res => res.json())
            .then(data => {
                const container = document.getElementById('cartelera');
                data.data.forEach(pelicula => {
                    container.innerHTML += `
                        <div class="pelicula">
                            <img src="${pelicula.afiche}" alt="${pelicula.titulo}">
                            <h3>${pelicula.titulo}</h3>
                            <p>${pelicula.sinopsis}</p>
                            <button onclick="verHorarios(${pelicula.id})">Ver Horarios</button>
                        </div>
                    `;
                });
            });

        function verHorarios(peliculaId) {
            fetch(`https://tudominio.com/api/cinema/peliculas/${peliculaId}/funciones`)
                .then(res => res.json())
                .then(data => {
                    // Mostrar horarios disponibles
                    console.log(data.data);
                });
        }
    </script>
</body>
</html>
```

---

### Ejemplo 2: React/Next.js

```javascript
// components/Cartelera.jsx
import { useEffect, useState } from 'react';

export default function Cartelera() {
  const [peliculas, setPeliculas] = useState([]);

  useEffect(() => {
    fetch('https://tudominio.com/api/cinema/cartelera')
      .then(res => res.json())
      .then(data => setPeliculas(data.data));
  }, []);

  return (
    <div className="grid grid-cols-3 gap-4">
      {peliculas.map(pelicula => (
        <div key={pelicula.id} className="pelicula-card">
          <img src={pelicula.afiche} alt={pelicula.titulo} />
          <h3>{pelicula.titulo}</h3>
          <p>{pelicula.duracion} • {pelicula.clasificacion}</p>
        </div>
      ))}
    </div>
  );
}
```

---

## Códigos de Respuesta

| Código | Significado |
|--------|-------------|
| 200 | Éxito |
| 201 | Creado exitosamente |
| 400 | Error de validación / Solicitud inválida |
| 401 | No autenticado (falta token) |
| 403 | No autorizado (sin permisos) |
| 404 | Recurso no encontrado |
| 500 | Error interno del servidor |

---

## Notas Importantes

1. **CORS:** Configurar dominios permitidos en `config/cors.php`
2. **Rate Limiting:** API pública limitada a 60 req/min por IP
3. **Caché:** Respuestas de cartelera cacheadas por 5 minutos
4. **Webhooks:** Disponibles para notificar cambios en tiempo real (próximamente)

---

**Versión:** 1.0  
**Última actualización:** 2026-02-05  
**Soporte:** soporte@cinemaparaiso.com
