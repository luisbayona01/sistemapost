<?php

namespace App\Traits;

use App\Scopes\HasEmpresaScope as ScopeClass;

trait HasEmpresaScope
{
    /**
     * Boot the trait and apply the global scope.
     */
    protected static function bootHasEmpresaScope()
    {
        static::addGlobalScope(new ScopeClass);

        // Auto-asignar empresa_id al crear registros
        static::creating(function ($model) {
            if (auth()->check() && empty($model->empresa_id)) {
                $model->empresa_id = auth()->user()->empresa_id;
            }
        });
    }

    /**
     * Scope helper para filtrar manualmente por empresa ignorando el scope global.
     */
    public function scopeForEmpresa($query, int $empresaId)
    {
        return $query->withoutGlobalScope(ScopeClass::class)->where('empresa_id', $empresaId);
    }
}
