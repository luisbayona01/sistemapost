<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Services\Inventory\PredictionService;
use App\Services\Inventory\ProfitabilityService;
use App\Models\Insumo;
use App\Models\InsumoLote;
use App\Services\Inventory\FinancialReportService;
use Illuminate\Http\Request;

class InventoryDashboardController extends Controller
{
    protected $predictionService;
    protected $profitabilityService;
    protected $financialReportService;
    protected $businessInsightsService;
    protected $auditService;

    public function __construct(
        PredictionService $predictionService,
        ProfitabilityService $profitabilityService,
        FinancialReportService $financialReportService,
        \App\Services\Business\BusinessInsightsService $businessInsightsService,
        \App\Services\Inventory\AuditService $auditService
    ) {
        $this->predictionService = $predictionService;
        $this->profitabilityService = $profitabilityService;
        $this->financialReportService = $financialReportService;
        $this->businessInsightsService = $businessInsightsService;
        $this->auditService = $auditService;
    }

    public function index(Request $request)
    {
        $empresaId = auth()->user()->empresa_id;

        // Rango dinámico para sugerencias (default 7 días / semana)
        $rangoDias = $request->get('range', 7);

        // 0. Reto de Auditoría Ciega
        $productosAuditoria = $this->auditService->getDailyAuditChallenge($empresaId);

        // 1. Matriz de Boston
        $matrizBoston = $this->profitabilityService->generarMatrizBoston($empresaId);

        // 2. Alertas Críticas (Riesgo de Desabastecimiento)
        // Nota: Las alertas críticas siguen siendo "críticas" sin importar el rango de compra larga,
        // pero podemos ajustar la proyección si el usuario quiere comprar para 15 días.
        $alertasRiesgo = [];
        $insumos = Insumo::where('empresa_id', $empresaId)->get();
        /** @var Insumo $insumo */
        foreach ($insumos as $insumo) {
            $analisis = $this->predictionService->sugerirCompra($insumo, $rangoDias);
            if ($analisis['riesgo_desabastecimiento']) {
                $alertasRiesgo[] = $analisis;
            }
        }

        // 3. Alertas de Vencimiento y KPIs
        $alertasVencimiento = InsumoLote::whereHas('insumo', function ($q) use ($empresaId) {
            $q->where('empresa_id', $empresaId);
        })
            ->where('cantidad_actual', '>', 0)
            ->whereBetween('fecha_vencimiento', [now(), now()->addDays(7)])
            ->with('insumo')
            ->get();

        // Calcular Valor Activos (Suma de costo * stock de insumos)
        $valorActivos = Insumo::where('empresa_id', $empresaId)
            ->where('stock_actual', '>', 0)
            ->get()
            ->sum(function ($insumo) {
                return $insumo->stock_actual * $insumo->costo_unitario;
            });

        // Contar Stock Bajo (KPI)
        $stockBajoCount = count($alertasRiesgo);

        // 4. Plan de Compras Dinámico
        $planCompras = $this->predictionService->generarPlanCompras($empresaId, $rangoDias);

        // 5. Resumen Financiero (P&L Mes Actual)
        $resumenFinanciero = $this->financialReportService->obtenerResumenFinanciero($empresaId, 'mes');

        // 6. Business Insights (Consejos)
        $insights = $this->businessInsightsService->generateInsights($empresaId);

        // 7. Productos Trending (Crecimiento/Estancamiento)
        $trendingProducts = $this->businessInsightsService->getTrendingProducts($empresaId);

        return view('admin.inventario-avanzado.dashboard', compact(
            'matrizBoston',
            'alertasRiesgo',
            'alertasVencimiento',
            'planCompras',
            'resumenFinanciero',
            'insights',
            'trendingProducts',
            'rangoDias',
            'productosAuditoria',
            'valorActivos',
            'stockBajoCount'
        ));
    }
}
