<?php

namespace App\Models;

use App\Observers\CajaObserver;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Traits\HasEmpresaScope;

#[ObservedBy(CajaObserver::class)]
class Caja extends Model
{
    use HasFactory, HasEmpresaScope;

    protected $fillable = [
        'empresa_id',
        'user_id',
        'fecha_apertura',
        'fecha_cierre',
        'monto_inicial',
        'monto_final_declarado',
        'monto_final_esperado',
        'diferencia',
        'estado',
        'cerrado_por',
        'notas_cierre',
        'efectivo_declarado',
        'tarjeta_declarado',
        'otros_declarado',
        'nombre', // Added back as it was in previous version
    ];

    protected $casts = [
        'fecha_apertura' => 'datetime',
        'fecha_cierre' => 'datetime',
        'monto_inicial' => 'decimal:2',
        'monto_final_declarado' => 'decimal:2',
        'monto_final_esperado' => 'decimal:2',
        'diferencia' => 'decimal:2',
        'efectivo_declarado' => 'decimal:2',
        'tarjeta_declarado' => 'decimal:2',
        'otros_declarado' => 'decimal:2',
    ];

    /**
     * Relación: Caja pertenece a una empresa
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * Relación: Caja fue abierta por un usuario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación: Caja fue cerrada por un usuario
     */
    public function cerradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cerrado_por');
    }

    /**
     * Relación: Caja contiene múltiples movimientos
     */
    public function movimientos(): HasMany
    {
        return $this->hasMany(Movimiento::class);
    }

    /**
     * Relación: Caja contiene múltiples ventas
     */
    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class);
    }

    /**
     * Scope: Obtener cajas abiertas
     */
    public function scopeAbierta($query)
    {
        return $query->where('estado', 'ABIERTA');
    }

    /**
     * Scope: Obtener cajas cerradas
     */
    public function scopeCerrada($query)
    {
        return $query->where('estado', 'CERRADA');
    }

    public function estaAbierta(): bool
    {
        return $this->estado === 'ABIERTA';
    }

    public function estaCerrada(): bool
    {
        return $this->estado === 'CERRADA';
    }

    /**
     * Calcula la diferencia entre lo declarado y lo esperado.
     */
    public function calcularDiferencia(float $declarado, float $esperado): float
    {
        return round($declarado - $esperado, 2);
    }
}
