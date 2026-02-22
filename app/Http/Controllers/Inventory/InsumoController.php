<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Insumo;
use App\Services\Inventory\InventoryService;
use Illuminate\Http\Request;

class InsumoController extends Controller
{
    protected $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string',
            'codigo' => 'nullable|string',
            'unidad_medida' => 'required|in:kg,g,l,ml,und,oz,lb',
            'stock_minimo' => 'required|numeric'
        ]);

        $data['empresa_id'] = auth()->user()->empresa_id;
        $insumo = Insumo::create($data);

        return redirect()->back()->with('success', 'Insumo creado correctamente');
    }

    public function storeLote(Request $request, Insumo $insumo)
    {
        $data = $request->validate([
            'cantidad' => 'required|numeric|min:0.001',
            'costo_unitario' => 'required|numeric|min:0',
            'numero_lote' => 'nullable|string',
            'fecha_vencimiento' => 'nullable|date',
            'descripcion' => 'required|string|max:255'
        ]);

        $this->inventoryService->registrarEntrada(
            $insumo->id,
            $data['cantidad'],
            $data['costo_unitario'],
            $data['numero_lote'],
            $data['fecha_vencimiento'],
            [
                'descripcion' => $data['descripcion']
            ]
        );

        return redirect()->back()->with('success', 'Stock cargado (Lote registrado)');
    }

    public function update(Request $request, Insumo $insumo)
    {
        $data = $request->validate([
            'nombre' => 'required|string',
            'unidad_medida' => 'required|in:kg,g,l,ml,und,oz,lb',
            'stock_minimo' => 'required|numeric'
        ]);

        $insumo->update($data);

        return redirect()->back()->with('success', 'Insumo actualizado correctamente');
    }
}
