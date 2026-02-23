<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Receta;
use Illuminate\Http\Request;

class RecipeController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'insumo_id' => 'required|exists:insumos,id',
            'cantidad' => 'required|numeric|min:0.001',
            'unidad_medida' => 'nullable|string',
            'merma_esperada' => 'nullable|numeric|min:0|max:100'
        ]);

        Receta::updateOrCreate(
            ['producto_id' => $data['producto_id'], 'insumo_id' => $data['insumo_id']],
            [
                'cantidad' => $data['cantidad'],
                'unidad_medida' => $data['unidad_medida'] ?? null,
                'merma_esperada' => $data['merma_esperada'] ?? 0
            ]
        );

        return redirect()->back()->with('success', 'Insumo vinculado a la receta');
    }

    public function destroy(Receta $receta)
    {
        $receta->delete();
        return redirect()->back()->with('success', 'Insumo removido de la receta');
    }
}
