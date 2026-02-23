<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FiscalReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:Root|Gerente|administrador');
    }

    /**
     * Vista de reporte bimestral de INC
     */
    public function bimestralINC(Request $request)
    {
        $empresaId = auth()->user()->empresa_id;
        $year = $request->get('year', date('Y'));
        $bimestre = $request->get('bimestre', ceil(date('n') / 2));

        $meses = $this->getMesesBimestre($bimestre);

        $data = Venta::where('empresa_id', $empresaId)
            ->where('estado_pago', 'PAGADA')
            ->whereYear('fecha_operativa', $year)
            ->whereIn(DB::raw('MONTH(fecha_operativa)'), $meses)
            ->select(
                DB::raw('MONTH(fecha_operativa) as mes'),
                DB::raw('SUM(subtotal_confiteria) as base_gravable'),
                DB::raw('SUM(inc_total) as inc_causado'),
                DB::raw('SUM(total_final) as total_ventas')
            )
            ->groupBy('mes')
            ->get();

        $totales = [
            'base' => $data->sum('base_gravable'),
            'inc' => $data->sum('inc_causado'),
            'total' => $data->sum('total_ventas')
        ];

        return view('admin.reports.fiscal.inc', compact('data', 'totales', 'year', 'bimestre'));
    }

    /**
     * Exportar a Excel/CSV (Simulado con descarga de texto por ahora o CSV básico)
     */
    public function exportINC(Request $request)
    {
        $empresaId = auth()->user()->empresa_id;
        $year = $request->get('year', date('Y'));
        $bimestre = $request->get('bimestre', ceil(date('n') / 2));
        $meses = $this->getMesesBimestre($bimestre);

        $filename = "Reporte_INC_{$year}_B{$bimestre}.csv";
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $data = Venta::where('empresa_id', $empresaId)
            ->where('estado_pago', 'PAGADA')
            ->whereYear('fecha_operativa', $year)
            ->whereIn(DB::raw('MONTH(fecha_operativa)'), $meses)
            ->with(['cliente'])
            ->get();

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['FECHA OPERATIVA', 'COMPROBANTE', 'CLIENTE', 'BASE GRAVABLE INC', 'INC (8%)', 'TOTAL']);

            foreach ($data as $venta) {
                fputcsv($file, [
                    $venta->fecha_operativa->format('Y-m-d'),
                    $venta->numero_comprobante,
                    $venta->cliente->razon_social ?? 'Consumidor Final',
                    $venta->subtotal_confiteria,
                    $venta->inc_total,
                    $venta->total_final
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getMesesBimestre($bimestre)
    {
        $bimestres = [
            1 => [1, 2],
            2 => [3, 4],
            3 => [5, 6],
            4 => [7, 8],
            5 => [9, 10],
            6 => [11, 12]
        ];
        return $bimestres[$bimestre] ?? [1, 2];
    }
}
