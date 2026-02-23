<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

use App\Traits\HasEmpresaScope;

class Distribuidor extends Model
{
    use HasFactory, HasEmpresaScope;

    protected $table = 'distribuidores';

    protected $fillable = [
        'empresa_id',
        'nombre',
        'contacto',
        'telefono',
        'email',
        'notas',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    /**
     * Relationship: Distribuidor belongs to Empresa
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * Relationship: Distribuidor has many Productos (movies)
     */
    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class);
    }
}
