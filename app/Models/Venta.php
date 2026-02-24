<?php

namespace App\Models;

use App\Observers\VentaObserver;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

use App\Traits\HasEmpresaScope;
use App\Traits\HasAuditForge;

#[ObservedBy(VentaObserver::class)]
class Venta extends Model
{
    use HasFactory, HasEmpresaScope, HasAuditForge;

    protected $guarded = ['id'];

    protected $casts = [
        'fecha_hora' => 'datetime',
        'fecha_operativa' => 'date',
        'subtotal' => 'decimal:2',
        'subtotal_cine' => 'decimal:2',
        'subtotal_confiteria' => 'decimal:2',
        'impuesto' => 'decimal:2',
        'inc_total' => 'decimal:2',
        'total' => 'decimal:2',
        'total_final' => 'decimal:2',
        'monto_recibido' => 'decimal:2',
        'vuelto_entregado' => 'decimal:2',
        'tarifa_servicio' => 'decimal:2',
        'monto_tarifa' => 'decimal:2',
        'cambio' => 'decimal:2',
        'estado_pago' => 'string', // PENDIENTE, PAGADA, FALLIDA, CANCELADA
        'tipo_venta' => 'string', // FISICA, WEB
        'origen' => 'string', // POS, WEB
    ];

    /**
     * Relación: Venta pertenece a una empresa
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * Relación: Venta pertenece a una caja
     */
    public function caja(): BelongsTo
    {
        return $this->belongsTo(Caja::class);
    }

    /**
     * Relación: Venta pertenece a un cliente
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    /**
     * Relación: Venta fue registrada por un usuario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación: Venta tiene un comprobante
     */
    public function comprobante(): BelongsTo
    {
        return $this->belongsTo(Comprobante::class);
    }

    /**
     * Relación: Venta contiene múltiples productos
     */
    public function productos(): BelongsToMany
    {
        return $this->belongsToMany(Producto::class)
            ->withTimestamps()
            ->withPivot('cantidad', 'precio_venta', 'tarifa_unitaria');
    }

    /**
     * Relación: Venta tiene múltiples transacciones de pago
     */
    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    /**
     * Relación: Venta tiene múltiples movimientos en caja
     */
    public function movimientos(): HasMany
    {
        return $this->hasMany(Movimiento::class);
    }

    /**
     * Relación: Venta tiene múltiples asientos asignados (Cinema)
     */
    public function asientosCinema(): HasMany
    {
        return $this->hasMany(FuncionAsiento::class);
    }

    /**
     * Scope: Obtener ventas en un período específico
     */
    public function scopeEnPeriodo($query, Carbon $inicio, Carbon $fin)
    {
        return $query->whereBetween('fecha_hora', [$inicio, $fin]);
    }

    /**
     * Scope: Obtener ventas por empresa
     */
    public function scopeForEmpresa($query, int $empresaId)
    {
        return $query->withoutGlobalScope('empresa')->where('empresa_id', $empresaId);
    }

    /**
     * Scope: Obtener ventas por usuario
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: Obtener ventas por caja
     */
    public function scopeByCaja($query, int $cajaId)
    {
        return $query->where('caja_id', $cajaId);
    }

    /**
     * Scope para ventas de boletería únicamente
     */
    public function scopeBoleteria($query)
    {
        return $query->where('canal', 'ventanilla');
    }

    /**
     * Scope para ventas de confitería únicamente
     */
    public function scopeConfiteria($query)
    {
        return $query->where('canal', 'confiteria');
    }

    /**
     * Scope para ventas web únicamente
     */
    public function scopeWeb($query)
    {
        return $query->where('canal', 'web');
    }

    /**
     * Scope para todas las ventas físicas (boletería + confitería + mixta, sin web)
     */
    public function scopeFisicas($query)
    {
        return $query->whereIn('canal', ['ventanilla', 'confiteria', 'mixta']);
    }

    /**
     * Scope para ventas mixtas (boletería + confitería) únicamente
     */
    public function scopeMixta($query)
    {
        return $query->where('canal', 'mixta');
    }

    /**
     * Obtener solo la fecha de la venta
     */
    public function getFechaAttribute(): string
    {
        return Carbon::parse($this->fecha_hora)->format('d-m-Y');
    }

    /**
     * Obtener solo la hora de la venta
     */
    public function getHoraAttribute(): string
    {
        return Carbon::parse($this->fecha_hora)->format('H:i');
    }

    /**
     * Calcular el total incluyendo tarifa
     */
    public function getTotalConTarifaAttribute(): float
    {
        return ($this->subtotal ?? 0) + ($this->impuesto ?? 0) + ($this->monto_tarifa ?? 0);
    }

    /**
     * Generar el número de venta
     */
    public function generarNumeroVenta(int $cajaId, string $tipoComprobante): string
    {
        // 1. Obtener el último consecutivo de esta CAJA específica
        $ultimaVenta = Venta::where('caja_id', $cajaId)
            ->whereNotNull('consecutivo_pos')
            ->orderBy('consecutivo_pos', 'desc')
            ->first();

        $nuevoConsecutivo = $ultimaVenta ? ($ultimaVenta->consecutivo_pos + 1) : 1;

        // 2. Guardamos el consecutivo numérico puro para auditoría
        $this->consecutivo_pos = $nuevoConsecutivo;

        // 3. Determinar el prefijo (POS-CajaID)
        $prefijo = "POS" . str_pad($cajaId, 3, "0", STR_PAD_LEFT);

        // 4. Formatear el número visible: POS001-0000001
        return $prefijo . "-" . str_pad($nuevoConsecutivo, 7, "0", STR_PAD_LEFT);
    }

    /**
     * Calcular la tarifa de servicio
     */
    public function calcularTarifa(float $porcentajeTarifa): self
    {
        $this->tarifa_servicio = (float) number_format($porcentajeTarifa, 2, '.', '');
        $this->monto_tarifa = (float) number_format(($this->subtotal ?? 0) * ($porcentajeTarifa / 100), 2, '.', '');

        return $this;
    }

    /**
     * Calcular tarifa unitaria de un producto en la venta
     */
    public function calcularTarifaUnitaria(int $productoId, float $precioVenta): float
    {
        if ($this->tarifa_servicio) {
            return (float) (($precioVenta * (float) $this->tarifa_servicio) / 100);
        }

        return 0;
    }

    /**
     * Helper para obtener todos los asientos concatenados
     */
    public function getAsientosConcatenadosAttribute(): string
    {
        return $this->asientosCinema->pluck('codigo_asiento')->implode(', ');
    }

    // =========================================================
    // FIX 1 FASE 6 — Relación Fiscal (no existía en fases 1-5)
    // =========================================================

    /**
     * Documento fiscal electrónico asociado a esta venta.
     * Una venta puede tener máximo 1 documento fiscal activo.
     */
    public function documentoFiscal(): HasOne
    {
        return $this->hasOne(DocumentoFiscal::class, 'venta_id');
    }
}

