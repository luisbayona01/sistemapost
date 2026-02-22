<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Caja;

class EnsureCajaAbierta
{
    /**
     * UX-FIRST: Auto-apertura silenciosa de caja.
     * Si el usuario no tiene una caja ABIERTA hoy, se crea automáticamente.
     * El cajero NUNCA ve un formulario de apertura. Solo vende.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('login.index');
        }

        $userId = auth()->id();
        $empresaId = auth()->user()->empresa_id;

        // Buscar caja abierta del usuario
        $cajaAbierta = Caja::where('user_id', $userId)
            ->where('empresa_id', $empresaId)
            ->where('estado', 'ABIERTA')
            ->first();

        // Si no hay caja abierta → crear una automáticamente
        if (!$cajaAbierta) {
            $baseInicial = config('caja.base_inicial_default', 0);

            $cajaAbierta = Caja::create([
                'empresa_id' => $empresaId,
                'user_id' => $userId,
                'fecha_apertura' => now(),
                'monto_inicial' => $baseInicial,
                'estado' => 'ABIERTA',
                'nombre' => 'Caja ' . auth()->user()->name . ' - ' . now()->format('d/m'),
            ]);

            \Log::info("Caja auto-abierta para usuario {$userId}", [
                'caja_id' => $cajaAbierta->id,
                'base_inicial' => $baseInicial,
            ]);
        }

        // Inyectar la caja activa al request para uso posterior
        $request->merge(['caja_activa' => $cajaAbierta]);

        return $next($request);
    }
}
