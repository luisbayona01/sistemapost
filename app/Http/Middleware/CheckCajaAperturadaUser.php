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
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $empresa_id = auth()->user()->empresa_id;

        $existe = Caja::where('user_id', Auth::id())
            ->where('empresa_id', $empresa_id)
            ->where('estado', 1)
            ->exists();

        if (!$existe) {
            return redirect()->route('cajas.index')
                ->with('error', 'Debe aperturar una caja en esta empresa');
        }

        return $next($request);
    }
}
