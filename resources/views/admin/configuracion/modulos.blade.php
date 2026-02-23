@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row">
        <div class="col-12">
            <h1 class="mt-4 mb-4">⚙️ Configuración de Módulos</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Panel</a></li>
                    <li class="breadcrumb-item active">Módulos</li>
                </ol>
            </nav>
        </div>
    </div>
    
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0"><i class="fas fa-cubes me-2"></i> Gestión de Módulos y Tipo de Negocio</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.modulos.update') }}">
                @csrf
                
                <div class="mb-4">
                    <label class="form-label fw-bold">Tipo de Negocio</label>
                    <select name="business_type" class="form-select">
                        <option value="cinema" {{ $config->business_type === 'cinema' ? 'selected' : '' }}>🎬 Cine</option>
                        <option value="restaurant" {{ $config->business_type === 'restaurant' ? 'selected' : '' }}>🍽️ Restaurante</option>
                        <option value="bakery" {{ $config->business_type === 'bakery' ? 'selected' : '' }}>🥖 Panadería</option>
                        <option value="bar" {{ $config->business_type === 'bar' ? 'selected' : '' }}>🍺 Bar</option>
                        <option value="retail" {{ $config->business_type === 'retail' ? 'selected' : '' }}>🛒 Retail</option>
                        <option value="generic" {{ $config->business_type === 'generic' ? 'selected' : '' }}>⚙️ Genérico</option>
                    </select>
                    <div class="form-text text-muted">Esto ajusta la lógica base del sistema para tu rubro.</div>
                </div>
                
                <hr>
                
                <div class="mb-4">
                    <h5 class="mb-4 fw-bold">Módulos Activos</h5>
                    
                    <div class="list-group">
                        <label class="list-group-item list-group-item-action d-flex align-items-center py-3">
                            <div class="flex-shrink-0">
                                <input type="checkbox" name="module_cinema" class="form-check-input md-check ms-0" 
                                       {{ $config->isModuleEnabled('cinema') ? 'checked' : '' }}>
                            </div>
                            <div class="ms-3">
                                <div class="fw-bold">🎬 Módulo de Cinema</div>
                                <small class="text-muted">Gestión de películas, funciones, salas y mapas de asientos.</small>
                            </div>
                        </label>
                        
                        <label class="list-group-item list-group-item-action d-flex align-items-center py-3">
                            <div class="flex-shrink-0">
                                <input type="checkbox" name="module_pos" class="form-check-input md-check ms-0"
                                       {{ $config->isModuleEnabled('pos') ? 'checked' : '' }}>
                            </div>
                            <div class="ms-3">
                                <div class="fw-bold">💰 Módulo POS</div>
                                <small class="text-muted">Punto de venta rápido, carrito unificado y pagos.</small>
                            </div>
                        </label>
                        
                        <label class="list-group-item list-group-item-action d-flex align-items-center py-3">
                            <div class="flex-shrink-0">
                                <input type="checkbox" name="module_inventory" class="form-check-input md-check ms-0"
                                       {{ $config->isModuleEnabled('inventory') ? 'checked' : '' }}>
                            </div>
                            <div class="ms-3">
                                <div class="fw-bold">📦 Módulo de Inventario</div>
                                <small class="text-muted">Control de stock, recetas, insumos, auditorías y kardex.</small>
                            </div>
                        </label>
                        
                        <label class="list-group-item list-group-item-action d-flex align-items-center py-3">
                            <div class="flex-shrink-0">
                                <input type="checkbox" name="module_reports" class="form-check-input md-check ms-0"
                                       {{ $config->isModuleEnabled('reports') ? 'checked' : '' }}>
                            </div>
                            <div class="ms-3">
                                <div class="fw-bold">📊 Módulo de Reportes</div>
                                <small class="text-muted">Dashboard avanzado, métricas de rentabilidad y reportes de ventas.</small>
                            </div>
                        </label>
                        
                        <label class="list-group-item list-group-item-action d-flex align-items-center py-3">
                            <div class="flex-shrink-0">
                                <input type="checkbox" name="module_api" class="form-check-input md-check ms-0"
                                       {{ $config->isModuleEnabled('api') ? 'checked' : '' }}>
                            </div>
                            <div class="ms-3">
                                <div class="fw-bold">🔌 API Externa</div>
                                <small class="text-muted">Habilita endpoints para integraciones con apps móviles o sitios web.</small>
                            </div>
                        </label>
                    </div>
                </div>
                
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary btn-lg px-5">
                        <i class="fas fa-save me-2"></i> Guardar Configuración
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<style>
    .md-check {
        width: 1.5rem;
        height: 1.5rem;
    }
    .list-group-item-action {
        cursor: pointer;
        transition: background-color 0.2s;
    }
    .list-group-item-action:hover {
        background-color: #f8f9fa;
    }
</style>
@section('js')
<script>
    document.querySelector('select[name="business_type"]').addEventListener('change', function() {
        const type = this.value;
        const presets = {
            'cinema': { 'cinema': true, 'pos': true, 'inventory': true, 'reports': true, 'api': false },
            'restaurant': { 'cinema': false, 'pos': true, 'inventory': true, 'reports': true, 'api': false },
            'bakery': { 'cinema': false, 'pos': true, 'inventory': true, 'reports': true, 'api': false },
            'bar': { 'cinema': false, 'pos': true, 'inventory': true, 'reports': true, 'api': false },
            'retail': { 'cinema': false, 'pos': true, 'inventory': true, 'reports': true, 'api': false },
            'generic': { 'cinema': true, 'pos': true, 'inventory': true, 'reports': true, 'api': false }
        };
        
        if (presets[type]) {
            const config = presets[type];
            Object.keys(config).forEach(module => {
                const checkbox = document.querySelector(`input[name="module_${module}"]`);
                if (checkbox) {
                    checkbox.checked = config[module];
                }
            });
            
            // Highlight changes
            const btn = document.querySelector('button[type="submit"]');
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-warning');
            btn.innerHTML = '<i class="fas fa-magic me-2"></i> Aplicar Preset de ' + type.charAt(0).toUpperCase() + type.slice(1);
        }
    });
</script>
@endsection
@endsection
