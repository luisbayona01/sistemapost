<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class SetTenantTeamId
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->empresa_id) {
            app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId(Auth::user()->empresa_id);
        }

        return $next($request);
    }
}
