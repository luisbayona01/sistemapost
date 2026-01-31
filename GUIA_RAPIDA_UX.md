# 🚀 GUÍA RÁPIDA - PANEL REFACTORIZADO

**Versión:** 1.0 | **Fecha:** 31 Enero 2026 | **Status:** ✅ Producción

---

## 📌 QUICK START

### Agregar Card de Estadística

```blade
<x-dashboard-stat-card
    title="Clientes"                          <!-- Título -->
    :value="$clientes"                        <!-- Número -->
    icon="fa-solid fa-users"                  <!-- Icon -->
    color="blue"                              <!-- Color: blue/green/purple/amber/cyan/indigo/red -->
    actionUrl="{{ route('clientes.index') }}" <!-- Link acción -->
    actionLabel="Ver clientes"                <!-- Texto botón -->
    trend="up"                                <!-- Opcional: up/down -->
    trendValue="+12%"                         <!-- Opcional: variación -->
/>
```

### Colores Disponibles

```blade
color="blue"      <!-- Azul principal -->
color="green"     <!-- Verde éxito -->
color="purple"    <!-- Púrpura info -->
color="amber"     <!-- Ámbar advertencia -->
color="cyan"      <!-- Cian datos -->
color="indigo"    <!-- Índigo datos 2 -->
color="red"       <!-- Rojo error -->
```

---

## 🧭 NAVEGACIÓN

### Agregar Sección en Sidebar

```blade
<x-nav.heading>Mi Sección</x-nav.heading>

<x-nav.nav-link 
    content="Mi Link"
    icon="fa-solid fa-icon"
    :href="route('mi.ruta')" />
```

### Agregar Collapsible

```blade
<x-nav.link-collapsed
    id="collapseNuevo"
    icon="fa-solid fa-icon"
    content="Mi Grupo">
    
    <x-nav.link-collapsed-item 
        :href="route('item1')" 
        content="Item 1" />
    
    <x-nav.link-collapsed-item 
        :href="route('item2')" 
        content="Item 2" />
</x-nav.link-collapsed>
```

---

## 📐 CREAR NUEVA PÁGINA

### Template Base

```blade
@extends('layouts.app')
@section('title', 'Mi Página')

@push('css')
<!-- CSS adicional aquí -->
@endpush

@section('content')
<div class="flex-1 flex flex-col">
    <div class="px-6 md:px-8 py-6 md:py-8">
        
        <!-- 1. Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">
                Título de Página
            </h1>
            <p class="text-gray-600 mt-2">
                Descripción corta
            </p>
        </div>

        <!-- 2. Breadcrumb -->
        <x-breadcrumb.template class="mb-6">
            <x-breadcrumb.item :href="route('panel')" content="Inicio" />
            <x-breadcrumb.item active="true" content="Mi Página" />
        </x-breadcrumb.template>

        <!-- 3. Contenido -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Card Ejemplo -->
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-900">Card Title</h3>
                </div>
                <div class="p-6">
                    Contenido aquí...
                </div>
            </div>
            
        </div>
        
    </div>
</div>
@endsection

@push('js')
<!-- JavaScript aquí -->
@endpush
```

---

## 🎨 COLORES Y TIPOGRAFÍA

### Fondos
```
bg-white        Primario
bg-gray-50      Página
bg-gray-100     Hover/Secundario
bg-blue-50      Accent light
```

### Textos
```
text-gray-900   Títulos
text-gray-700   Contenido principal
text-gray-600   Secundario
text-gray-500   Helper text
```

### Tamaños Tipográficos
```
text-4xl font-bold           Títulos grandes (H1)
text-3xl font-bold           Títulos medios (H2)
text-lg font-semibold        Títulos pequeños (H3)
text-base font-medium        Body principal
text-sm font-medium          Body pequeño
text-xs font-medium          Labels/Helper
```

---

## 📏 SPACING ESTÁNDAR

```html
<!-- Contenedor principal -->
<div class="px-6 md:px-8 py-6 md:py-8">...</div>

<!-- Card interior -->
<div class="p-6">...</div>

<!-- Section gaps -->
<div class="gap-6">...</div>

<!-- Item gaps -->
<div class="gap-3">...</div>

<!-- Margen después de header -->
<div class="mb-8">...</div>
```

---

## 🎯 GRID RESPONSIVE

### Columnas automáticas
```html
<!-- 1 mobile, 2 tablet, 3 desktop -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

<!-- 1 mobile, 2 desktop -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

<!-- 1 mobile, 2 tablet, 4 desktop -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
```

---

## 🔘 BOTONES Y LINKS

### Primary Button
```html
<button class="px-4 py-2 bg-blue-600 text-white rounded-lg
    font-medium hover:bg-blue-700 transition-colors">
    Acción Principal
</button>
```

### Secondary Button
```html
<button class="px-4 py-2 border border-gray-300 text-gray-700
    rounded-lg font-medium hover:bg-gray-50 transition-colors">
    Acción Secundaria
</button>
```

