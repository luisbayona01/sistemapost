<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\CinemaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\ExportExcelController;
use App\Http\Controllers\ExportPDFController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\MovimientoController;
use App\Http\Controllers\PresentacioneController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProveedoreController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\SuperAdmin\DashboardController;
use App\Http\Controllers\SuperAdmin\EmpresasController as SuperAdminEmpresasController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\POS\CashierController;
use App\Http\Controllers\DevolucionController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\ExecutiveController;
use App\Http\Controllers\Reports\FiscalReportController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// ==================== Landing & Authentication ====================

// Landing page (new controller)
Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/landing', [LandingController::class, 'index']); // Safety alias

// Registro de empresa
Route::get('/register', [RegisterController::class, 'create'])->name('register.create');
Route::post('/register', [RegisterController::class, 'store'])->name('register.store');

// Login
Route::get('/login', [LoginController::class, 'index'])->name('login.index');
Route::post('/login', [LoginController::class, 'login'])->name('login.login');

// Logout (POST enforced)
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

// Stripe Webhook - No authentication required (third-party service)
Route::post('/webhooks/stripe', StripeWebhookController::class)->name('webhooks.stripe');

// Cartelera Pública (Problema #5)
Route::get('/cartelera', [App\Http\Controllers\CarteleraController::class, 'index'])->name('cartelera.index');

// ==================== Protected Routes ====================

