<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware de Seguridad: Previene inyección de empresa_id
 * 
 * Valida que cualquier empresa_id enviado en el request coincida
 * con la empresa del usuario autenticado. Esto previene que un
 * usuario malicioso intente acceder o modificar datos de otra empresa.
 * 
 * @package App\Http\Middleware
 * @since FASE 4.1
 */
class EnsureUserBelongsToEmpresa
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Solo aplicar si el usuario está autenticado
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        $userEmpresaId = $user->empresa_id;

        // Verificar empresa_id en diferentes ubicaciones del request
        $requestEmpresaId = $this->getEmpresaIdFromRequest($request);

        if ($requestEmpresaId && $requestEmpresaId != $userEmpresaId) {
            // Log del intento de acceso no autorizado
            Log::warning('Intento de inyección de empresa_id bloqueado', [
                'user_id' => $user->id,
                'user_empresa_id' => $userEmpresaId,
                'request_empresa_id' => $requestEmpresaId,
                'ip' => $request->ip(),
                'route' => $request->path(),
                'method' => $request->method(),
            ]);

            abort(403, 'No tienes permiso para acceder a recursos de otra empresa.');
        }

        return $next($request);
    }

    /**
     * Extraer empresa_id de diferentes ubicaciones del request
     *
     * @param Request $request
     * @return int|null
     */
    private function getEmpresaIdFromRequest(Request $request): ?int
    {
        // Verificar en el body (POST/PUT)
        if ($request->filled('empresa_id')) {
            return (int) $request->input('empresa_id');
        }

        // Verificar en query string (GET)
        if ($request->query('empresa_id')) {
            return (int) $request->query('empresa_id');
        }

        // Verificar en route parameters
        if ($request->route('empresa_id')) {
            return (int) $request->route('empresa_id');
        }

        return null;
    }
}