### Link
```html
<a href="#" class="text-blue-600 hover:text-blue-700 
    font-medium transition-colors">
    Link
</a>
```

---

## 📊 CARDS

### Card Simple
```html
<div class="bg-white rounded-lg border border-gray-200 shadow-sm">
    <div class="p-6">
        Contenido...
    </div>
</div>
```

### Card con Header
```html
<div class="bg-white rounded-lg border border-gray-200 shadow-sm">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="font-semibold text-gray-900">Título</h3>
    </div>
    <div class="p-6">
        Contenido...
    </div>
</div>
```

### Card con Footer
```html
<div class="bg-white rounded-lg border border-gray-200 shadow-sm">
    <div class="p-6">Contenido...</div>
    <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
        <a href="#" class="text-blue-600">Acción</a>
    </div>
</div>
```

---

## 🔍 BÚSQUEDA

### Input Search
```html
<div class="relative">
    <i class="fas fa-search absolute left-3 top-1/2 
        transform -translate-y-1/2 text-gray-400"></i>
    <input type="text" 
        placeholder="Buscar..." 
        class="w-full pl-10 pr-4 py-2 bg-gray-100 
            rounded-lg border border-transparent 
            focus:outline-none focus:bg-white 
            focus:border-blue-300 focus:ring-2 
            focus:ring-blue-200 transition-all" />
</div>
```

---

## 🎨 HOVER EFFECTS

### Botones
```html
<button class="... hover:bg-blue-700 transition-colors">
<button class="... hover:bg-gray-50 transition-colors">
```

### Cards
```html
<div class="... hover:shadow-md transition-shadow">
```

### Links
```html
<a class="... hover:text-blue-700 transition-colors">
```

### Navs
```html
<a class="... hover:bg-gray-100 transition-all duration-200">
```

---

## ♿ ACCESIBILIDAD

### ARIA Labels
```html
<button aria-label="Toggle sidebar">...</button>
<button aria-haspopup="true" aria-expanded="false">...</button>
<nav role="navigation">...</nav>
<a role="menuitem" href="#">...</a>
```

### Semantic HTML
```html
<main>Contenido principal</main>
<nav>Navegación</nav>
<aside>Sidebar</aside>
<footer>Footer</footer>
<header>Header</header>
<section>Sección</section>
<article>Artículo</article>
```

---

## 📱 RESPONSIVE BREAKPOINTS

```
Predeterminado   <640px   Mobile
sm:              ≥640px   Small
md:              ≥768px   Tablet
lg:              ≥1024px  Desktop
xl:              ≥1280px  Large Desktop
```

### Ejemplos
```html
<!-- Hidden en mobile, visible en md+ -->
<div class="hidden md:block">...</div>

<!-- Diferente grid según tamaño -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4">

<!-- Padding dinámico -->
<div class="px-4 md:px-6 lg:px-8">

<!-- Diferentes tamaños de tipografía -->
<h1 class="text-2xl md:text-3xl lg:text-4xl">
```

---

## 🚫 QUÉ NO HACER

❌ No usar `bg-gray-900` (oscuro eliminado)  
❌ No usar `text-white` en contenido (solo en especiales)  
❌ No usar clases Bootstrap como `container-fluid`, `row`, `col`  
❌ No crear nuevas tarjetas con gradientes agresivos  
❌ No mezclar Tailwind arbitrario con componentes  
❌ No eliminar border-gray-200 de cards  

---

## ✅ QUÉ HACER

✅ Usar `bg-white` para fondos principales  
✅ Usar `text-gray-900` para títulos  
✅ Usar Tailwind utilities consistentemente  
✅ Mantener gap-6 entre cards  
✅ Usar componentes existentes `x-dashboard-stat-card`  
✅ Mantener paleta de colores: 7 colores estándar  
✅ Usar `rounded-lg` para bordes  
✅ Usar `border border-gray-200` para separadores  

---

## 🐛 DEBUGGING

### Sidebar no aparece
```
Verificar: hidden md:flex en <aside>
```

### Cards no aligneadas
```
Verificar: gap-6 en grid
Verificar: p-6 en contenedor
```

### Tipografía rara
```
Verificar: font-semibold, font-bold
Verificar: text-sm, text-base, text-lg
```

### Colores no coinciden
```
Verificar: bg-blue-600 vs bg-blue-700
Verificar: text-gray-900 vs text-gray-700
```

---

## 📚 REFERENCIAS

- **Documentación completa:** [UX_UI_REFACTORIZACION_FILAMENT.md](UX_UI_REFACTORIZACION_FILAMENT.md)
- **Resumen ejecutivo:** [REFACTORIZACION_UX_RESUMEN.md](REFACTORIZACION_UX_RESUMEN.md)
- **Tailwind CSS:** https://tailwindcss.com/docs
- **Filament:** https://filamentphp.com/

---

**¡Listo para empezar! 🚀**
