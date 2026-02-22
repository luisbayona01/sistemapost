<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Traits\HasEmpresaScope;

class Proveedore extends Model
{
    use HasFactory, HasEmpresaScope;

    protected $fillable = ['persona_id', 'empresa_id'];

    /**
     * Relación: Proveedor pertenece a una empresa
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * Relación: Proveedor pertenece a una persona
     */
    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class);
    }

    /**
     * Relación: Proveedor tiene múltiples compras
     */
    public function compras(): HasMany
    {
        return $this->hasMany(Compra::class);
    }

    /**
     * Scope: Obtener proveedores por empresa
     */
    public function scopeForEmpresa($query, int $empresaId)
    {
        return $query->withoutGlobalScope('empresa')->where('empresa_id', $empresaId);
    }

    /**
     * Scope: Buscar proveedores por nombre
     */
    public function scopeSearch($query, string $term)
    {
        return $query->whereHas('persona', function ($subquery) use ($term) {
            $subquery->where('razon_social', 'like', "%{$term}%");
        });
    }

    /**
     * Obtener la razón social, tipo y número de documento del proveedor
     */
    public function getNombreDocumentoAttribute(): string
    {
        return $this->persona->razon_social . ' - ' . $this->persona->documento->nombre . ': ' . $this->persona->numero_documento;
    }

    /**
     * Obtener el nombre completo del proveedor
     */
    public function getNombreCompletoAttribute(): string
    {
        return $this->persona->razon_social;
    }
}
