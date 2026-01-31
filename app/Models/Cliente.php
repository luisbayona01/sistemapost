<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = ['persona_id', 'empresa_id'];

    /**
     * Relación: Cliente pertenece a una empresa
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * Relación: Cliente pertenece a una persona
     */
    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class);
    }

    /**
     * Relación: Cliente tiene múltiples ventas
     */
    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class);
    }

    /**
     * Global scope: Filtrar clientes por empresa del usuario autenticado
     */
    protected static function booted(): void
    {
        static::addGlobalScope('empresa', function (Builder $query) {
            if (auth()->check() && auth()->user()->empresa_id) {
                $query->where('clientes.empresa_id', auth()->user()->empresa_id);
            }
        });
    }

    /**
     * Scope: Obtener clientes por empresa
     */
    public function scopeForEmpresa($query, int $empresaId)
    {
        return $query->withoutGlobalScope('empresa')->where('empresa_id', $empresaId);
    }

    /**
     * Scope: Buscar clientes por nombre
     */
    public function scopeSearch($query, string $term)
    {
        return $query->whereHas('persona', function ($subquery) use ($term) {
            $subquery->where('razon_social', 'like', "%{$term}%");
        });
    }

    /**
     * Obtener la razón social, tipo y número de documento del cliente
     */
    public function getNombreDocumentoAttribute(): string
    {
        return $this->persona->razon_social . ' - ' . $this->persona->documento->nombre . ': ' . $this->persona->numero_documento;
    }

    /**
     * Obtener el nombre completo del cliente
     */
    public function getNombreCompletoAttribute(): string
    {
        return $this->persona->razon_social;
    }

    /**
     * Obtener el número de documento del cliente
     */
    public function getNumeroDocumentoAttribute(): string
    {
        return $this->persona->numero_documento;
    }
}
