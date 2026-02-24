<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RequireTwoFactor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->isSuperAdmin() && !auth()->user()->isTwoFactorEnabled()) {
            // Si es Super Admin y no tiene 2FA configurado, redirigir al setup
            // Evitar loop infinito si ya está en la página de setup
            if (!$request->is('2fa/setup') && !$request->is('logout')) {
                return redirect()->route('2fa.setup');
            }
        }

        return $next($request);
    }
}
