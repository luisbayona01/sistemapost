<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConsolidatedReportController extends Controller
{
    /**
     * Vista principal de reporte consolidado (Consolidación Fase 4)
     */
    public function index(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', now()->subDays(7)->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', now()->format('Y-m-d'));

        $empresaId = auth()->user()->empresa_id;

        // Obtener datos consolidados
        $consolidado = $this->getConsolidatedData($empresaId, $fechaInicio, $fechaFin);

        return view('admin.reportes.consolidado', compact('consolidado', 'fechaInicio', 'fechaFin'));
    }

    /**
     * Obtiene datos consolidados de boletería y confitería
     * Regla: No usar funcion_asientos.precio (SQL Error)
     * Regla: Eliminar concepto "mixta" de auditoría
     */
    private function getConsolidatedData($empresaId, $fechaInicio, $fechaFin)
    {
        // 1. ANCLA DE VERDAD: Total de ventas cobradas (DIAN 2026)
        $queryBase = Venta::where('empresa_id', $empresaId)
            ->whereBetween('fecha_operativa', [$fechaInicio, $fechaFin])
            ->where('estado_pago', 'PAGADA');

        $totalGeneral = $queryBase->sum('total_final');
        $totalTransacciones = $queryBase->count();
        $totalINCGeneral = $queryBase->sum('inc_total');

        // 2. DULCERÍA / CONFITERÍA: Usando el campo auditado
        $ingresoConfiteria = $queryBase->sum('subtotal_confiteria');
        $incConfiteria = $totalINCGeneral;

        $transaccionesConfiteria = DB::table('ventas')
            ->where('empresa_id', $empresaId)
            ->whereIn('canal', ['confiteria', 'mixta'])
            ->whereBetween('fecha_operativa', [$fechaInicio, $fechaFin])
            ->where('estado_pago', 'PAGADA')
            ->count();

        // 3. BOLETERÍA / ENTRADAS: Usando el campo auditado (Exento)
        $ingresoBoleteria = $queryBase->sum('subtotal_cine');

        $transaccionesBoleteria = DB::table('ventas')
            ->where('empresa_id', $empresaId)
            ->whereIn('canal', ['ventanilla', 'web', 'mixta'])
            ->whereBetween('fecha_operativa', [$fechaInicio, $fechaFin])
            ->where('estado_pago', 'PAGADA')
            ->count();

        // Estructurar datos finales
        $boleteria = (object) [
            'total_transacciones' => $transaccionesBoleteria,
            'ingreso_total' => $ingresoBoleteria,
            'total_impuestos' => 0, // Exento
            'ingreso_neto' => $ingresoBoleteria
        ];

        $confiteria = (object) [
            'total_transacciones' => $transaccionesConfiteria,
            'ingreso_total' => $ingresoConfiteria + $incConfiteria,
            'total_impuestos' => $incConfiteria,
            'ingreso_neto' => $ingresoConfiteria
        ];

        $total = (object) [
            'total_transacciones' => $totalTransacciones,
            'ingreso_total' => $totalGeneral,
            'total_impuestos' => $totalINCGeneral,
            'ingreso_neto' => $totalGeneral - $totalINCGeneral,
        ];

        // Porcentajes de participación
        $participacion = [
            'boleteria_porcentaje' => $totalGeneral > 0 ? ($ingresoBoleteria / $totalGeneral) * 100 : 0,
            'confiteria_porcentaje' => $totalGeneral > 0 ? (($ingresoConfiteria + $incConfiteria) / $totalGeneral) * 100 : 0
        ];

        return [
            'boleteria' => $boleteria,
            'confiteria' => $confiteria,
            'total' => $total,
            'participacion' => $participacion,
        ];
    }
}
