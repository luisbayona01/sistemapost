<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tarifa;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TarifaController extends Controller
{
    public function index()
    {
        $tarifas = Tarifa::where('empresa_id', auth()->user()->empresa_id)
            ->orderBy('precio')->get();

        return view('admin.tarifas.index', compact('tarifas'));
    }

    public function crear()
    {
        return view('admin.tarifas.crear');
    }

    public function guardar(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'precio' => 'required|numeric|min:0',
        ]);

        Tarifa::create([
            'empresa_id' => auth()->user()->empresa_id,
            'nombre' => $request->nombre,
            'precio' => $request->precio,
            'dias_semana' => $request->dias_semana ?? [],
            'aplica_festivos' => $request->boolean('aplica_festivos'),
            'es_default' => false,
            'activa' => true,
            'color' => $request->color ?? '#3B82F6',
        ]);

        return redirect()->route('admin.tarifas.index')
            ->with('success', 'Tarifa creada correctamente');
    }

    public function editar($id)
    {
        $tarifa = Tarifa::where('empresa_id', auth()->user()->empresa_id)
            ->findOrFail($id);
        return view('admin.tarifas.editar', compact('tarifa'));
    }

    public function actualizar(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'precio' => 'required|numeric|min:0',
        ]);

        $tarifa = Tarifa::where('empresa_id', auth()->user()->empresa_id)
            ->findOrFail($id);

        $tarifa->update([
            'nombre' => $request->nombre,
            'precio' => $request->precio,
            'dias_semana' => $request->dias_semana ?? [],
            'aplica_festivos' => $request->boolean('aplica_festivos'),
            'color' => $request->color ?? $tarifa->color,
        ]);

        return redirect()->route('admin.tarifas.index')
            ->with('success', 'Tarifa actualizada');
    }

    public function obtenerParaFecha(Request $request)
    {
        $fecha = Carbon::parse($request->fecha);
        $tarifa = Tarifa::obtenerParaFecha($fecha, auth()->user()->empresa_id);

        return response()->json([
            'tarifa' => $tarifa,
            'es_festivo' => Tarifa::esFestivoColombia($fecha),
            'dia_semana' => $fecha->locale('es')->dayName,
        ]);
    }
}
