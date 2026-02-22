<?php

namespace App\Http\Controllers;

use App\Models\Pelicula;
use App\Models\Distribuidor;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class PeliculaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Reutilizamos permisos de productos por ahora para facilitar la transición
        $this->middleware('permission:ver-producto|crear-producto|editar-producto|eliminar-producto');
    }

    public function index(): View
    {
        $peliculas = Pelicula::with('distribuidor')->latest()->paginate(10);
        return view('admin.peliculas.index', compact('peliculas'));
    }

    public function create(): View
    {
        $distribuidores = Distribuidor::all();
        return view('admin.peliculas.create', compact('distribuidores'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'sinopsis' => 'nullable|string',
            'duracion' => 'nullable|string|max:20', // Ej: "120 min" o "2h"
            'clasificacion' => 'nullable|string|max:10',
            'genero' => 'nullable|string|max:50',
            'distribuidor_id' => 'nullable|exists:distribuidores,id',
            'trailer_url' => 'nullable|url',
            'fecha_estreno' => 'nullable|date',
            'fecha_fin_exhibicion' => 'nullable|date|after_or_equal:fecha_estreno',
            'afiche' => 'nullable|image|max:2048' // 2MB max
        ]);

        $validated['empresa_id'] = auth()->user()->empresa_id;
        $validated['activo'] = true;

        if ($request->hasFile('afiche')) {
            $path = $request->file('afiche')->store('peliculas', 'public');
            $validated['afiche'] = $path;
        }

        Pelicula::create($validated);

        return redirect()->route('peliculas.index')->with('success', 'Película registrada exitosamente.');
    }

    public function edit(Pelicula $pelicula): View
    {
        $distribuidores = Distribuidor::all();
        return view('admin.peliculas.edit', compact('pelicula', 'distribuidores'));
    }

    public function update(Request $request, Pelicula $pelicula): RedirectResponse
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'sinopsis' => 'nullable|string',
            'duracion' => 'nullable|string|max:20',
            'clasificacion' => 'nullable|string|max:10',
            'genero' => 'nullable|string|max:50',
            'distribuidor_id' => 'nullable|exists:distribuidores,id',
            'trailer_url' => 'nullable|url',
            'fecha_estreno' => 'nullable|date',
            'fecha_fin_exhibicion' => 'nullable|date|after_or_equal:fecha_estreno',
            'afiche' => 'nullable|image|max:2048',
            'activo' => 'boolean'
        ]);

        if ($request->hasFile('afiche')) {
            // Eliminar anterior si existe
            if ($pelicula->afiche) {
                Storage::disk('public')->delete($pelicula->afiche);
            }
            $path = $request->file('afiche')->store('peliculas', 'public');
            $validated['afiche'] = $path;
        }

        $pelicula->update($validated);

        return redirect()->route('peliculas.index')->with('success', 'Película actualizada correctamente.');
    }

    public function destroy(Pelicula $pelicula): RedirectResponse
    {
        // Verificar si tiene funciones asociadas
        if ($pelicula->funciones()->exists()) {
            return redirect()->back()->with('error', 'No se puede eliminar la película porque tiene funciones programadas.');
        }

        if ($pelicula->afiche) {
            Storage::disk('public')->delete($pelicula->afiche);
        }

        $pelicula->delete();

        return redirect()->route('peliculas.index')->with('success', 'Película eliminada.');
    }
}
