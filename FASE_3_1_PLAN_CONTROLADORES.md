# 🚀 FASE 3.1 - PLAN IMPLEMENTACIÓN CONTROLADORES

**Status:** 📋 PLANIFICACIÓN  
**Objetivo:** Adaptar 25 controladores para multi-tenancy + nuevos modelos  

---

## 🎯 CONTROLADORES A ACTUALIZAR - PRIORIDAD

### 🔴 CRÍTICOS (Requieren cambios profundos)

#### 1. **ventaController.php** (PRIORIDAD: 1)

**Cambios Necesarios:**

```
1. Capturar empresa_id en create()
   └─ Obtener de auth()->user()->empresa_id
   
2. Validar caja abierta en create()
   └─ Usar Middleware: check-caja-aperturada-user
   
3. En store():
   └─ Agregar empresa_id
   └─ Calcular tarifa con calcularTarifa()
   └─ Registrar movimiento de caja (evento existente)
   
4. Usar Global Scopes
   └─ index() ahora filtra automáticamente por empresa
   
5. Usar métodos del modelo
   └─ calcularTarifa()
   └─ getTotalConTarifa()
```

**Líneas a Cambiar:**
- L45-49: `create()` - Obtener empresa y validar caja
- L55-70: `create()` - Pasar empresa a vista
- L90-100: `store()` - Agregar empresa_id a create()
- L101-125: Usar métodos del modelo

---

#### 2. **CajaController.php** (PRIORIDAD: 2)

**Cambios Necesarios:**

```
1. En store():
   └─ Agregar empresa_id
   └─ Validar que usuario no tenga caja abierta
   
2. Crear método close():
   └─ POST /cajas/{id}/close
   └─ Usar método cerrar() del modelo
   └─ Calcular saldo final
   
3. index():
   └─ Global Scope filtra automáticamente
   
4. Usar métodos del modelo:
   └─ cerrar()
   └─ calcularSaldo()
   └─ estaAbierta()
   └─ estaCerrada()
```

**Líneas a Cambiar:**
- L38-50: `store()` - Validar empresa + caja abierta
- NUEVO: `close()` - Cierre de caja
- NUEVO: Vista `caja/close.blade.php`

---

#### 3. **MovimientoController.php** (PRIORIDAD: 3)

**Cambios Necesarios:**

```
1. En create():
   └─ Obtener caja y validar que pertenece a usuario/empresa
   
2. En store():
   └─ Agregar empresa_id
   └─ Agregar user_id si no existe
   
3. index():
   └─ Global Scope filtra automáticamente
   
4. Usar métodos del modelo:
   └─ esIngreso()
   └─ esEgreso()
```

**Líneas a Cambiar:**
- L30-35: `create()` - Validar caja
- L45-50: `store()` - Agregar empresa_id

---

### 🟡 IMPORTANTES (Requieren ajustes moderados)

#### 4. **ProductoController.php**

**Cambios:** Agregar empresa_id a queries
```php
// ❌ ACTUAL
$productos = Producto::where('estado', 1)->get();

// ✅ NUEVO
$productos = auth()->user()->empresa->productos()
    ->where('estado', 1)
    ->get();
```

---

#### 5. **compraController.php**

**Cambios:** Agregar empresa_id en create/store

---

#### 6. **clienteController.php**

**Cambios:** Agregar empresa_id en queries

---

#### 7. **proveedorController.php**

**Cambios:** Agregar empresa_id en queries

---

#### 8. **InventarioController.php**

**Cambios:** Usar métodos del modelo
- aumentarStock()
- disminuirStock()
- estaVencido()
- esStockBajo()

---

#### 9. **KardexController.php**

**Cambios:** Filtrar por empresa_id

---

#### 10. **userController.php**

**Cambios:** 
- Agregar empresa_id en create/store
- Validar que usuario pertenece a empresa

---

#### 11. **EmpleadoController.php**

**Cambios:**
- Agregar empresa_id en create/store
- Relación user() → users() (HasMany)

---

#### 12. **EmpresaController.php**

**Cambios:**
- Actualizar con nuevas relaciones (13 HasMany)
- Usar métodos: calcularImpuesto(), getAbreviaturaImpuesto()

---

#### 13. **homeController.php**

**Cambios:** Filtrar dashboard por empresa_id

