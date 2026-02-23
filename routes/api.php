<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ==================== CINEMA PUBLIC API ====================
// Endpoints públicos para integración con sitios web externos
// No requieren autenticación

use App\Http\Controllers\Api\CinemaPublicController;
use App\Http\Controllers\Api\CinemaAdminController;

Route::prefix('cinema')->group(function () {

    // Cartelera y películas
    Route::get('/cartelera', [CinemaPublicController::class, 'getCartelera']);
    Route::get('/peliculas/{id}/funciones', [CinemaPublicController::class, 'getFuncionesPelicula']);

    // Asientos y disponibilidad
    Route::get('/funciones/{id}/asientos', [CinemaPublicController::class, 'getAsientosFuncion']);

    // Precios
    Route::get('/precios', [CinemaPublicController::class, 'getPrecios']);

    // Reservas y compras (requieren session)
    Route::post('/reservar', [CinemaPublicController::class, 'reservarAsientos']);
    Route::post('/confirmar-compra', [CinemaPublicController::class, 'confirmarCompra']);
});

// ==================== CINEMA ADMIN API ====================
// Endpoints administrativos (requieren autenticación)

Route::prefix('admin')->middleware('auth:sanctum')->group(function () {

    // Películas
    Route::get('/peliculas', [CinemaAdminController::class, 'getPeliculas']);
    Route::post('/peliculas', [CinemaAdminController::class, 'storePelicula']);
    Route::put('/peliculas/{id}', [CinemaAdminController::class, 'updatePelicula']);

    // Funciones
    Route::get('/funciones', [CinemaAdminController::class, 'getFunciones']);
    Route::post('/funciones', [CinemaAdminController::class, 'storeFuncion']);
    Route::put('/funciones/{id}', [CinemaAdminController::class, 'updateFuncion']);
    Route::delete('/funciones/{id}', [CinemaAdminController::class, 'deleteFuncion']);

    // Distribuidores
    Route::get('/distribuidores', [CinemaAdminController::class, 'getDistribuidores']);

    // Precios
    Route::get('/precios', [CinemaAdminController::class, 'getPrecios']);
    Route::put('/precios/{id}', [CinemaAdminController::class, 'updatePrecio']);

    // Reportes
    Route::get('/reportes/ventas', [CinemaAdminController::class, 'getReporteVentas']);

    // ==================== INVENTORY & PROFITABILITY API ====================
    Route::prefix('inventory')->group(function () {
        Route::get('/valuation', [\App\Http\Controllers\Api\InventoryApiController::class, 'assetValuation']);
        Route::get('/stock', [\App\Http\Controllers\Api\InventoryApiController::class, 'stockLevels']);
        Route::get('/profitability/{producto}', [\App\Http\Controllers\Api\InventoryApiController::class, 'productProfitability']);
    });
});
