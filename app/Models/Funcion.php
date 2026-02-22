<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Traits\HasEmpresaScope;

class Funcion extends Model
{
    use HasEmpresaScope;

    protected $table = 'funciones';

    protected $fillable = ['empresa_id', 'sala_id', 'pelicula_id', 'fecha_hora', 'precio', 'activo'];

    protected $casts = [
        'fecha_hora' => 'datetime',
        'precio' => 'decimal:2',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function sala(): BelongsTo
    {
        return $this->belongsTo(Sala::class);
    }

    public function pelicula(): BelongsTo
    {
        return $this->belongsTo(Pelicula::class);
    }

    public function asientos(): HasMany
    {
        return $this->hasMany(FuncionAsiento::class);
    }

    public function precios()
    {
        return $this->belongsToMany(PrecioEntrada::class, 'funcion_precio');
    }
}
