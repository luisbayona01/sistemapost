<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alerta extends Model
{
    protected $fillable = [
        'empresa_id',
        'tipo',
        'categoria',
        'titulo',
        'mensaje',
        'datos',
        'vista',
        'resuelta',
        'resuelta_at',
        'resuelta_por',
    ];

    protected $casts = [
        'datos' => 'array',
        'vista' => 'boolean',
        'resuelta' => 'boolean',
        'resuelta_at' => 'datetime',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function resueltaPor()
    {
        return $this->belongsTo(User::class, 'resuelta_por');
    }

    // Scopes
    public function scopeNoVistas($query)
    {
        return $query->where('vista', false);
    }

    public function scopeNoResueltas($query)
    {
        return $query->where('resuelta', false);
    }

    public function scopeCriticas($query)
    {
        return $query->where('tipo', 'CRITICA');
    }
}
