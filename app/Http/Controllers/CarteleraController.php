<?php

namespace App\Http\Controllers;

use App\Models\Funcion;
use App\Models\Marca;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CarteleraController extends Controller
{
    /**
     * Mostrar la cartelera pública de funciones
     */
    public function index(Request $request)
    {
        // El TenantResolver middleware ya puso 'currentTenant' en el app()
        // y HasEmpresaScope filtrará automáticamente.
        $funciones = Funcion::with(['pelicula', 'sala'])
            ->where('activo', true) // Solo activas
            ->where('fecha_hora', '>=', now()) // Solo futuras
            ->where('fecha_hora', '<=', now()->addDays(7)) // Próxima semana
            ->orderBy('fecha_hora')
            ->get()
            ->groupBy(function ($funcion) {
                return $funcion->pelicula ? $funcion->pelicula->titulo : 'Sin Título';
            });

        return view('cartelera.index', compact('funciones'));
    }
}
