<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Verificar si el usuario está autenticado
        if (!$user) {
            return redirect()->route('login.index')
                ->with('error', 'Debe iniciar sesión para acceder a esta sección.');
        }

        // Verificar si el usuario tiene el rol super-admin
        if (!$user->hasRole('super-admin')) {
            abort(403, 'No tiene permisos para acceder a esta sección.');
        }

        // Verificar que el usuario no tenga empresa asignada
        if ($user->empresa_id !== null) {
            abort(403, 'Super admin no puede estar asignado a una empresa.');
        }

        return $next($request);
    }
}
