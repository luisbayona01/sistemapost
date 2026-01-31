<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CheckShowVentaUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $venta = $request->route('venta');
        $empresa_id = auth()->user()->empresa_id;

        if ($venta->user_id != Auth::id() || $venta->empresa_id != $empresa_id) {
            throw new HttpException(403, 'No tienes permiso para ver esta venta');
        }

        return $next($request);
    }
}
