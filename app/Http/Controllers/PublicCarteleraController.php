<?php

namespace App\Http\Controllers;

use App\Models\Funcion;
use Illuminate\Http\Request;

class PublicCarteleraController extends Controller
{
    /**
     * Mostrar la cartelera pública del tenant actual.
     */
    public function index()
    {
        $tenant = app()->bound('currentTenant') ? app('currentTenant') : null;

        // Si no hay tenant resuelto, mostrar la landing page global
        if (!$tenant) {
            return app(\App\Http\Controllers\LandingController::class)->index();
        }

        $funciones = Funcion::with(['pelicula', 'sala'])
            ->where('fecha_hora', '>=', now())
            ->where('activo', 1)
            ->orderBy('fecha_hora', 'asc')
            ->get();

        return view('public.cartelera', compact('funciones', 'tenant'));
    }

    /**
     * Mostrar detalle de una función específica para reservar.
     */
    public function show($id)
    {
        $tenant = app('currentTenant');

        // Si no hay tenant resuelto, error
        if (!$tenant) {
            abort(404, 'Empresa no encontrada');
        }

        $funcion = Funcion::with(['pelicula', 'sala', 'asientos', 'precios'])
            ->findOrFail($id);

        $mapa = is_array($funcion->sala->configuracion_json)
            ? $funcion->sala->configuracion_json
            : json_decode($funcion->sala->configuracion_json, true);

        return view('public.funcion', compact('funcion', 'tenant', 'mapa'));
    }
}