```php
// ❌ ACTUAL
$totalVentasPorDia = DB::table('ventas')
    ->selectRaw('DATE(created_at) as fecha, SUM(total) as total')
    ->where('created_at', '>=', Carbon::now()->subDays(7))
    ->groupBy(DB::raw('DATE(created_at)'))
    ->get();

// ✅ NUEVO
$totalVentasPorDia = auth()->user()->empresa->ventas()
    ->selectRaw('DATE(created_at) as fecha, SUM(total) as total')
    ->where('created_at', '>=', Carbon::now()->subDays(7))
    ->groupBy(DB::raw('DATE(created_at)'))
    ->get();
```

---

### 🟢 MENORES (Ajustes simples)

#### 14-25: Otros controladores

- categoriaController: empresa_id
- marcaController: empresa_id
- ExportExcelController: Filtrar por empresa
- ExportPDFController: Filtrar por empresa
- ImportExcelController: empresa_id automático
- ActivityLogController: Filtrar por empresa
- profileController: Sin cambios
- loginController: Sin cambios
- logoutController: Sin cambios
- roleController: Sin cambios

---

## 📝 PLANTILLA DE CAMBIO ESTÁNDAR

Para cada controlador, aplicar este patrón:

### En create():
```php
public function create()
{
    // Obtener empresa actual
    $empresa = auth()->user()->empresa;
    
    // Otras variables necesarias
    $otras = ...;
    
    return view('...create', compact('empresa', 'otras'));
}
```

### En store():
```php
public function store(Request $request)
{
    // Validar
    $validated = $request->validate([...]);
    
    // Agregar empresa_id y user_id
    $modelo = Modelo::create(array_merge($validated, [
        'empresa_id' => auth()->user()->empresa_id,
        'user_id' => Auth::id(), // si aplica
    ]));
    
    // Log y redireccionar
    ActivityLogService::log(...);
    return redirect()->with('success', ...);
}
```

### En index():
```php
public function index()
{
    // Global Scope filtra automáticamente
    // Solo si necesitas filtro adicional
    $modelos = auth()->user()->empresa->modelos()
        ->latest()
        ->get();
    
    return view('...index', compact('modelos'));
}
```

---

## 🔄 VALIDACIONES A USAR

### Caja Abierta (en ventaController, MovimientoController)
```php
$cajaAbierta = auth()->user()->empresa->cajas()
    ->abierta() // Scope abierta()
    ->where('user_id', Auth::id())
    ->first();

if (!$cajaAbierta) {
    return redirect()->route('cajas.create')
        ->with('error', 'Debes abrir una caja primero');
}
```

### Usuario Pertenece a Empresa
```php
if ($usuario->empresa_id !== auth()->user()->empresa_id) {
    abort(403, 'No tienes permiso');
}
```

### Modelo Pertenece a Empresa
```php
if ($venta->empresa_id !== auth()->user()->empresa_id) {
    abort(403, 'No tienes permiso');
}
```

---

## 📋 ORDEN DE IMPLEMENTACIÓN RECOMENDADO

### Semana 1: Controladores Críticos
1. ventaController (2 h)
2. CajaController (1.5 h)
3. MovimientoController (1.5 h)

### Semana 2: Controladores Importantes
4. ProductoController (1 h)
5. compraController (1 h)
6. clienteController (0.5 h)
7. proveedorController (0.5 h)
8. InventarioController (1 h)
9. KardexController (0.5 h)
10. userController (1 h)
11. EmpleadoController (0.5 h)
12. EmpresaController (1 h)
13. homeController (1 h)

### Semana 3: Controladores Menores
14-25. Otros (2 h)

---

## ✅ VALIDACIÓN DESPUÉS DE CADA CONTROLADOR

```
After each controller:
1. Verificar que no haya errores de sintaxis
2. Prueba: crear recurso
3. Prueba: listar recursos
4. Prueba: ver recurso
5. Prueba: filtrado por empresa funciona
6. Verificar logs en ActivityLogService
```

---

## 🎯 PRÓXIMO PASO

**Paso 1: Actualizar ventaController.php**
- Agregar empresa_id en create() y store()
- Validar caja abierta
- Usar métodos del modelo
- Registrar movimiento

EOF
cat /var/www/html/Punto-de-Venta/FASE_3_1_PLAN_CONTROLADORES.md
