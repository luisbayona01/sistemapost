<?php

namespace App\Http\Middleware;

use App\Models\Caja;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckCajaAperturadaUser
{
    /**
     * UX-FIRST: Si el usuario tiene caja abierta, validar que no lleve +24h.
     * Si no tiene caja, NO bloquear. El middleware EnsureCajaAbierta se encarga de auto-crear.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $empresa_id = auth()->user()->empresa_id;

        $cajaAbierta = Caja::where('user_id', Auth::id())
            ->where('empresa_id', $empresa_id)
            ->where('estado', 'ABIERTA')
            ->first();

        // Si no hay caja abierta → bloquear
        if (!$cajaAbierta) {
            return redirect()->route('admin.cajas.index')
                ->with('error', 'Debes abrir una caja registradora primero.');
        }

        // Validar tiempo de caja abierta (Hard-Lock 24h)
        $horasAbierta = $cajaAbierta->created_at->diffInHours(now());

        if ($horasAbierta > 24) {
            return redirect()->route('admin.cajas.index')
                ->with('error', "Tu caja lleva {$horasAbierta} horas abierta. Debes cerrarla antes de continuar.")
                ->with('caja_id', $cajaAbierta->id);
        }

        return $next($request);
    }
}
