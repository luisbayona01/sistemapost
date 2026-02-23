<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscriptionActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Si no está autenticado, dejar pasar (será redirigido por Authenticate)
        if (!$user) {
            return $next($request);
        }

        // Si es super-admin o Root, no tiene restricciones de suscripción
        if ($user->hasRole('super-admin') || $user->hasRole('Root')) {
            return $next($request);
        }

        // Si el usuario no tiene empresa, redirigir a login
        if ($user->empresa_id === null) {
            return redirect()->route('login.index')
                ->with('error', 'Usuario sin empresa asignada.');
        }

        // Obtener la empresa del usuario
        $empresa = $user->empresa;

        // Si la empresa no existe, redirigir a login
        if (!$empresa) {
            auth()->logout();
            return redirect()->route('login.index')
                ->with('error', 'Empresa no encontrada.');
        }

        // Verificar si la suscripción está activa
        if (!$empresa->hasActiveSuscription()) {
            auth()->logout();

            $mensaje = 'Su suscripción ha vencido o la empresa está suspendida.';
            if ($empresa->isSuspendida()) {
                $mensaje = 'Su empresa ha sido suspendida. Contacte con soporte.';
            } elseif ($empresa->isSubscriptionExpired()) {
                $mensaje = 'Su suscripción ha expirado. Por favor renuévela.';
            }

            return redirect()->route('login.index')
                ->with('error', $mensaje);
        }

        return $next($request);
    }
}
