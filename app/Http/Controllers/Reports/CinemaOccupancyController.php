<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\{Funcion, FuncionAsiento};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CinemaOccupancyController extends Controller
{
    public function index(Request $request)
    {
        $empresaId = auth()->user()->empresa_id;
        $fechaInicio = $request->input('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', now()->format('Y-m-d'));

        $funciones = Funcion::with(['pelicula', 'sala'])
            ->where('empresa_id', $empresaId)
            ->whereBetween('fecha_hora', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
            ->get()
            ->map(function ($funcion) {

                $totalAsientos = $funcion->sala->capacidad_total ?? 0;

                $asientosVendidos = FuncionAsiento::where('funcion_id', $funcion->id)
                    ->where('estado', 'vendido')
                    ->count();

                $ocupacion = $totalAsientos > 0 ? ($asientosVendidos / $totalAsientos) * 100 : 0;

                $ingresoTotal = DB::table('venta_funcion_asientos')
                    ->join('ventas', 'venta_funcion_asientos.venta_id', '=', 'ventas.id')
                    ->join('funcion_asientos', 'venta_funcion_asientos.funcion_asiento_id', '=', 'funcion_asientos.id')
                    ->where('funcion_asientos.funcion_id', $funcion->id)
                    ->where('ventas.estado_pago', 'PAGADA')
                    ->sum('ventas.total');

                return [
                    'funcion_id' => $funcion->id,
                    'pelicula' => $funcion->pelicula?->titulo ?? 'Sin título',
                    'sala' => $funcion->sala?->nombre ?? 'Sin sala',
                    'fecha_hora' => $funcion->fecha_hora->format('d/m/Y H:i'),
                    'capacidad_total' => $totalAsientos,
                    'asientos_vendidos' => $asientosVendidos,
                    'asientos_disponibles' => $totalAsientos - $asientosVendidos,
                    'ocupacion_porcentaje' => round($ocupacion, 1),
                    'ingreso_total' => $ingresoTotal,
                ];
            });

        return view('admin.reportes.cinema-occupancy', compact('funciones', 'fechaInicio', 'fechaFin'));
    }
}
