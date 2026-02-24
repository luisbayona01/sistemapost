<?php

namespace App\Services;

use App\Models\FeatureFlag;
use Illuminate\Support\Facades\Cache;

class FeatureService
{
    /**
     * Verifica si una funcionalidad está activa para el tenant actual.
     */
    public function enabled(string $key, $default = false): bool
    {
        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;
        if (!$tenantId)
            return $default;

        return Cache::remember("feature_{$tenantId}_{$key}", 3600, function () use ($tenantId, $key, $default) {
            // Usamos withoutGlobalScopes para asegurar que consultamos el registro correcto
            // aunque el modelo mismo ya tiene el scope activo. 
            // Pero por seguridad en el cache, filtramos manualmente.
            return FeatureFlag::where('empresa_id', $tenantId)
                ->where('key', $key)
                ->value('enabled') ?? $default;
        });
    }

    /**
     * Activa o desactiva una funcionalidad para el tenant actual.
     */
    public function set(string $key, bool $enabled, array $metadata = [])
    {
        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;
        if (!$tenantId)
            return;

        FeatureFlag::updateOrCreate(
            ['empresa_id' => $tenantId, 'key' => $key],
            [
                'enabled' => $enabled,
                'metadata' => $metadata,
                'enabled_at' => $enabled ? now() : null
            ]
        );

        Cache::forget("feature_{$tenantId}_{$key}");
    }
}
