<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Insumo;
use App\Models\InsumoSalida;
use App\Models\AuditoriaInventario;
use App\Models\Producto;
use App\Services\Inventory\InventoryService;
use App\Services\Inventory\ProfitabilityService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryController extends Controller
{
    protected $inventoryService;
    protected $profitabilityService;

    public function __construct(InventoryService $inventoryService, ProfitabilityService $profitabilityService)
    {
        $this->inventoryService = $inventoryService;
        $this->profitabilityService = $profitabilityService;
    }

    public function index(): View
    {
        $empresaId = auth()->user()->empresa_id;
        $valorActivos = $this->inventoryService->obtenerValorInventario($empresaId);
        $stockBajoCount = Insumo::whereColumn('stock_actual', '<=', 'stock_minimo')->count();
        $alertasVencimiento = \App\Models\InsumoLote::where('fecha_vencimiento', '<=', now()->addDays(7))
            ->where('cantidad_actual', '>', 0)
            ->with('insumo')
            ->get();

        return view('admin.inventario-avanzado.dashboard', compact('valorActivos', 'stockBajoCount', 'alertasVencimiento'));
    }

    public function almacen(): View
    {
        $insumos = Insumo::with('lotes')->orderBy('nombre', 'asc')->get();
        return view('admin.inventario-avanzado.almacen', compact('insumos'));
    }

    public function cocina(): View
    {
        $productos = Producto::retail()
            ->latest('id')
            ->with('insumos')
            ->get();
        // Calculamos rentabilidad para cada uno (esto se puede optimizar con caché o campos calculados)
        foreach ($productos as $producto) {
            $producto->analisis = $this->profitabilityService->analizarRentabilidad($producto);
        }

        $insumosBase = Insumo::all();
        return view('admin.inventario-avanzado.cocina', compact('productos', 'insumosBase'));
    }

    public function updatePrecio(Request $request, Producto $producto)
    {
        $request->validate([
            'precio' => 'required|numeric|min:0',
            'gasto_operativo_fijo' => 'nullable|numeric|min:0',
            'tipo_impuesto' => 'nullable|in:IVA,IMPOCONSUMO,EXENTO',
            'porcentaje_impuesto' => 'nullable|numeric|min:0|max:100',
        ]);

        $producto->update([
            'precio' => $request->precio,
            'gasto_operativo_fijo' => $request->gasto_operativo_fijo ?? $producto->gasto_operativo_fijo,
            'tipo_impuesto' => $request->tipo_impuesto ?? $producto->tipo_impuesto,
            'porcentaje_impuesto' => $request->porcentaje_impuesto ?? $producto->porcentaje_impuesto,
        ]);

        return redirect()->back()->with('success', 'Configuración de precio y costos actualizada');
    }

    public function auditoria(): View
    {
        $auditorias = AuditoriaInventario::with('user')->latest()->get();
        return view('admin.inventario-avanzado.auditoria', compact('auditorias'));
    }
}
