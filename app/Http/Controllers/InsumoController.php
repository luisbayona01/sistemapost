<?php

namespace App\Http\Controllers;

use App\Models\Insumo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InsumoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $insumos = Insumo::where('empresa_id', Auth::user()->empresa_id)
            ->latest()
            ->paginate(10);
        return view('admin.insumos.index', compact('insumos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.insumos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'codigo' => 'nullable|string|max:50',
            'unidad_medida' => 'required|in:kg,g,l,ml,und',
            'costo_unitario' => 'required|numeric|min:0',
            'stock_minimo' => 'nullable|numeric|min:0',
        ]);

        $validated['empresa_id'] = Auth::user()->empresa_id;

        Insumo::create($validated);

        return redirect()->route('insumos.index')->with('success', 'Insumo creado correctamente');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Insumo $insumo)
    {
        return view('admin.insumos.edit', compact('insumo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Insumo $insumo)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'codigo' => 'nullable|string|max:50',
            'unidad_medida' => 'required|in:kg,g,l,ml,und',
            'costo_unitario' => 'required|numeric|min:0',
            'stock_minimo' => 'nullable|numeric|min:0',
        ]);

        $insumo->update($validated);

        return redirect()->route('insumos.index')->with('success', 'Insumo actualizado');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Insumo $insumo)
    {
        $insumo->delete();
        return redirect()->route('insumos.index')->with('success', 'Insumo eliminado');
    }
}
