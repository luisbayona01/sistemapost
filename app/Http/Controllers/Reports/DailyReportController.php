<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Venta;
use Illuminate\Support\Facades\DB;

class DailyReportController extends Controller
{
    protected $accountingService;

    public function __construct(\App\Services\AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    /**
     * Reporte Diario de Ventas (Placeholder para ruta solicitada)
     */
    public function __invoke(Request $request)
    {
        $empresaId = auth()->user()->empresa_id;

        // Si no viene fecha, usamos el día operativo actual
        $fecha = $request->input('fecha') ?: $this->accountingService->getActiveDay($empresaId)->format('Y-m-d');

        // Reutilizamos lógica de consolidado pero usando la fecha operativa
        $ventas = Venta::where('empresa_id', $empresaId)
            ->where('fecha_operativa', $fecha)
            ->where('estado_pago', 'PAGADA')
            ->get();

        $totalGeneral = $ventas->sum('total_final');
        $totalCine = $ventas->sum('subtotal_cine');
        $totalConfiteria = $ventas->sum('subtotal_confiteria');
        $totalINC = $ventas->sum('inc_total');

        $resumenMedios = $ventas->groupBy('metodo_pago')
            ->map(function ($group) {
                return $group->sum('total_final');
            });

        return view('admin.reportes.diario', compact('ventas', 'totalGeneral', 'totalCine', 'totalConfiteria', 'totalINC', 'resumenMedios', 'fecha'));
    }
}
