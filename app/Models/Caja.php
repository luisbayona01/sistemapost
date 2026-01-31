<?php

namespace App\Models;

use App\Observers\CajaObserver;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy(CajaObserver::class)]
class Caja extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'fecha_hora_apertura' => 'datetime',
        'fecha_hora_cierre' => 'datetime',
        'saldo_inicial' => 'decimal:2',
        'saldo_final' => 'decimal:2',
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
        return $this->belongsTo(User::class);
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
     * Global scope: Filtrar cajas por empresa del usuario autenticado
     */
    protected static function booted(): void
    {
        static::addGlobalScope('empresa', function (Builder $query) {
            if (auth()->check() && auth()->user()->empresa_id) {
                $query->where('cajas.empresa_id', auth()->user()->empresa_id);
            }
        });
    }

    /**
     * Scope: Obtener cajas abiertas
     */
    public function scopeAbierta($query)
    {
        return $query->where('estado', 'abierta');
    }

    /**
     * Scope: Obtener cajas cerradas
     */
    public function scopeCerrada($query)
    {
        return $query->where('estado', 'cerrada');
    }

    /**
     * Scope: Obtener cajas por empresa
     */
    public function scopeForEmpresa($query, int $empresaId)
    {
        return $query->withoutGlobalScope('empresa')->where('empresa_id', $empresaId);
    }

    /**
     * Scope: Obtener cajas por usuario
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Obtener solo fecha de la apertura
     */
    public function getFechaAperturaAttribute(): string
    {
        return Carbon::parse($this->fecha_hora_apertura)->format('d-m-Y');
    }

    /**
     * Obtener solo hora de la apertura
     */
    public function getHoraAperturaAttribute(): string
    {
        return Carbon::parse($this->fecha_hora_apertura)->format('H:i');
    }

    /**
     * Obtener solo fecha del cierre
     */
    public function getFechaCierreAttribute(): string
    {
        return $this->fecha_hora_cierre
            ? Carbon::parse($this->fecha_hora_cierre)->format('d-m-Y')
            : '';
    }

    /**
     * Obtener solo hora del cierre
     */
    public function getHoraCierreAttribute(): string
    {
        return $this->fecha_hora_cierre
            ? Carbon::parse($this->fecha_hora_cierre)->format('H:i')
            : '';
    }

    /**
     * Calcular el saldo total de la caja
     * (saldo_inicial + movimientos)
     */
    public function calcularSaldo(): float
    {
        $ingresos = $this->movimientos()
            ->where('tipo', 'ingreso')
            ->sum('monto');

        $egresos = $this->movimientos()
            ->where('tipo', 'egreso')
            ->sum('monto');

        return ($this->saldo_inicial ?? 0) + $ingresos - $egresos;
    }

    /**
     * Cerrar la caja registrando el saldo final
     */
    public function cerrar(float $montoRecibido): self
    {
        $this->saldo_final = $montoRecibido;
        $this->estado = 'cerrada';
        $this->fecha_hora_cierre = Carbon::now();
        $this->save();

        return $this;
    }

    /**
     * Verificar si la caja está abierta
     */
    public function estaAbierta(): bool
    {
        return $this->estado === 'abierta';
    }

    /**
     * Verificar si la caja está cerrada
     */
    public function estaCerrada(): bool
    {
        return $this->estado === 'cerrada';
    }
}
