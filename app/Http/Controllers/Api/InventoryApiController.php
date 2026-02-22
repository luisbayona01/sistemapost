<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Insumo;
use App\Models\Producto;
use App\Services\Inventory\InventoryService;
use App\Services\Inventory\ProfitabilityService;
use Illuminate\Http\JsonResponse;

class InventoryApiController extends Controller
{
    protected $inventoryService;
    protected $profitabilityService;

    public function __construct(InventoryService $inventoryService, ProfitabilityService $profitabilityService)
    {
        $this->inventoryService = $inventoryService;
        $this->profitabilityService = $profitabilityService;
    }

    /**
     * Get real-time asset valuation
     */
    public function assetValuation(): JsonResponse
    {
        $empresaId = auth()->user()->empresa_id;
        $value = $this->inventoryService->obtenerValorInventario($empresaId);

        return response()->json([
            'success' => true,
            'data' => [
                'valuation' => $value,
                'currency' => 'COP',
                'timestamp' => now()
            ]
        ]);
    }

    /**
     * Get profitability analysis for a product
     */
    public function productProfitability(Producto $producto): JsonResponse
    {
        $analysis = $this->profitabilityService->analizarRentabilidad($producto);

        return response()->json([
            'success' => true,
            'data' => $analysis
        ]);
    }

    /**
     * Get stock levels for all supplies
     */
    public function stockLevels(): JsonResponse
    {
        $insumos = Insumo::select('id', 'nombre', 'stock_actual', 'stock_minimo', 'unidad_medida')->get();

        return response()->json([
            'success' => true,
            'data' => $insumos
        ]);
    }
}
