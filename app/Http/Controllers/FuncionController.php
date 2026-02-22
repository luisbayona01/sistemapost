<?php

namespace App\Http\Controllers;

use App\Models\Funcion;
use App\Models\Sala;
use App\Models\Pelicula;
use App\Models\PrecioEntrada;
use App\Models\FuncionAsiento;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;

class FuncionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:ver-producto|crear-producto|editar-producto|eliminar-producto');
    }

    public function index(): View
    {
        $funciones = Funcion::with(['sala', 'pelicula'])
            ->orderBy('fecha_hora', 'desc')
            ->paginate(20);

        return view('admin.funciones.index', compact('funciones'));
    }

    public function create(): View
    {
        $salas = Sala::all();
        $peliculas = Pelicula::where('activo', true)->get(); // Solo productos que son películas
        $precios = PrecioEntrada::where('activo', true)->get();

        return view('admin.funciones.create', compact('salas', 'peliculas', 'precios'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'sala_id' => 'required|exists:salas,id',
            'pelicula_id' => 'required|exists:peliculas,id',
            'fecha' => 'required|date',
            'hora' => 'required',
            'precio' => 'required|numeric|min:0',
            'precios_entrada' => 'nullable|array'
        ]);

        $fechaHora = Carbon::parse($validated['fecha'] . ' ' . $validated['hora']);

        if ($fechaHora->isPast()) {
            return redirect()->back()->withInput()->with('error', "La fecha y hora deben ser en el futuro.");
        }

        $pelicula = Pelicula::find($validated['pelicula_id']);
        $duracion = (int) filter_var($pelicula->duracion, FILTER_SANITIZE_NUMBER_INT) ?: 120;

        $conflicto = $this->checkScheduleConflict($validated['sala_id'], $fechaHora, $duracion);
        if ($conflicto) {
            $tituloConflicto = $conflicto->pelicula ? $conflicto->pelicula->titulo : 'una película';
            return redirect()->back()->withInput()->with('error', "¡Conflicto de horario! La Sala ya está ocupada por '{$tituloConflicto}' en ese horario.");
        }

        $funcion = Funcion::create([
            'sala_id' => $validated['sala_id'],
            'pelicula_id' => $validated['pelicula_id'],
            'fecha_hora' => $fechaHora,
            'precio' => $validated['precio'],
            'empresa_id' => auth()->user()->empresa_id,
            'activo' => true
        ]);

        if (!empty($validated['precios_entrada'])) {
            $funcion->precios()->attach($validated['precios_entrada']);
        }

        $this->generateSeats($funcion);

        return redirect()->route('funciones.index')->with('success', 'Función creada exitosamente');
    }

    public function bulkCreate(): View
    {
        $salas = Sala::all();
        $peliculas = Pelicula::where('activo', true)->get();
        $precios = PrecioEntrada::where('activo', true)->get();

        return view('admin.funciones.bulk-create', compact('salas', 'peliculas', 'precios'));
    }

    public function bulkStore(Request $request): RedirectResponse
    {
        $request->validate([
            'pelicula_id' => 'required|exists:peliculas,id',
            'sala_id' => 'required|exists:salas,id',
            'fecha_desde' => 'required|date',
            'fecha_hasta' => 'required|date|after_or_equal:fecha_desde',
            'dias' => 'required|array|min:1',
            'horarios' => 'required|array|min:1',
            'precio' => 'required|numeric|min:0',
            'precios_entrada' => 'nullable|array'
        ]);

        $empresaId = auth()->user()->empresa_id;
        $creadas = 0;
        $duplicadas = 0;
        $errors = [];

        // Filtrar horarios vacíos
        $horarios = array_filter($request->horarios ?? []);

        if (empty($horarios)) {
            return back()->with('error', 'Debes agregar al menos un horario');
        }

        $fechaActual = Carbon::parse($request->fecha_desde);
        $fechaFin = Carbon::parse($request->fecha_hasta);

        while ($fechaActual <= $fechaFin) {
            // Verificar si el día de la semana está seleccionado
            if (in_array((string) $fechaActual->dayOfWeekIso, $request->dias)) {

                foreach ($horarios as $hora) {
                    $fechaHora = Carbon::parse($fechaActual->toDateString() . ' ' . $hora);

                    if ($fechaHora->isFuture() || $fechaHora->isToday()) {

                        // Evitar duplicados EXACTOS (Misma sala, misma hora)
                        $existe = Funcion::where('empresa_id', $empresaId)
                            ->where('sala_id', $request->sala_id)
                            ->where('fecha_hora', $fechaHora)
                            ->exists();

                        if ($existe) {
                            $duplicadas++;
                            continue;
                        }

                        $funcion = Funcion::create([
                            'empresa_id' => $empresaId,
                            'pelicula_id' => $request->pelicula_id,
                            'sala_id' => $request->sala_id,
                            'fecha_hora' => $fechaHora,
                            'precio' => $request->precio,
                            'activo' => true,
                        ]);

                        if (!empty($request->precios_entrada)) {
                            $funcion->precios()->attach($request->precios_entrada);
                        }

                        // Generar asientos usando lógica existente (pero asegurando config)
                        $sala = Sala::find($request->sala_id);
                        if ($sala && !empty($sala->configuracion_json)) {
                            $mapa = is_string($sala->configuracion_json) ? json_decode($sala->configuracion_json, true) : $sala->configuracion_json;
                            foreach ($mapa as $seat) {
                                if (isset($seat['tipo']) && $seat['tipo'] === 'asiento') {
                                    FuncionAsiento::create([
                                        'funcion_id' => $funcion->id,
                                        'codigo_asiento' => $seat['fila'] . $seat['num'],
                                        'estado' => 'DISPONIBLE'
                                    ]);
                                }
                            }
                        }

                        $creadas++;
                    }
                }
            }
            $fechaActual->addDay();
        }

        $msg = "✅ {$creadas} función(es) creada(s).";
        if ($duplicadas > 0) {
            $msg .= " ⚠️ {$duplicadas} omitida(s) por duplicado exacto.";
        }

        return redirect()->route('funciones.index')->with('success', $msg);
    }

    private function checkScheduleConflict($salaId, $fechaHora, $duracionMinutos, $ignoreId = null)
    {
        // MÁS PERMISIVO: Solo bloquea si hay una función en exactamente el mismo bloque de tiempo (con margen pequeño)
        $inicio = Carbon::parse($fechaHora);

        $query = Funcion::where('sala_id', $salaId)
            ->whereBetween('fecha_hora', [
                (clone $inicio)->subMinutes(30),
                (clone $inicio)->addMinutes(30),
            ])
            ->with(['pelicula']);

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        return $query->first();
    }

    private function generateSeats(Funcion $funcion)
    {
        $sala = Sala::find($funcion->sala_id);
        $mapa = $sala->configuracion_json;
        foreach ($mapa as $seat) {
            if ($seat['tipo'] === 'asiento') {
                FuncionAsiento::create([
                    'funcion_id' => $funcion->id,
                    'codigo_asiento' => $seat['fila'] . $seat['num'],
                    'estado' => FuncionAsiento::ESTADO_DISPONIBLE
                ]);
            }
        }
    }


    public function edit(Funcion $funcione): View
    {
        $salas = Sala::all();
        $peliculas = Pelicula::where('activo', true)->get();
        $precios = PrecioEntrada::where('activo', true)->get();

        // Check if has sales
        $tienteVentas = FuncionAsiento::where('funcion_id', $funcione->id)
            ->where('estado', FuncionAsiento::ESTADO_VENDIDO)
            ->exists();

        return view('admin.funciones.edit', compact('funcione', 'salas', 'peliculas', 'precios', 'tienteVentas'));
    }

    public function update(Request $request, Funcion $funcione): RedirectResponse
    {
        $ventasCount = FuncionAsiento::where('funcion_id', $funcione->id)
            ->where('estado', FuncionAsiento::ESTADO_VENDIDO)
            ->count();

        $validated = $request->validate([
            'sala_id' => 'required|exists:salas,id',
            'pelicula_id' => 'required|exists:peliculas,id',
            'fecha_hora' => 'required|date',
            'precio' => 'required|numeric|min:0',
        ]);

        if ($ventasCount > 0 && !$request->has('force_update')) {
            return redirect()->back()
                ->with('warning', "Esta función tiene {$ventasCount} asientos vendidos. ¿Está seguro de modificarla?")
                ->with('show_confirm', true);
        }

        // Si cambia sala o fecha/hora, validar conflicto
        if ($funcione->sala_id != $validated['sala_id'] || $funcione->fecha_hora != $validated['fecha_hora']) {
            $pelicula = Pelicula::find($validated['pelicula_id']);
            $duracion = (int) filter_var($pelicula->duracion, FILTER_SANITIZE_NUMBER_INT) ?: 120;

            // Ignorar la función actual en la búsqueda de conflictos
            $conflicto = $this->checkScheduleConflict($validated['sala_id'], Carbon::parse($validated['fecha_hora']), $duracion, $funcione->id);

            if ($conflicto) {
                $tituloConflicto = $conflicto->pelicula ? $conflicto->pelicula->titulo : 'una película';
                return redirect()->back()->withInput()->with('error', "¡Conflicto de horario! La Sala ya está ocupada por '{$tituloConflicto}' en ese horario.");
            }
        }

        // Si cambia la sala, regenerar asientos (solo si no hay ventas o si se forza)
        $salaCambio = $funcione->sala_id != $validated['sala_id'];

        $funcione->update($validated);

        if ($salaCambio) {
            // Eliminar asientos viejos que NO estén vendidos (si se forza y hay vendidos, los vendidos quedan huérfanos pero los nuevos aparecerán)
            // Lo ideal es que si hay vendidos NO se cambie la sala, pero si se forza:
            FuncionAsiento::where('funcion_id', $funcione->id)->where('estado', '!=', FuncionAsiento::ESTADO_VENDIDO)->delete();
            $this->generateSeats($funcione);
        }

        return redirect()->route('funciones.index')
            ->with('success', 'Función actualizada exitosamente');
    }

    public function destroy(Request $request, Funcion $funcione): RedirectResponse
    {
        // Check if has sales
        $ventasCount = FuncionAsiento::where('funcion_id', $funcione->id)
            ->where('estado', FuncionAsiento::ESTADO_VENDIDO)
            ->count();

        if ($ventasCount > 0 && !$request->has('confirm_delete')) {
            return redirect()->route('funciones.index')
                ->with('warning', "Esta función tiene {$ventasCount} asientos vendidos. Si la elimina, perderá el registro de esas ventas en esta función. ¿Desea continuar?")
                ->with('confirm_delete_id', $funcione->id);
        }

        $funcione->delete();

        return redirect()->route('funciones.index')
            ->with('success', 'Función eliminada exitosamente');
    }

    public function toggleActivo(Funcion $funcion): RedirectResponse
    {
        $funcion->update(['activo' => !$funcion->activo]);
        return redirect()->back()->with('success', 'Estado de la función actualizado');
    }
}