// Base Auth Group
Route::middleware(['auth'])->group(function () {

    // Redirect root home to admin dashboard
    Route::get('/home', function () {
        return redirect()->route('admin.dashboard.index');
    });

    // ==================== POS / CLIENT SIDE (Authenticated) ====================
    // These routes are NOT prefixed with /admin to simulate a separate app

    // Cinema POS
    Route::middleware(['module:cinema'])->group(function () {
        Route::get('/cinema/funciones', [CinemaController::class, 'index'])->name('cinema.funciones.index');
        Route::get('/cinema/funciones/{funcion}/asientos', [CinemaController::class, 'showSeatMap'])->name('cinema.seat-map');
        Route::post('/cinema/asientos/reservar', [CinemaController::class, 'reservarAsiento'])->name('cinema.reservar');
        Route::post('/cinema/asientos/vender', [CinemaController::class, 'venderAsiento'])->name('cinema.vender');
        Route::get('/cinema/tickets/{venta}', [CinemaController::class, 'exportarTicket'])->name('cinema.ticket.pdf');
    });


    // ==================== NEW CASHIER POS ====================
    // CRÍTICO: Requiere caja abierta para operar
    Route::middleware(['role:cajero|Gerente|Root|administrador', 'module:pos', 'caja.abierta'])->prefix('pos')->name('pos.')->group(function () {
        Route::get('/', [\App\Http\Controllers\POS\CashierController::class, 'index'])->name('index');
        Route::post('/seat-lock', [\App\Http\Controllers\POS\CashierController::class, 'lockSeatTemporal'])->name('seat-lock');
        Route::post('/agregar-boleto', [\App\Http\Controllers\POS\CashierController::class, 'agregarBoleto'])->name('agregar.boleto');
        Route::post('/agregar-producto', [\App\Http\Controllers\POS\CashierController::class, 'agregarProducto'])->name('agregar.producto');
        Route::post('/agregar-carrito-completo', [\App\Http\Controllers\POS\CashierController::class, 'agregarCarritoCompleto'])->name('agregar.carrito-completo');
        Route::post('/finalizar-venta', [\App\Http\Controllers\POS\CashierController::class, 'finalizarVenta'])->name('finalizar');
        Route::post('/vaciar-carrito', [\App\Http\Controllers\POS\CashierController::class, 'vaciarCarrito'])->name('vaciar');
        Route::delete('/quitar-boleto/{index}', [\App\Http\Controllers\POS\CashierController::class, 'quitarBoleto'])->name('quitar.boleto');
        Route::delete('/quitar-producto/{index}', [\App\Http\Controllers\POS\CashierController::class, 'quitarProducto'])->name('quitar.producto');
        Route::get('/carrito-partial', [\App\Http\Controllers\POS\CashierController::class, 'getCarritoPartial'])->name('carrito.partial');
    });

    // Operaciones Administrativas del POS (Sin requerir caja abierta)
    Route::middleware(['role:Gerente|Root|administrador', 'module:pos'])->prefix('pos')->name('pos.')->group(function () {
        Route::post('/cerrar-dia', [\App\Http\Controllers\OperationalDayController::class, 'close'])->name('cerrar-dia');
    });

    // Panel Ejecutivo (Vista Móvil)
    Route::middleware(['auth', 'role:Root|Gerente|administrador'])->group(function () {
        Route::get('/pos/ejecutivo', [ExecutiveController::class, 'dashboard'])->name('executive.dashboard');
    });

    // Notifications
    Route::post('/notifications/mark-as-read', function () {
        Auth::user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    })->name('notifications.markAsRead');


    // ==================== ADMIN PANEL (Backoffice) ====================
    Route::group(['middleware' => ['check-subscription-active', 'role:administrador|cajero|Root|Gerente'], 'prefix' => 'admin'], function () {

        // Redirect /admin to /admin/dashboard
        Route::get('/', function () {
            return redirect()->route('admin.dashboard.index');
        });

        // Main Dashboard
        Route::name('admin.')->group(function () {
            Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])
                ->name('dashboard.index');

            Route::get('/dashboard/top-performers', [App\Http\Controllers\Admin\DashboardController::class, 'topPerformers'])
                ->name('dashboard.top-performers');

            Route::get('/dashboard/ocupacion', [App\Http\Controllers\Admin\DashboardController::class, 'ocupacion'])
                ->name('dashboard.ocupacion');

            Route::get('/dashboard/confiteria', [App\Http\Controllers\Admin\DashboardController::class, 'confiteria'])
                ->name('dashboard.confiteria');
        });

        // Devoluciones (Fase 3.9 - Blindaje)
        // Módulo de Devoluciones (Orden #5)
        Route::name('admin.')->group(function () {
            Route::get('/devoluciones', [App\Http\Controllers\Admin\DevolucionController::class, 'index'])
                ->name('devoluciones.index');
            Route::post('/devoluciones/buscar', [App\Http\Controllers\Admin\DevolucionController::class, 'buscarVenta'])
                ->name('devoluciones.buscar');
            Route::post('/devoluciones/procesar', [App\Http\Controllers\Admin\DevolucionController::class, 'procesar'])
                ->name('devoluciones.procesar');
        });
        Route::get('/ventas/buscar', [App\Http\Controllers\ventaController::class, 'buscarPorComprobante'])->name('ventas.buscar');


        // Módulos Management (Solo Root)
        Route::middleware(['role:Root'])->name('admin.')->group(function () {
            Route::get('/modulos', [\App\Http\Controllers\Admin\ModulosController::class, 'index'])->name('modulos.index');
            Route::post('/modulos', [\App\Http\Controllers\Admin\ModulosController::class, 'update'])->name('modulos.update');
        });

        // Resources
        Route::resource('categorias', CategoriaController::class)->except('show');
        Route::resource('presentaciones', PresentacioneController::class)->except('show');
        Route::resource('marcas', MarcaController::class)->except('show');
        Route::resource('productos', ProductoController::class)->except('show', 'destroy');
        Route::post('productos/{producto}/toggle-status', [ProductoController::class, 'toggleStatus'])->name('productos.toggleStatus');
        Route::resource('clientes', ClienteController::class)->except('show');
        Route::resource('proveedores', ProveedoreController::class)->except('show');
        Route::resource('compras', CompraController::class)->except('edit', 'update', 'destroy');
        Route::resource('ventas', VentaController::class)->except('edit', 'update');

        // Stripe payment routes
        Route::get('/ventas/{venta}/pago/config', [VentaController::class, 'configPago'])->name('ventas.pago.config');
        Route::post('/ventas/{venta}/pago/iniciar', [VentaController::class, 'iniciarPago'])->name('ventas.pago.iniciar');
        Route::get('/ventas/{venta}/pago/estado', [VentaController::class, 'estadoPago'])->name('ventas.pago.estado');

        // Administration
        Route::resource('users', UserController::class)->except('show');
        Route::resource('roles', RoleController::class)->except('show');
        Route::resource('profile', ProfileController::class)->only('index', 'update');
        Route::resource('inventario', App\Http\Controllers\InventarioController::class)->only('index', 'create', 'store');
        Route::post('inventario/ajuste-rapido', [App\Http\Controllers\InventarioController::class, 'ajusteRapido'])->name('admin.inventario.ajuste-rapido');
        // FIX 4: Carga masiva de inventario desde CSV
        Route::get('inventario/carga-masiva', [App\Http\Controllers\InventarioCargaMasivaController::class, 'index'])
            ->name('inventario.carga-masiva.index');
        Route::get('inventario/carga-masiva/plantilla', [App\Http\Controllers\InventarioCargaMasivaController::class, 'descargarPlantilla'])
            ->name('inventario.carga-masiva.plantilla');
        Route::post('inventario/carga-masiva/procesar', [App\Http\Controllers\InventarioCargaMasivaController::class, 'procesar'])
            ->name('inventario.carga-masiva.procesar');
        Route::resource('kardex', App\Http\Controllers\KardexController::class)->only('index');
        Route::resource('insumos', \App\Http\Controllers\InsumoController::class);

        // Empresa & Empleados
        Route::resource('empresa', EmpresaController::class)->only('index', 'update');
        Route::resource('empleados', EmpleadoController::class)->except('show');

        // Gestión de Cajas & Finanzas (FASE 4 - Consolidada)
        Route::name('admin.')->group(function () {
            Route::resource('cajas', App\Http\Controllers\Admin\CajaController::class);
            Route::post('cajas/abrir', [App\Http\Controllers\Admin\CajaController::class, 'abrirCaja'])->name('cajas.abrir');
            Route::get('cajas/{id}/cierre', [App\Http\Controllers\Admin\CajaController::class, 'mostrarCierre'])->name('cajas.mostrar-cierre');
            Route::get('cajas/{id}/cierre-wizard', [App\Http\Controllers\Admin\CajaController::class, 'mostrarCierreWizard'])->name('cajas.mostrar-cierre-wizard');
            Route::post('cajas/{id}/reabrir', [App\Http\Controllers\Admin\CajaController::class, 'reabrirCierre'])->name('cajas.reabrir-cierre');
            Route::post('cajas/{id}/cerrar', [App\Http\Controllers\Admin\CajaController::class, 'cerrar'])->name('cajas.cerrar');
            Route::get('cajas/{id}/reporte', [App\Http\Controllers\Admin\CajaController::class, 'reporteCierre'])->name('cajas.reporte-cierre');
            Route::get('cajas/{id}/descargar-pdf', [App\Http\Controllers\Admin\CajaController::class, 'descargarPDF'])->name('cajas.descargar-pdf');
            Route::get('cajas/{id}/descargar-excel', [App\Http\Controllers\Admin\CajaController::class, 'descargarExcel'])->name('cajas.descargar-excel');
            Route::get('cierre-dia', [App\Http\Controllers\Admin\CajaController::class, 'cierreDia'])->name('cajas.cierre-dia');

            // NUEVAS RUTAS SINGULARIZADAS (Fase 4.1)
            Route::get('/cajas-admin', [App\Http\Controllers\Admin\CajaController::class, 'index'])->name('caja.index');
            Route::get('/caja/{id}/cerrar-simple', [App\Http\Controllers\Admin\CajaController::class, 'cerrarSimple'])->name('caja.cerrar-simple');
            Route::post('/caja/{id}/procesar-cierre', [App\Http\Controllers\Admin\CajaController::class, 'procesarCierre'])->name('caja.procesar-cierre');
            Route::get('/caja/{id}/reporte', [App\Http\Controllers\Admin\CajaController::class, 'reporteCierre'])->name('caja.reporte-cierre');
            Route::get('/caja/{id}/descargar-pdf', [App\Http\Controllers\Admin\CajaController::class, 'descargarPDF'])->name('caja.descargar-pdf');
            Route::get('/caja/{id}/descargar-excel', [App\Http\Controllers\Admin\CajaController::class, 'descargarExcel'])->name('caja.descargar-excel');
        });

        // Reportes Específicos
        Route::group(['prefix' => 'reportes', 'name' => 'admin.reportes.'], function () {
            Route::get('/consolidado', [\App\Http\Controllers\Reports\ConsolidatedReportController::class, 'index'])->name('consolidado');
            Route::get('/cinema', [App\Http\Controllers\Admin\DashboardController::class, 'ocupacion'])->name('cinema');
            Route::get('/confiteria', [App\Http\Controllers\Admin\DashboardController::class, 'confiteria'])->name('confiteria');
        });

        // Movimientos
        Route::resource('movimientos', MovimientoController::class)->except('edit', 'update');

        // Cinema Admin (Solo si módulo cinema está activo)
        Route::middleware(['module:cinema'])->group(function () {
            Route::resource('distribuidores', \App\Http\Controllers\DistribuidorController::class)->except('show');
            Route::resource('peliculas', \App\Http\Controllers\PeliculaController::class);

            Route::get('funciones/bulk', [\App\Http\Controllers\FuncionController::class, 'bulkCreate'])->name('funciones.bulkCreate');
            Route::post('funciones/bulk', [\App\Http\Controllers\FuncionController::class, 'bulkStore'])->name('funciones.bulkStore');
            Route::resource('funciones', \App\Http\Controllers\FuncionController::class);

            Route::post('funciones/{funcion}/toggle-activo', [\App\Http\Controllers\FuncionController::class, 'toggleActivo'])->name('funciones.toggleActivo');
        });

        // Reportes Consolidados e Individuales
        Route::middleware(['role:Root|Gerente|administrador|cajero'])->name('admin.')->group(function () {
            // Reporte Consolidado (Siempre disponible o según módulo reports?)
            Route::get('/reportes/consolidado', [\App\Http\Controllers\Reports\ConsolidatedReportController::class, 'index'])
                ->name('reportes.consolidado');

            // Los reportes individuales adaptados a la nueva estructura
            Route::middleware(['module:cinema'])->get('/reportes/cinema', [\App\Http\Controllers\Reports\CinemaReportController::class, 'index'])
                ->name('reportes.cinema');

            Route::middleware(['module:pos'])->get('/reportes/confiteria', [\App\Http\Controllers\Reports\ConcessionsReportController::class, 'index'])
                ->name('reportes.confiteria');

            Route::get('/reportes/ocupacion-cinema', [\App\Http\Controllers\Reports\CinemaOccupancyController::class, 'index'])
                ->name('reportes.ocupacion');

            Route::get('/reportes/peliculas', [\App\Http\Controllers\Reports\ReportePeliculasController::class, 'index'])
                ->name('reportes.peliculas');

            Route::get('/reportes/peliculas/export', [\App\Http\Controllers\Reports\ReportePeliculasController::class, 'export'])
                ->name('reportes.peliculas.export');

            // REQUERIMIENTO ESPECIAL: Ruta solicitada por usuario (mapeada a controlador estándar)
            Route::get('/reportes/diario', \App\Http\Controllers\Reports\DailyReportController::class)
                ->name('ventas.diarias');
        });


        // ==================== Advanced Inventory & Profitability ====================
        Route::group(['middleware' => ['module:inventory'], 'prefix' => 'inventario-avanzado', 'as' => 'inventario-avanzado.'], function () {
            Route::get('/', [\App\Http\Controllers\Inventory\InventoryDashboardController::class, 'index'])->name('index');

            // Carga Masiva (Excel)
            Route::get('/importar', [\App\Http\Controllers\Inventory\InventoryImportController::class, 'show'])->name('import.show');
            Route::get('/importar/plantilla', [\App\Http\Controllers\Inventory\InventoryImportController::class, 'downloadTemplate'])->name('import.template');
            Route::get('/exportar/actual', [\App\Http\Controllers\Inventory\InventoryImportController::class, 'exportCurrent'])->name('export.current');
            Route::post('/importar', [\App\Http\Controllers\Inventory\InventoryImportController::class, 'import'])->name('import.store');

            Route::get('/almacen', [\App\Http\Controllers\Inventory\InventoryController::class, 'almacen'])->name('almacen');
            Route::get('/cocina', [\App\Http\Controllers\Inventory\InventoryController::class, 'cocina'])->name('cocina');
            Route::post('/cocina/{producto}/update-precio', [\App\Http\Controllers\Inventory\InventoryController::class, 'updatePrecio'])->name('cocina.update-precio');
            Route::get('/auditoria', [\App\Http\Controllers\Inventory\InventoryController::class, 'auditoria'])->name('auditoria');

            // Insumos & Lotes
            Route::resource('insumos', \App\Http\Controllers\Inventory\InsumoController::class);
            Route::post('insumos/{insumo}/lote', [\App\Http\Controllers\Inventory\InsumoController::class, 'storeLote'])->name('insumos.lote.store');

            // Recetas
            Route::post('recetas', [\App\Http\Controllers\Inventory\RecipeController::class, 'store'])->name('recetas.store');
            Route::delete('recetas/{receta}', [\App\Http\Controllers\Inventory\RecipeController::class, 'destroy'])->name('recetas.destroy');

            // Auditorias
            Route::post('auditorias', [\App\Http\Controllers\Inventory\AuditController::class, 'store'])->name('auditorias.store');
            Route::post('auditorias/{auditoria}/finalizar', [\App\Http\Controllers\Inventory\AuditController::class, 'finalize'])->name('auditorias.finalize');

            // Bajas y Cortesías
            Route::get('baja/create', [\App\Http\Controllers\Inventory\InsumoSalidaController::class, 'create'])->name('baja.create');
            Route::post('baja', [\App\Http\Controllers\Inventory\InsumoSalidaController::class, 'store'])->name('baja.store');
            Route::get('baja/{id}/ticket', [\App\Http\Controllers\Inventory\InsumoSalidaController::class, 'ticket'])->name('baja.ticket');

            // Ajustes Formales (Fase 3)
            Route::get('ajustes/create', [\App\Http\Controllers\Inventory\AjusteController::class, 'create'])->name('ajustes.create');
            Route::post('ajustes', [\App\Http\Controllers\Inventory\AjusteController::class, 'store'])->name('ajustes.store');

            // Gastos Operacionales (Fase 3)
            Route::get('gastos', [\App\Http\Controllers\Inventory\GastoOperacionalController::class, 'index'])->name('gastos.index');
            Route::post('gastos', [\App\Http\Controllers\Inventory\GastoOperacionalController::class, 'store'])->name('gastos.store');
            Route::delete('gastos/{gasto}', [\App\Http\Controllers\Inventory\GastoOperacionalController::class, 'destroy'])->name('gastos.destroy');

            // Reportes Operativos
            Route::get('/reportes/valorizado', [\App\Http\Controllers\Inventory\OperationalReportController::class, 'valorizado'])->name('reports.inventory-value');
            Route::get('/reportes/ventas', [\App\Http\Controllers\Inventory\OperationalReportController::class, 'ventas'])->name('reports.sales');
            Route::get('/reportes/marginalidad', [\App\Http\Controllers\Inventory\OperationalReportController::class, 'marginalidad'])->name('reports.profitability');

        });

        // Reportes Fiscales (Fase 4.2) - Movidos fuera de inventario-avanzado para coincidir con el nombre de ruta esperado
        Route::get('/reportes/fiscal/inc', [FiscalReportController::class, 'bimestralINC'])->name('reports.fiscal.inc');
        Route::get('/reportes/fiscal/inc/export', [FiscalReportController::class, 'exportINC'])->name('reports.fiscal.inc.export');





        // Reportes & Exports
        Route::get('/export-pdf-comprobante-venta', [ExportPDFController::class, 'exportPdfComprobanteVenta'])
            ->name('export.pdf-comprobante-venta');

        Route::get('/export-pdf-documento-fiscal', [ExportPDFController::class, 'exportPdfDocumentoFiscal'])
            ->name('export.pdf-documento-fiscal');

        Route::get('/export-excel-vental-all', [ExportExcelController::class, 'exportExcelVentasAll'])
            ->name('export.excel-ventas-all');

        Route::post('/importar-excel-empleados', [App\Http\Controllers\ImportExcelController::class, 'importExcelEmpleados'])
            ->name('import.excel-empleados');

        // Logout internal alias
        Route::post('/logout-internal', [logoutController::class, 'logout'])->name('internal.logout');
    });

    // ==================== ROOT CONSOLE (Super User Only) ====================
    Route::group(['middleware' => ['auth', 'role:Root'], 'prefix' => 'admin/root', 'as' => 'root.'], function () {
        Route::resource('activity-log', ActivityLogController::class)->only('index');

        // Auto-Backup Trigger
        Route::get('/backup/run', function () {
            // Simulator of Backup logic
            try {
                // \Artisan::call('backup:run'); // Requires spatie/laravel-backup or similar
                // For now, we log the attempt
                \App\Services\ActivityLogService::log('Backup Manual Solicitado', 'System', ['initiator' => auth()->user()->id]);
                return redirect()->back()->with('success', 'Backup del sistema iniciado en segundo plano.');
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Error al iniciar backup: ' . $e->getMessage());
            }
        })->name('backup.run');
    });

    // ==========================================
    // MÓDULO 2: REPORTES INTELIGENTES (Gerente/Root)
    // ==========================================
    Route::middleware(['auth', 'role:Root|Gerente'])->prefix('admin')->name('admin.')->group(function () {



        // ---------------------------------------------------------------------
        // MÓDULO 3: ALERTAS DE INTELIGENCIA OPERATIVA
        // ---------------------------------------------------------------------
        Route::get('/alertas', [App\Http\Controllers\Admin\AlertasController::class, 'index'])
            ->name('alertas.index');

        Route::post('/alertas/{alerta}/resolver', [App\Http\Controllers\Admin\AlertasController::class, 'resolver'])
            ->name('alertas.resolver');

        Route::post('/alertas/marcar-vistas', [App\Http\Controllers\Admin\AlertasController::class, 'marcarTodasVistas'])
            ->name('alertas.marcar-vistas');

        Route::get('/alertas/count', [App\Http\Controllers\Admin\AlertasController::class, 'count'])
            ->name('alertas.count');

        // ---------------------------------------------------------------------
        // MÓDULO 4: VISTA EJECUTIVA MÓVIL
        // ---------------------------------------------------------------------
        Route::get('/mobile', [App\Http\Controllers\Admin\MobileController::class, 'index'])
            ->name('mobile.index');

        // ---------------------------------------------------------------------
        // MÓDULO 5: SISTEMA DE TARIFAS
        // ---------------------------------------------------------------------
        Route::get('/tarifas', [App\Http\Controllers\Admin\TarifaController::class, 'index'])
            ->name('tarifas.index');
        Route::get('/tarifas/crear', [App\Http\Controllers\Admin\TarifaController::class, 'crear'])
            ->name('tarifas.crear');
        Route::post('/tarifas/guardar', [App\Http\Controllers\Admin\TarifaController::class, 'guardar'])
            ->name('tarifas.guardar');
        Route::get('/tarifas/para-fecha', [App\Http\Controllers\Admin\TarifaController::class, 'obtenerParaFecha'])
            ->name('tarifas.para-fecha');
        Route::get('/tarifas/{id}/editar', [App\Http\Controllers\Admin\TarifaController::class, 'editar'])
            ->name('tarifas.editar');
        Route::put('/tarifas/{id}', [App\Http\Controllers\Admin\TarifaController::class, 'actualizar'])
            ->name('tarifas.actualizar');

        // ---------------------------------------------------------------------
        // MÓDULO 6: FACTURAS DE COMPRA
        // ---------------------------------------------------------------------
        Route::get('/facturas', [App\Http\Controllers\Admin\FacturaCompraController::class, 'index'])
            ->name('facturas.index');
        Route::get('/facturas/crear', [App\Http\Controllers\Admin\FacturaCompraController::class, 'crear'])
            ->name('facturas.crear');
        Route::post('/facturas/guardar', [App\Http\Controllers\Admin\FacturaCompraController::class, 'guardar'])
            ->name('facturas.guardar');
        Route::get('/facturas/{id}/editar', [App\Http\Controllers\Admin\FacturaCompraController::class, 'editar'])
            ->name('facturas.editar');
        Route::put('/facturas/{id}', [App\Http\Controllers\Admin\FacturaCompraController::class, 'actualizar'])
            ->name('facturas.actualizar');

        // Nuevas rutas para Reportes y Quick Store
        Route::get('/facturas-reporte-mensual', [App\Http\Controllers\Admin\FacturaCompraController::class, 'reporteMensual'])
            ->name('facturas.reporte-mensual');
        Route::post('/proveedores-quick-store', [ProveedoreController::class, 'quickStore'])
            ->name('proveedores.quick-store');
    });
});

