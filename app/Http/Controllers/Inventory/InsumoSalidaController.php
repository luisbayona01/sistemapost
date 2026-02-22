<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Services\Inventory\InventoryService;
use App\Enums\TipoMovimientoEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\ActivityLogService;

class InsumoSalidaController extends Controller
{
    protected $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function create(Request $request)
    {
        $tipo = $request->get('tipo', 'BAJA');
        $insumos = \App\Models\Insumo::where('empresa_id', auth()->user()->empresa_id)->get();
        return view('admin.inventario-avanzado.baja.create', compact('insumos', 'tipo'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'insumo_id' => 'required|exists:insumos,id',
            'cantidad' => 'required|numeric|min:0.01',
            'motivo' => 'required|string|min:5|max:255',
            'tipo' => 'required|in:BAJA,CORTESIA,MERMA'
        ]);

        try {
            $salida = $this->inventoryService->registrarSalidaEspecial(
                $validated['insumo_id'],
                $validated['cantidad'],
                $validated['tipo'],
                $validated['motivo'],
                auth()->id()
            );

            ActivityLogService::log('Baja de Inventario', 'Inventario', $validated);

            // Redirigir al ticket (PDF o Vista imprimible)
            return redirect()->route('inventario-avanzado.baja.ticket', $salida->id)->with('success', 'Baja registrada correctamente.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al registrar baja: ' . $e->getMessage());
        }
    }

    public function ticket($id)
    {
        $salida = \App\Models\InsumoSalida::with(['insumo', 'user'])->findOrFail($id);
        return view('admin.inventario-avanzado.baja.ticket', compact('salida'));
    }
}
