<?php

namespace App\Http\Controllers;

use App\Models\Distribuidor;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class DistribuidorController extends Controller
{
    public function index(): View
    {
        $distribuidores = Distribuidor::latest()->paginate(15);
        return view('admin.distribuidores.index', compact('distribuidores'));
    }

    public function create(): View
    {
        return view('admin.distribuidores.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'contacto' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'notas' => 'nullable|string',
            'activo' => 'boolean'
        ]);

        Distribuidor::create($validated);

        return redirect()->route('distribuidores.index')
            ->with('success', 'Distribuidor creado exitosamente');
    }

    public function edit(Distribuidor $distribuidor): View
    {
        return view('admin.distribuidores.edit', compact('distribuidor'));
    }

    public function update(Request $request, Distribuidor $distribuidor): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'contacto' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'notas' => 'nullable|string',
            'activo' => 'boolean'
        ]);

        $distribuidor->update($validated);

        return redirect()->route('distribuidores.index')
            ->with('success', 'Distribuidor actualizado exitosamente');
    }

    public function destroy(Distribuidor $distribuidor): RedirectResponse
    {
        // Check if distribuidor has movies
        if ($distribuidor->productos()->count() > 0) {
            return redirect()->route('distribuidores.index')
                ->with('error', 'No se puede eliminar: tiene películas asociadas');
        }

        $distribuidor->delete();

        return redirect()->route('distribuidores.index')
            ->with('success', 'Distribuidor eliminado exitosamente');
    }
}
