<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\GastoOperacional;
use Illuminate\Http\Request;

class GastoOperacionalController extends Controller
{
    public function index()
    {
        $gastos = GastoOperacional::where('empresa_id', auth()->user()->empresa_id)
            ->orderBy('periodo', 'desc')
            ->get();

        return view('admin.gastos.index', compact('gastos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'monto' => 'required|numeric|min:0',
            'periodo' => 'required|string|regex:/^\d{4}-\d{2}$/',
            'fecha_pago' => 'nullable|date',
            'observaciones' => 'nullable|string',
        ]);

        GastoOperacional::create(array_merge($validated, [
            'empresa_id' => auth()->user()->empresa_id
        ]));

        return redirect()->back()->with('success', 'Gasto operacional registrado correctamente.');
    }

    public function destroy(GastoOperacional $gasto)
    {
        $gasto->delete();
        return redirect()->back()->with('success', 'Gasto eliminado.');
    }
}
