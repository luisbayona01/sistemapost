<?php

namespace App\Services\Business;

use App\Models\Venta;
use App\Models\Insumo;
use App\Models\InsumoLote;
use App\Models\AuditoriaInventario;
use App\Models\Producto;
use App\Models\FuncionAsiento;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BusinessInsightsService
{
    public function generateInsights($empresaId)
    {
        $insights = [];

        // 1. IA FINANCIERA: Alerta por subida de costos
        $this->addFinancialInsights($empresaId, $insights);

        // 2. IA LOGÍSTICA: Preventas y ocupación
        $this->addLogisticsInsights($empresaId, $insights);

        // 3. IA DE INVENTARIO: Quiebre de Stock (Runway)
        $this->addInventoryStockOutInsights($empresaId, $insights);

        // 4. IA DETECTIVE: Correlación Auditoría vs Turnos (Cajas)
        $this->addDetectiveInsights($empresaId, $insights);

        // 5. Salud de Ventas Básica (Keep existing or mix)
        $this->addSalesHealthInsights($empresaId, $insights);

        // 6. Calidad de Datos (Importaciones)
        $this->addImportQualityInsights($empresaId, $insights);

        return $insights;
    }

    private function addImportQualityInsights($empresaId, &$insights)
    {
        $prodIncompletos = Producto::where('empresa_id', $empresaId)
            ->where(function ($q) {
                $q->where('precio', '<=', 0)->orWhere('costo_total_unitario', '<=', 0);
            })->count();

        if ($prodIncompletos > 0) {
            $insights[] = [
                'type' => 'warning',
                'title' => 'Calidad de Datos',
                'icon' => 'fas fa-tools',
                'message' => "Tienes {$prodIncompletos} productos con precio o costo en $0. Te sugerimos completarlos para reportes precisos."
            ];
        }

        $insumosSinCosto = Insumo::where('empresa_id', $empresaId)
            ->where('costo_unitario', '<=', 0)
            ->count();

        if ($insumosSinCosto > 0) {
            $insights[] = [
                'type' => 'info',
                'title' => 'Datos de Insumos',
                'icon' => 'fas fa-info-circle',
                'message' => "{$insumosSinCosto} insumos tienen costo $0. Esto afecta el cálculo de rentabilidad de tus recetas."
            ];
        }
    }

    private function addFinancialInsights($empresaId, &$insights)
    {
        // Buscamos insumos cuyo último lote sea más caro que el promedio histórico
        $insumosCriticos = Insumo::where('empresa_id', $empresaId)->get();
        /** @var Insumo $insumo */
        foreach ($insumosCriticos as $insumo) {
            $lotes = InsumoLote::where('insumo_id', $insumo->id)
                ->latest()
                ->take(5)
                ->get();

            if ($lotes->count() >= 2) {
                $ultimoCosto = $lotes[0]->costo_unitario;
                $costoPrevio = $lotes[1]->costo_unitario;

                if ($ultimoCosto > $costoPrevio * 1.05) { // Subida de más del 5%
                    $productosAfectados = $insumo->productos()->take(1)->get();
                    $msg = "El costo de '{$insumo->nombre}' subió un " . number_format((($ultimoCosto / $costoPrevio) - 1) * 100, 1) . "%. ";
                    if ($productosAfectados->count() > 0) {
                        $p = $productosAfectados[0];
                        $profitService = app(\App\Services\Inventory\ProfitabilityService::class);
                        $sugerido = $profitService->sugerirPrecio($p, 40);
                        $msg .= "Se sugiere ajustar el precio de '{$p->nombre}' a $" . number_format($sugerido, 0) . " para mantener margen del 40%.";
                    }

                    $insights[] = [
                        'type' => 'warning',
                        'title' => 'IA Financiera',
                        'icon' => 'fas fa-chart-line',
                        'message' => $msg
                    ];
                }
            }
        }
    }

    private function addLogisticsInsights($empresaId, &$insights)
    {
        $predictionService = app(\App\Services\Inventory\PredictionService::class);
        $mañana = Carbon::tomorrow();
        $preventas = \App\Models\Venta::boleteria()
            ->whereDate('created_at', '>=', Carbon::now()->subDays(2)) // Recientes
            ->count();

        // Ocupación futura (asientos reservados para mañana)
        $asientosReservados = \App\Models\FuncionAsiento::whereHas('funcion', function ($q) use ($mañana) {
            $q->whereDate('fecha_hora', $mañana);
        })->count();

        if ($asientosReservados > 50) { // Umbral de "alta ocupación"
            $insights[] = [
                'type' => 'info',
                'title' => 'IA Logística',
                'icon' => 'fas fa-truck-loading',
                'message' => "Se detectan {$asientosReservados} asientos reservados para mañana. Prepara stock extra de maíz y vasos de refresco."
            ];
        }
    }

    private function addInventoryStockOutInsights($empresaId, &$insights)
    {
        $insumos = Insumo::where('empresa_id', $empresaId)
            ->where('stock_actual', '>', 0)
            ->get();

        $predictionService = app(\App\Services\Inventory\PredictionService::class);

        /** @var Insumo $insumo */
        foreach ($insumos as $insumo) {
            $analisis = $predictionService->sugerirCompra($insumo, 7);
            $consumoDiario = $analisis['promedio_diario'];

            if ($consumoDiario > 0) {
                $horasRestantes = ($insumo->stock_actual / $consumoDiario) * 24;
                if ($horasRestantes < 48) {
                    $insights[] = [
                        'type' => 'danger',
                        'title' => 'IA de Inventario',
                        'icon' => 'fas fa-exclamation-triangle',
                        'message' => "Al ritmo actual, '{$insumo->nombre}' se agotará en menos de " . round($horasRestantes) . " horas."
                    ];
                }
            }
        }
    }

    private function addDetectiveInsights($empresaId, &$insights)
    {
        // Buscar auditorías con pérdidas significativas y cruzar con la caja de ese día
        $auditoriasMalas = \App\Models\AuditoriaInventario::where('empresa_id', $empresaId)
            ->where('total_diferencia_valor', '<', -50000)
            ->with('user')
            ->latest()
            ->take(3)
            ->get();

        foreach ($auditoriasMalas as $audit) {
            $insights[] = [
                'type' => 'warning',
                'title' => 'IA Detective',
                'icon' => 'fas fa-user-secret',
                'message' => "Pérdida de $" . number_format(abs($audit->total_diferencia_valor), 0) . " detectada en auditoría #{$audit->id}. Se sugiere revisar los cierres de caja del usuario {$audit->user->name} en esa fecha."
            ];
        }
    }

    private function addSalesHealthInsights($empresaId, &$insights)
    {
        $ventasHoy = Venta::where('empresa_id', $empresaId)->whereDate('fecha_hora', Carbon::today())->sum('total');
        $ventasAyer = Venta::where('empresa_id', $empresaId)->whereDate('fecha_hora', Carbon::yesterday())->sum('total');

        if ($ventasHoy > 0) {
            $diff = $ventasAyer > 0 ? (($ventasHoy / $ventasAyer) - 1) * 100 : 100;
            $insights[] = [
                'type' => $diff >= 0 ? 'success' : 'warning',
                'title' => 'Rendimiento Diario',
                'icon' => 'fas fa-cash-register',
                'message' => "Las ventas hoy van un " . number_format(abs($diff), 1) . "% " . ($diff >= 0 ? "arriba" : "abajo") . " comparado con ayer."
            ];
        }
    }

    /**
     * Lógica para el Widget de Top Ventas (Crecimiento vs Estancamiento)
     */
    public function getTrendingProducts($empresaId)
    {
        $hoy = Carbon::now();
        $semanaActual = [$hoy->copy()->subDays(7), $hoy];
        $semanaPrevia = [$hoy->copy()->subDays(14), $hoy->copy()->subDays(7)];

        $productos = \App\Models\Producto::retail()->get();
        $trending = [];

        foreach ($productos as $p) {
            $ventasActual = \DB::table('producto_venta')
                ->join('ventas', 'ventas.id', '=', 'producto_venta.venta_id')
                ->where('producto_id', $p->id)
                ->whereBetween('ventas.created_at', $semanaActual)
                ->sum('cantidad');

            $ventasPrevia = \DB::table('producto_venta')
                ->join('ventas', 'ventas.id', '=', 'producto_venta.venta_id')
                ->where('producto_id', $p->id)
                ->whereBetween('ventas.created_at', $semanaPrevia)
                ->sum('cantidad');

            $crecimiento = $ventasPrevia > 0 ? (($ventasActual / $ventasPrevia) - 1) * 100 : ($ventasActual > 0 ? 100 : 0);

            $trending[] = [
                'nombre' => $p->nombre,
                'ventas_actual' => (int) $ventasActual,
                'crecimiento' => $crecimiento,
                'status' => $crecimiento > 10 ? 'creciendo' : ($crecimiento < -10 ? 'estancado' : 'estable')
            ];
        }

        return collect($trending)->sortByDesc('crecimiento')->values();
    }
}
