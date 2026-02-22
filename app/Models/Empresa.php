<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Empresa extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $table = 'empresa';

    protected $casts = [
        'porcentaje_impuesto' => 'decimal:2',
        'stripe_connect_updated_at' => 'datetime',
        'tarifa_servicio_porcentaje' => 'decimal:2',
        'tarifa_servicio_monto' => 'decimal:2',
        'fecha_proximo_pago' => 'datetime',
        'fecha_vencimiento_suscripcion' => 'datetime',
        'fecha_onboarding_completado' => 'datetime',
    ];

    /**
     * Relación: Empresa tiene un plan SaaS
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(SaaSPlan::class, 'plan_id');
    }

    public function moneda(): BelongsTo
    {
        return $this->belongsTo(Moneda::class);
    }

    /**
     * Obtener el símbolo de moneda (defensivo)
     */
    public function getSimboloAttribute(): string
    {
        return $this->moneda->simbolo ?? '$';
    }

    /**
     * Relación: Empresa tiene múltiples usuarios
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Relación: Empresa tiene múltiples empleados
     */
    public function empleados(): HasMany
    {
        return $this->hasMany(Empleado::class);
    }

    /**
     * Relación: Empresa tiene múltiples cajas
     */
    public function cajas(): HasMany
    {
        return $this->hasMany(Caja::class);
    }

    /**
     * Relación: Empresa tiene múltiples ventas
     */
    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class);
    }

    /**
     * Relación: Empresa tiene múltiples productos
     */
    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class);
    }

    /**
     * Relación: Empresa tiene múltiples compras
     */
    public function compras(): HasMany
    {
        return $this->hasMany(Compra::class);
    }

    /**
     * Relación: Empresa tiene múltiples clientes
     */
    public function clientes(): HasMany
    {
        return $this->hasMany(Cliente::class);
    }

    /**
     * Relación: Empresa tiene múltiples proveedores
     */
    public function proveedores(): HasMany
    {
        return $this->hasMany(Proveedore::class);
    }

    /**
     * Relación: Empresa tiene múltiples movimientos
     */
    public function movimientos(): HasMany
    {
        return $this->hasMany(Movimiento::class);
    }

    /**
     * Relación: Empresa tiene múltiples transacciones de pago
     */
    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    /**
     * Relación: Empresa tiene múltiples registros de inventario
     */
    public function inventarios(): HasMany
    {
        return $this->hasMany(Inventario::class);
    }

    /**
     * Relación: Empresa tiene múltiples registros de kardex
     */
    public function kardexes(): HasMany
    {
        return $this->hasMany(Kardex::class);
    }

    /**
     * Relación: Empresa tiene una configuración Stripe (1-a-1)
     */
    public function stripeConfig(): HasOne
    {
        return $this->hasOne(StripeConfig::class);
    }

    /**
     * Obtener el porcentaje de impuesto
     */
    public function getImpuestoPorcentaje(): float
    {
        return (float) $this->porcentaje_impuesto;
    }

    /**
     * Obtener la abreviatura del impuesto
     */
    public function getAbreviaturaImpuesto(): string
    {
        return $this->abreviatura_impuesto ?? 'IGV';
    }

    /**
     * Calcular impuesto sobre un monto
     */
    public function calcularImpuesto(float $monto): float
    {
        return ($monto * $this->porcentaje_impuesto) / 100;
    }

    /**
     * Scope: Obtener empresas activas
     */
    public function scopeActivas($query)
    {
        return $query->where('estado', 'activa');
    }

    /**
     * Scope: Obtener empresas inactivas
     */
    public function scopeInactivas($query)
    {
        return $query->where('estado', 'inactiva');
    }

    /**
     * Verificar si la empresa tiene Stripe Connect activado
     */
    public function hasStripeConnect(): bool
    {
        return $this->stripe_account_id !== null &&
            $this->stripe_connect_status === 'ACTIVE';
    }

    /**
     * Obtener estado de Stripe Connect
     */
    public function getStripeConnectStatus(): string
    {
        return $this->stripe_connect_status ?? 'NOT_STARTED';
    }

    /**
     * Verificar si el onboarding está pendiente
     */
    public function isStripeConnectPending(): bool
    {
        return $this->stripe_connect_status === 'PENDING';
    }

    /**
     * Verificar si el onboarding fue rechazado
     */
    public function isStripeConnectRejected(): bool
    {
        return $this->stripe_connect_status === 'REJECTED';
    }

    /**
     * ======================== FASE 6: SaaS Subscription ========================
     */

    /**
     * Verificar si la suscripción está activa
     */
    public function hasActiveSuscription(): bool
    {
        return $this->estado_suscripcion === 'active' && $this->estado === 'activa';
    }

    /**
     * Verificar si la suscripción está en período de prueba
     */
    public function isTrialPeriod(): bool
    {
        return $this->estado_suscripcion === 'trial';
    }

    /**
     * Verificar si la suscripción está vencida
     */
    public function isSubscriptionExpired(): bool
    {
        return $this->estado_suscripcion === 'past_due' ||
            $this->estado_suscripcion === 'cancelled';
    }

    /**
     * Verificar si la empresa está suspendida por admin
     */
    public function isSuspendida(): bool
    {
        return $this->estado === 'suspendida';
    }

    /**
     * Calcular tarifa por transacción
     */
    public function calcularTarifaTransaccion(float $monto): float
    {
        return ($monto * $this->tarifa_servicio_porcentaje) / 100;
    }

    /**
     * Obtener tarifa formateada
     */
    public function getTarifaFormateada(): string
    {
        return number_format((float) $this->tarifa_servicio_porcentaje, 2, ',', '.');
    }

    /**
     * Scope: Suscripciones activas
     */
    public function scopeWithActiveSubscription($query)
    {
        return $query->where('estado_suscripcion', 'active')
            ->where('estado', 'activa');
    }

    /**
     * Scope: Suscripciones vencidas
     */
    public function scopeWithExpiredSubscription($query)
    {
        return $query->whereIn('estado_suscripcion', ['past_due', 'cancelled']);
    }
}

