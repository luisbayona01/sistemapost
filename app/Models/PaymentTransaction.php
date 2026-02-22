<?php

namespace App\Models;

use App\Enums\MetodoPagoEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentTransaction extends Model
{
    protected $guarded = ['id'];

    protected $table = 'payment_transactions';

    protected $casts = [
        'payment_method' => MetodoPagoEnum::class,
        'status' => 'string', // PENDING|SUCCESS|FAILED|REFUNDED|CANCELLED
        'metadata' => 'array',
        'amount_paid' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Seguridad: Las transacciones exitosas son inmutables
     */
    protected static function booted()
    {
        static::updating(function ($transaction) {
            if ($transaction->getOriginal('status') === 'SUCCESS') {
                throw new \Exception('No se puede modificar una transacción de pago exitosa (Inmutable).');
            }
        });

        static::deleting(function ($transaction) {
            if ($transaction->status === 'SUCCESS') {
                throw new \Exception('No se puede eliminar una transacción de pago exitosa (Auditoría Crítica).');
            }
        });
    }

    /**
     * Relación: Transacción pertenece a una empresa
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * Relación: Transacción pertenece a una venta
     */
    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class);
    }

    /**
     * Verificar si la transacción fue exitosa
     */
    public function isSuccessful(): bool
    {
        return $this->status === 'SUCCESS';
    }

    /**
     * Verificar si la transacción falló
     */
    public function isFailed(): bool
    {
        return $this->status === 'FAILED';
    }

    /**
     * Verificar si la transacción está pendiente
     */
    public function isPending(): bool
    {
        return $this->status === 'PENDING';
    }

    /**
     * Marcar transacción como completada
     */
    public function markAsSuccess(array $metadata = null): self
    {
        $this->update([
            'status' => 'SUCCESS',
            'metadata' => $metadata ?? $this->metadata,
        ]);

        return $this;
    }

    /**
     * Marcar transacción como fallida
     */
    public function markAsFailed(string $errorMessage, array $metadata = null): self
    {
        $this->update([
            'status' => 'FAILED',
            'error_message' => $errorMessage,
            'metadata' => $metadata ?? $this->metadata,
        ]);

        return $this;
    }

    /**
     * Scope: Obtener transacciones exitosas
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'SUCCESS');
    }

    /**
     * Scope: Obtener transacciones fallidas
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'FAILED');
    }

    /**
     * Scope: Obtener transacciones pendientes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'PENDING');
    }

    /**
     * Scope: Obtener transacciones por método de pago
     */
    public function scopeByPaymentMethod($query, MetodoPagoEnum $method)
    {
        return $query->where('payment_method', $method);
    }

    /**
     * Scope: Obtener transacciones por empresa
     */
    public function scopeForEmpresa($query, int $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }
}
