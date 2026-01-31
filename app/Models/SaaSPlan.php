<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SaaSPlan extends Model
{
    protected $table = 'saas_plans';

    protected $fillable = [
        'nombre',
        'stripe_price_id',
        'precio_mensual_cop',
        'descripcion',
        'caracteristicas',
        'dias_trial',
        'activo',
    ];

    protected $casts = [
        'precio_mensual_cop' => 'decimal:2',
        'dias_trial' => 'integer',
        'activo' => 'boolean',
        'caracteristicas' => 'array',
    ];

    /**
     * Relación: Plan tiene múltiples empresas
     */
    public function empresas(): HasMany
    {
        return $this->hasMany(Empresa::class, 'plan_id');
    }

    /**
     * Scope: Planes activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Obtener el precio formateado
     */
    public function getPrecioFormateado(): string
    {
        return number_format($this->precio_mensual_cop, 2, ',', '.');
    }

    /**
     * Obtener características como array
     */
    public function getCaracteristicasArray(): array
    {
        return $this->caracteristicas ?? [];
    }
}
