<?php

namespace App\Models;

use App\Scopes\HasEmpresaScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeatureFlag extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'enabled' => 'boolean',
        'metadata' => 'array',
        'enabled_at' => 'datetime',
    ];

    protected static function booted()
    {
        // En Laravel 12+ usualmente usamos addGlobalScope en el booted del modelo
        // pero como ya tenemos el Trait HasEmpresaScope que lo hace en su boot, 
        // solo necesitamos usar el trait si decidimos estandarizarlo.
        // El USER pidió explícitamente:
        // static::addGlobalScope(new HasEmpresaScope());
        // Sin embargo, nuestro HasEmpresaScope es una clase Scope (implements Scope).

        static::addGlobalScope(new \App\Scopes\HasEmpresaScope());
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }
}
