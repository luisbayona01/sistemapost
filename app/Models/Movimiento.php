<?php

namespace App\Models;

use App\Enums\MetodoPagoEnum;
use App\Enums\TipoMovimientoEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Movimiento extends Model
{
    // Tipos de Movimiento
    public const TIPO_VENTA = 'VENTA';
    public const TIPO_RETIRO = 'RETIRO';
    public const TIPO_INGRESO = 'INGRESO';
    public const TIPO_EGRESO = 'EGRESO';
    public const TIPO_CORTESIA = 'CORTESIA';
    public const TIPO_BAJA = 'BAJA';

    // Métodos de Pago
    public const METODO_EFECTIVO = 'EFECTIVO';
    public const METODO_TARJETA = 'TARJETA';
    public const METODO_TRANSFERENCIA = 'TRANSFERENCIA';
    public const METODO_QR = 'QR';
    public const METODO_MIXTO = 'MIXTO';
    public const METODO_STRIPE = 'STRIPE';

    protected $guarded = ['id'];

    protected $casts = [
        'tipo' => TipoMovimientoEnum::class,
        'metodo_pago' => MetodoPagoEnum::class,
        'monto' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación: Movimiento pertenece a una empresa
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * Relación: Movimiento pertenece a una caja
     */
    public function caja(): BelongsTo
    {
        return $this->belongsTo(Caja::class);
    }

    /**
     * Relación: Movimiento pertenece a una venta (opcional)
     */
    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class);
    }

    /**
     * Global scope: Filtrar movimientos por empresa del usuario autenticado
     */
    protected static function booted(): void
    {
        static::addGlobalScope('empresa', function (Builder $query) {
            if (auth()->check()) {
                $query->where($query->getModel()->qualifyColumn('empresa_id'), auth()->user()->empresa_id);
            } elseif (!app()->runningInConsole()) {
                abort(403, 'Acceso no autorizado a datos de empresa.');
            }
        });
    }

    /**
     * Scope: Obtener movimientos por tipo
     */
    public function scopeTipo($query, TipoMovimientoEnum $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    /**
     * Scope: Obtener movimientos de ingreso
     */
    public function scopeIngresos($query)
    {
        return $query->where('tipo', TipoMovimientoEnum::INGRESO);
    }

    /**
     * Scope: Obtener movimientos de egreso
     */
    public function scopeEgresos($query)
    {
        return $query->where('tipo', TipoMovimientoEnum::EGRESO);
    }

    /**
     * Scope: Obtener movimientos en un período
     */
    public function scopeEnPeriodo($query, Carbon $inicio, Carbon $fin)
    {
        return $query->whereBetween('created_at', [$inicio, $fin]);
    }

    /**
     * Scope: Obtener movimientos por método de pago
     */
    public function scopeByMetodoPago($query, MetodoPagoEnum $metodo)
    {
        return $query->where('metodo_pago', $metodo);
    }

    /**
     * Scope: Obtener movimientos por empresa
     */
    public function scopeForEmpresa($query, int $empresaId)
    {
        return $query->withoutGlobalScope('empresa')->where('empresa_id', $empresaId);
    }

    /**
     * Scope: Obtener movimientos por caja
     */
    public function scopeByCaja($query, int $cajaId)
    {
        return $query->where('caja_id', $cajaId);
    }

    /**
     * Scope: Obtener movimientos de una venta
     */
    public function scopeFromVenta($query, int $ventaId)
    {
        return $query->where('venta_id', $ventaId)->whereNotNull('venta_id');
    }

    /**
     * Verificar si el movimiento es un ingreso
     */
    public function esIngreso(): bool
    {
        return $this->tipo === TipoMovimientoEnum::INGRESO;
    }

    /**
     * Verificar si el movimiento es un egreso
     */
    public function esEgreso(): bool
    {
        return $this->tipo === TipoMovimientoEnum::EGRESO;
    }
}
