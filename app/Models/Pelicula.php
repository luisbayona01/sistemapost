<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Traits\HasEmpresaScope;

class Pelicula extends Model
{
    use HasFactory, HasEmpresaScope;

    protected $guarded = ['id'];

    protected $casts = [
        'fecha_estreno' => 'date',
        'fecha_fin_exhibicion' => 'date',
        'activo' => 'boolean',
    ];

    /*
     * Relaciones
     */

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function distribuidor(): BelongsTo
    {
        return $this->belongsTo(Distribuidor::class);
    }

    public function funciones(): HasMany
    {
        return $this->hasMany(Funcion::class);
    }

    /*
     * Scopes
     */

    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    public function scopeEnCartelera($query)
    {
        $hoy = now()->toDateString();
        return $query->where('activo', true)
            ->where('fecha_estreno', '<=', $hoy)
            ->where(function ($q) use ($hoy) {
                $q->whereNull('fecha_fin_exhibicion')
                    ->orWhere('fecha_fin_exhibicion', '>=', $hoy);
            });
    }
}
