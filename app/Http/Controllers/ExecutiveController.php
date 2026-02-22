<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Services\AccountingService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ExecutiveController extends Controller
{
    protected $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
        $this->middleware('auth');
        // Solo gerentes, administradores o root pueden ver esto
        $this->middleware('role:Root|Gerente|administrador');
    }

    public function dashboard(Request $request)
    {
        $empresaId = auth()->user()->empresa_id;

        // Obtenemos la fecha operativa actual o una específica si viene por request
        $fechaFiltro = $request->get('fecha', $this->accountingService->getActiveDay($empresaId)->format('Y-m-d'));

        $ventas = Venta::where('empresa_id', $empresaId)
            ->where('fecha_operativa', $fechaFiltro)
            ->where('estado_pago', 'PAGADA')
            ->get();

        $stats = [
            'fecha_operativa' => Carbon::parse($fechaFiltro)->format('d M, Y'),
            'total_ventas' => $ventas->sum('total_final'),
            'total_efectivo' => $ventas->where('metodo_pago', 'EFECTIVO')->sum('total_final'),
            'total_tarjetas' => $ventas->where('metodo_pago', 'TARJETA')->sum('total_final'),
            'inc_recaudado' => $ventas->sum('inc_total'),
            'conteo_ventas' => $ventas->count(),
            'por_canal' => [
                'cine' => $ventas->where('canal', 'ventanilla')->sum('total_final'),
                'confiteria' => $ventas->where('canal', 'confiteria')->sum('total_final'),
                'mixta' => $ventas->where('canal', 'mixta')->sum('total_final'),
            ]
        ];

        // FIX 5: Breakdown por Película (boletos + recaudación por función)
        $breakdownPeliculas = DB::table('funcion_asientos')
            ->join('funciones', 'funcion_asientos.funcion_id', '=', 'funciones.id')
            ->join('peliculas', 'funciones.pelicula_id', '=', 'peliculas.id')
            ->join('salas', 'funciones.sala_id', '=', 'salas.id')
            ->join('ventas', 'funcion_asientos.venta_id', '=', 'ventas.id')
            ->where('ventas.empresa_id', $empresaId)
            ->where('ventas.fecha_operativa', $fechaFiltro)
            ->where('ventas.estado_pago', 'PAGADA')
            ->select(
                'peliculas.titulo',
                'funciones.fecha_hora',
                'salas.nombre as sala',
                DB::raw('COUNT(funcion_asientos.id) as boletos_vendidos'),
                DB::raw('SUM(ventas.total) as total_recaudado'),
                DB::raw('salas.capacidad as capacidad_sala')
            )
            ->groupBy('funciones.id', 'peliculas.titulo', 'funciones.fecha_hora', 'salas.nombre', 'salas.capacidad')
            ->orderByDesc('boletos_vendidos')
            ->get();

        return view('executive.dashboard', compact('stats', 'fechaFiltro', 'breakdownPeliculas'));
    }
}
