<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\categoriaController;
use App\Http\Controllers\clienteController;
use App\Http\Controllers\compraController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\ExportExcelController;
use App\Http\Controllers\ExportPDFController;
use App\Http\Controllers\homeController;
use App\Http\Controllers\ImportExcelController;
use App\Http\Controllers\InventarioControlller;
use App\Http\Controllers\KardexController;
use App\Http\Controllers\loginController;
use App\Http\Controllers\logoutController;
use App\Http\Controllers\marcaController;
use App\Http\Controllers\MovimientoController;
use App\Http\Controllers\presentacioneController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\profileController;
use App\Http\Controllers\proveedorController;
use App\Http\Controllers\roleController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\SuperAdmin\DashboardController;
use App\Http\Controllers\SuperAdmin\EmpresasController as SuperAdminEmpresasController;
use App\Http\Controllers\userController;
use App\Http\Controllers\ventaController;
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

// Landing page (sin autenticación requerida)
Route::get('/', [homeController::class, 'index'])->name('panel');
Route::get('/landing', [homeController::class, 'index'])->name('landing');

// Registro de empresa
Route::get('/register', [RegisterController::class, 'create'])->name('register.create');
Route::post('/register', [RegisterController::class, 'store'])->name('register.store');

// Login
Route::get('/login', [loginController::class, 'index'])->name('login.index');
Route::post('/login', [loginController::class, 'login'])->name('login.login');

// Stripe Webhook - No authentication required (third-party service)
Route::post('/webhooks/stripe', StripeWebhookController::class)->name('webhooks.stripe');

// ==================== Super Admin Routes ====================

Route::middleware(['auth', 'check-super-admin'])->prefix('admin/super')->name('super-admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/empresas', [SuperAdminEmpresasController::class, 'index'])->name('empresas.index');
    Route::get('/empresas/{empresa}', [SuperAdminEmpresasController::class, 'show'])->name('empresas.show');
    Route::post('/empresas/{empresa}/suspender', [SuperAdminEmpresasController::class, 'suspend'])->name('empresas.suspend');
    Route::post('/empresas/{empresa}/activar', [SuperAdminEmpresasController::class, 'activate'])->name('empresas.activate');
});

// ==================== Main Admin Panel Routes ====================

Route::group(['middleware' => ['auth', 'check-subscription-active'], 'prefix' => 'admin'], function () {
    Route::resource('categorias', categoriaController::class)->except('show');
    Route::resource('presentaciones', presentacioneController::class)->except('show');
    Route::resource('marcas', marcaController::class)->except('show');
    Route::resource('productos', ProductoController::class)->except('show', 'destroy');
    Route::resource('clientes', clienteController::class)->except('show');
    Route::resource('proveedores', proveedorController::class)->except('show');
    Route::resource('compras', compraController::class)->except('edit', 'update', 'destroy');
    Route::resource('ventas', ventaController::class)->except('edit', 'update', 'destroy');

    // Stripe payment routes
    Route::get('/ventas/{venta}/pago/config', [ventaController::class, 'configPago'])->name('ventas.pago.config');
    Route::post('/ventas/{venta}/pago/iniciar', [ventaController::class, 'iniciarPago'])->name('ventas.pago.iniciar');
    Route::get('/ventas/{venta}/pago/estado', [ventaController::class, 'estadoPago'])->name('ventas.pago.estado');

    Route::resource('users', userController::class)->except('show');
    Route::resource('roles', roleController::class)->except('show');
    Route::resource('profile', profileController::class)->only('index', 'update');
    Route::resource('activityLog', ActivityLogController::class)->only('index');
    Route::resource('inventario', InventarioControlller::class)->only('index', 'create', 'store');
    Route::resource('kardex', KardexController::class)->only('index');
    Route::resource('empresa', EmpresaController::class)->only('index', 'update');
    Route::resource('empleados', EmpleadoController::class)->except('show');
    Route::resource('cajas', CajaController::class)->except('edit', 'update');
    Route::get('cajas/{caja}/close-form', [CajaController::class, 'showCloseForm'])->name('cajas.closeForm');
    Route::post('cajas/{caja}/close', [CajaController::class, 'close'])->name('cajas.close');

    Route::resource('movimientos', MovimientoController::class)->except('edit', 'update');

    // Reportes
    Route::get('/export-pdf-comprobante-venta/{id}', [ExportPDFController::class, 'exportPdfComprobanteVenta'])
        ->name('export.pdf-comprobante-venta');

    Route::get('/export-excel-vental-all', [ExportExcelController::class, 'exportExcelVentasAll'])
        ->name('export.excel-ventas-all');

    Route::post('/importar-excel-empleados', [ImportExcelController::class, 'importExcelEmpleados'])
        ->name('import.excel-empleados');

    Route::post('/notifications/mark-as-read', function () {
        Auth::user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    })->name('notifications.markAsRead');

    Route::get('/logout', [logoutController::class, 'logout'])->name('logout');
});

