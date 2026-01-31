<?php

namespace App\Models;

use App\Observers\VentaObsever;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy(VentaObsever::class)]
class Venta extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'fecha_hora' => 'datetime',
        'subtotal' => 'decimal:2',
        'impuesto' => 'decimal:2',
        'total' => 'decimal:2',
        'monto_recibido' => 'decimal:2',
        'vuelto_entregado' => 'decimal:2',
        'tarifa_servicio' => 'decimal:2',
        'monto_tarifa' => 'decimal:2',
        'estado_pago' => 'string', // PENDIENTE, PAGADA, FALLIDA, CANCELADA
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
     * Global scope: Filtrar ventas por empresa del usuario autenticado
     */
    protected static function booted(): void
    {
        static::addGlobalScope('empresa', function (Builder $query) {
            if (auth()->check() && auth()->user()->empresa_id) {
                $query->where('ventas.empresa_id', auth()->user()->empresa_id);
            }
        });
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
        // Determinar el prefijo según el tipo de comprobante
        $prefijo = strtoupper(substr($tipoComprobante, 0, 1)); // "B" para Boleta, "F" para Factura

        // Obtener la última venta de la caja
        $ultimaVenta = Venta::where('caja_id', $cajaId)
            ->latest('id')
            ->first();

        // Extraer la parte numérica del número de venta o comenzar desde 1
        $ultimoNumero = $ultimaVenta
            ? (int) substr($ultimaVenta->numero_comprobante, 7) // "0000001" -> 1
            : 0;

        // Incrementar el número
        $nuevoNumero = $ultimoNumero + 1;

        // Formatear el número de venta
        $numeroVenta = sprintf("%s%03d - %07d", $prefijo, $cajaId, $nuevoNumero);

        return $numeroVenta;
    }

    /**
     * Calcular la tarifa de servicio
     */
    public function calcularTarifa(float $porcentajeTarifa): self
    {
        $this->tarifa_servicio = $porcentajeTarifa;
        $this->monto_tarifa = ($this->subtotal ?? 0) * ($porcentajeTarifa / 100);

        return $this;
    }

    /**
     * Calcular tarifa unitaria de un producto en la venta
     */
    public function calcularTarifaUnitaria(int $productoId, float $precioVenta): float
    {
        if ($this->tarifa_servicio) {
            return ($precioVenta * $this->tarifa_servicio) / 100;
        }

        return 0;
    }
}

