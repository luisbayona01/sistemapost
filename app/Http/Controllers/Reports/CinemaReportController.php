<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Venta;
use App\Models\Funcion;
use App\Models\Sala;
use App\Models\Producto;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class CinemaReportController extends Controller
{
    /**
     * Reporte Principal de Taquilla y Ocupación
     */
    public function index(Request $request): View
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->toDateString());
        $peliculaId = $request->get('pelicula_id');
        $funcionId = $request->get('funcion_id');

        // 1. Ingresos Totales (Solo Tickets)
        $queryIngresos = Venta::boleteria()
            ->whereBetween('created_at', [$startDate, $endDate . ' 23:59:59']);

        if ($peliculaId) {
            $queryIngresos->whereHas('funcionAsientos.funcion', function ($q) use ($peliculaId) {
                $q->where('pelicula_id', $peliculaId);
            });
        }

        if ($funcionId) {
            $queryIngresos->whereHas('funcionAsientos', function ($q) use ($funcionId) {
                $q->where('funcion_id', $funcionId);
            });
        }

        $ingresosTickets = $queryIngresos->sum('total');

        // 2. Boletos Vendidos
        $queryBoletos = DB::table('producto_venta')
            ->join('ventas', 'ventas.id', '=', 'producto_venta.venta_id')
            ->join('productos', 'productos.id', '=', 'producto_venta.producto_id')
            ->where('ventas.canal', 'ventanilla')
            ->where('productos.es_venta_retail', false)
            ->whereBetween('ventas.created_at', [$startDate, $endDate . ' 23:59:59']);

        if ($peliculaId) {
            $queryBoletos->join('funcion_asientos', 'ventas.id', '=', 'funcion_asientos.venta_id')
                ->join('funciones', 'funciones.id', '=', 'funcion_asientos.funcion_id')
                ->where('funciones.pelicula_id', $peliculaId);
        }

        if ($funcionId) {
            $queryBoletos->whereExists(function ($q) use ($funcionId) {
                $q->select(DB::raw(1))
                    ->from('funcion_asientos')
                    ->whereColumn('funcion_asientos.venta_id', 'ventas.id')
                    ->where('funcion_asientos.funcion_id', $funcionId);
            });
        }

        $boletosVendidos = $queryBoletos->sum('producto_venta.cantidad');

        // 3. Ocupación Promedio
        $queryFunciones = Funcion::whereBetween('fecha_hora', [$startDate, $endDate . ' 23:59:59']);
        if ($peliculaId)
            $queryFunciones->where('pelicula_id', $peliculaId);
        if ($funcionId)
            $queryFunciones->where('id', $funcionId);

        $funciones = $queryFunciones->with('sala')->get();
        $capacidadTotal = $funciones->sum(function ($f) {
            return $f->sala->capacidad ?? 0;
        });

        $ocupacionPromedio = $capacidadTotal > 0 ? ($boletosVendidos / $capacidadTotal) * 100 : 0;

        // 4. Películas más Taquilleras
        $topMovies = DB::table('peliculas')
            ->join('funciones', 'peliculas.id', '=', 'funciones.pelicula_id')
            ->join('funcion_asientos', 'funciones.id', '=', 'funcion_asientos.funcion_id')
            ->join('ventas', 'funcion_asientos.venta_id', '=', 'ventas.id')
            ->where('funcion_asientos.estado', 'vendido')
            ->whereBetween('ventas.created_at', [$startDate, $endDate . ' 23:59:59'])
            ->select('peliculas.titulo as nombre', DB::raw('SUM(ventas.total) as total_ventas'), DB::raw('COUNT(funcion_asientos.id) as tickets_vendidos'))
            ->groupBy('peliculas.id', 'peliculas.titulo')
            ->orderByDesc('total_ventas')
            ->limit(5)
            ->get();

        $peliculas = \App\Models\Pelicula::all();
        $funcionesList = [];
        if ($peliculaId) {
            $funcionesList = Funcion::where('pelicula_id', $peliculaId)
                ->whereBetween('fecha_hora', [$startDate, $endDate . ' 23:59:59'])
                ->get();
        }

        return view('admin.reportes.cinema', compact(
            'ingresosTickets',
            'boletosVendidos',
            'ocupacionPromedio',
            'topMovies',
            'startDate',
            'endDate',
            'peliculas',
            'funcionesList',
            'peliculaId',
            'funcionId'
        ));
    }
}
