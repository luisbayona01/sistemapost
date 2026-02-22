<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BusinessConfiguration;
use Symfony\Component\HttpFoundation\Response;

class CheckModuleEnabled
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $module): Response
    {
        // Obtener empresa del usuario autenticado
        $empresaId = auth()->user()?->empresa_id;

        if (!$empresaId) {
            abort(403, 'Usuario sin empresa asignada');
        }

        // Obtener o crear configuración por defecto
        $config = BusinessConfiguration::firstOrCreate(
            ['empresa_id' => $empresaId],
            [
                'business_type' => 'generic',
                'modules_enabled' => [
                    'cinema' => true,
                    'pos' => true,
                    'inventory' => true,
                    'reports' => true,
                    'api' => false,
                ],
            ]
        );

        // Verificar si el módulo está habilitado
        if (!$config->isModuleEnabled($module)) {
            abort(403, "El módulo '{$module}' no está habilitado para este negocio. Contacte al administrador.");
        }

        return $next($request);
    }
}
