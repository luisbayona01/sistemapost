<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Venta;
use Carbon\Carbon;

class ReportePeliculasController extends Controller
{
    protected $accountingService;

    public function __construct(\App\Services\AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    /**
     * Reporte consolidado de películas
     */
    public function index(Request $request)
    {
        $empresaId = auth()->user()->empresa_id;
        $activeDay = $this->accountingService->getActiveDay($empresaId);

        $fecha = $request->input('fecha') ?: $activeDay->format('Y-m-d');
        $salaId = $request->input('sala_id');

        $dataHoy = $this->queryPeliculas($fecha, $salaId);
        $dataMes = $this->ultimoMes();

        // Detalle por función (lo que pidió el usuario: fecha, horario, sala)
        $funcionesDetalle = $this->detalleFunciones($fecha, $salaId);

        $salas = \App\Models\Sala::where('empresa_id', $empresaId)->get();

        return view('reportes.peliculas', [
            'hoy' => $dataHoy,
            'ultimoMes' => $dataMes,
            'funcionesDetalle' => $funcionesDetalle,
            'fecha' => $fecha,
            'salaId' => $salaId,
            'salas' => $salas
        ]);
    }

    /**
     * Exportar reporte a Excel
     */
    public function export(Request $request)
    {
        $fecha = $request->input('fecha');
        $salaId = $request->input('sala_id');

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\MovieReportExport($fecha, $salaId),
            'reporte_peliculas_' . now()->format('Y_m_d') . '.xlsx'
        );
    }

    /**
     * Consulta agrupada por película para el día seleccionado
     */
    private function queryPeliculas($fecha, $salaId = null)
    {
        $empresaId = auth()->user()->empresa_id;

        $query = DB::table('funcion_asientos as fa')
            ->join('ventas as v', 'v.id', '=', 'fa.venta_id')
            ->join('funciones as f', 'f.id', '=', 'fa.funcion_id')
            ->join('peliculas as p', 'p.id', '=', 'f.pelicula_id')
            ->where('v.empresa_id', $empresaId)
            ->where('v.estado_pago', 'PAGADA')
            ->where('v.fecha_operativa', $fecha);

        if ($salaId) {
            $query->where('f.sala_id', $salaId);
        }

        return $query->select(
            'p.titulo as pelicula',
            DB::raw('COUNT(fa.id) as cantidad_boletos'),
            DB::raw('SUM(f.precio) as monto_pesos')
        )
            ->groupBy('p.id', 'p.titulo')
            ->orderBy('monto_pesos', 'desc')
            ->get();
    }

    /**
     * Consulta detallada por función (Horarios, Salas)
     */
    private function detalleFunciones($fecha, $salaId = null)
    {
        $empresaId = auth()->user()->empresa_id;

        $query = DB::table('funcion_asientos as fa')
            ->join('ventas as v', 'v.id', '=', 'fa.venta_id')
            ->join('funciones as f', 'f.id', '=', 'fa.funcion_id')
            ->join('peliculas as p', 'p.id', '=', 'f.pelicula_id')
            ->join('salas as s', 's.id', '=', 'f.sala_id')
            ->where('v.empresa_id', $empresaId)
            ->where('v.estado_pago', 'PAGADA');

        // Para el detalle permitimos filtrar por fecha real de la función o operativa
        // Usaremos la operativa para consistencia con el consolidado
        $query->where('v.fecha_operativa', $fecha);

        if ($salaId) {
            $query->where('f.sala_id', $salaId);
        }

        return $query->select(
            'p.titulo as pelicula',
            's.nombre as sala',
            'f.fecha_hora',
            DB::raw('COUNT(fa.id) as boletos'),
            DB::raw('SUM(f.precio) as monto')
        )
            ->groupBy('p.id', 'p.titulo', 's.id', 's.nombre', 'f.id', 'f.fecha_hora')
            ->orderBy('f.fecha_hora', 'asc')
            ->get();
    }

    /**
     * Resumen de las últimas 4 semanas
     */
    public function ultimoMes()
    {
        $empresaId = auth()->user()->empresa_id;
        $fechaInicio = now()->subDays(28)->format('Y-m-d');

        $stats = DB::table('funcion_asientos as fa')
            ->join('ventas as v', 'v.id', '=', 'fa.venta_id')
            ->join('funciones as f', 'f.id', '=', 'fa.funcion_id')
            ->join('peliculas as p', 'p.id', '=', 'f.pelicula_id')
            ->where('v.empresa_id', $empresaId)
            ->where('v.estado_pago', 'PAGADA')
            ->where('v.fecha_operativa', '>=', $fechaInicio)
            ->select(
                'p.titulo as pelicula',
                DB::raw('COUNT(fa.id) as boletos'),
                DB::raw('SUM(f.precio) as monto')
            )
            ->groupBy('p.id', 'p.titulo')
            ->get();

        $totalMonto = $stats->sum('monto');

        return $stats->map(function ($item) use ($totalMonto) {
            $item->porcentaje = $totalMonto > 0 ? ($item->monto / $totalMonto) * 100 : 0;
            return $item;
        })->sortByDesc('monto');
    }
}
