<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Empresa;
use Illuminate\Support\Facades\Auth;

class TenantResolver
{
    /**
     * Resuelve el contexto del tenant basándose en Auth, Subdominio o Slug.
     */
    public function handle(Request $request, Closure $next)
    {
        $tenant = null;

        // 1. Prioridad: Usuario Autenticado
        if (Auth::check() && Auth::user()->empresa_id) {
            $tenant = Auth::user()->empresa;
            app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId(Auth::user()->empresa_id);
        }

        // 2. Si no hay auth (ruta pública), resolver por Host/Subdominio
        if (!$tenant) {
            $host = $request->getHost();
            // Buscar por dominio exacto
            $tenant = Empresa::where('dominio', $host)->first();

            // Si no, buscar por subdominio (slug)
            if (!$tenant) {
                $subdomain = explode('.', $host)[0];
                if (!in_array($subdomain, ['www', 'localhost', '127'])) {
                    $tenant = Empresa::where('slug', $subdomain)->first();
                }
            }
        }

        // 3. Compartir en el contenedor para HasEmpresaScope y otros
        if ($tenant) {
            app()->instance('currentTenant', $tenant);

            // Sincronizar con Spatie Teams
            app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($tenant->id);
        }

        return $next($request);
    }
}
