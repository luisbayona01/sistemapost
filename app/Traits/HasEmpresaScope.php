<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasEmpresaScope
{
    protected static function bootHasEmpresaScope()
    {
        static::addGlobalScope('empresa', function (Builder $query) {
            if (auth()->check()) {
                if (!auth()->user()->hasRole('Root')) {
                    $query->where($query->getModel()->qualifyColumn('empresa_id'), auth()->user()->empresa_id);
                }
            } elseif (!app()->runningInConsole()) {
                // abort(403, 'Acceso no autorizado a datos de empresa.');
            }
        });

        static::creating(function ($model) {
            if (auth()->check() && !$model->empresa_id) {
                $model->empresa_id = auth()->user()->empresa_id;
            }
        });
    }

    public function scopeForEmpresa($query, int $empresaId)
    {
        return $query->withoutGlobalScope('empresa')->where('empresa_id', $empresaId);
    }
}
