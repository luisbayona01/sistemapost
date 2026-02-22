<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alerta;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AlertasController extends Controller
{
    /**
     * Centro de notificaciones
     */
    public function index(Request $request)
    {
        $empresaId = auth()->user()->empresa_id;
        $filtro = $request->get('filtro', 'activas'); // activas, todas, criticas

        $query = Alerta::where('empresa_id', $empresaId);

        if ($filtro === 'activas') {
            $query->noResueltas();
        } elseif ($filtro === 'criticas') {
            $query->criticas()->noResueltas();
        }

        $alertas = $query->orderBy('tipo', 'asc') // CRITICA primero (si enum ordenado alfabéticamente)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Marcar como vistas las mostradas
        if ($request->get('page', 1) == 1) {
            $ids = $alertas->pluck('id');
            Alerta::whereIn('id', $ids)->update(['vista' => true]);
        }

        return view('admin.alertas.index', compact('alertas', 'filtro'));
    }

    /**
     * Marcar una alerta como resuelta
     */
    public function resolver(Request $request, Alerta $alerta)
    {
        $this->authorize('update', $alerta); // O control de empresa_id manual

        if ($alerta->empresa_id !== auth()->user()->empresa_id) {
            abort(403);
        }

        $alerta->update([
            'resuelta' => true,
            'resuelta_at' => Carbon::now(),
            'resuelta_por' => auth()->id()
        ]);

        return back()->with('success', 'Alerta marcada como resuelta.');
    }

    /**
     * Marcar todas como vistas (para el botón del header)
     */
    public function marcarTodasVistas()
    {
        Alerta::where('empresa_id', auth()->user()->empresa_id)
            ->where('vista', false)
            ->update(['vista' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Obtener conteo de alertas no vistas (para ajax polling si se desea)
     */
    public function count()
    {
        $count = Alerta::where('empresa_id', auth()->user()->empresa_id)
            ->where('vista', false)
            ->where('resuelta', false)
            ->count();

        return response()->json(['count' => $count]);
    }
}
