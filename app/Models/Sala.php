<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Traits\HasEmpresaScope;

class Sala extends Model
{
    use HasEmpresaScope;

    protected $fillable = ['empresa_id', 'nombre', 'configuracion_json', 'capacidad'];

    protected $casts = [
        'configuracion_json' => 'array',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function funciones(): HasMany
    {
        return $this->hasMany(Funcion::class);
    }
}
