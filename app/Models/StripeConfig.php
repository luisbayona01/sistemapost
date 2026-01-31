<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StripeConfig extends Model
{
    protected $guarded = ['id'];

    protected $table = 'stripe_configs';

    protected $casts = [
        'test_mode' => 'boolean',
        'enabled' => 'boolean',
    ];

    /**
     * Campos que deben estar encriptados
     */
    protected $encrypted = [
        'secret_key',
        'webhook_secret',
    ];

    /**
     * Relación: Configuración pertenece a una empresa
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * Verificar si la configuración Stripe está habilitada
     */
    public function isEnabled(): bool
    {
        return $this->enabled === true;
    }

    /**
     * Verificar si está en modo de prueba
     */
    public function isTestMode(): bool
    {
        return $this->test_mode === true;
    }

    /**
     * Obtener la clave pública (sin encripción)
     */
    public function getPublicKey(): string
    {
        return $this->public_key;
    }

    /**
     * Obtener la clave secreta (desencriptada automáticamente por Laravel)
     */
    public function getSecretKey(): string
    {
        return $this->secret_key;
    }

    /**
     * Obtener el webhook secret (desencriptado automáticamente por Laravel)
     */
    public function getWebhookSecret(): string
    {
        return $this->webhook_secret;
    }

    /**
     * Scope: Obtener configuraciones habilitadas
     */
    public function scopeEnabled($query)
    {
        return $query->where('enabled', true);
    }

    /**
     * Scope: Obtener configuraciones en modo de prueba
     */
    public function scopeTestMode($query)
    {
        return $query->where('test_mode', true);
    }

    /**
     * Scope: Obtener configuraciones en modo vivo
     */
    public function scopeLiveMode($query)
    {
        return $query->where('test_mode', false);
    }

    /**
     * Scope: Obtener configuración para una empresa
     */
    public function scopeForEmpresa($query, int $empresaId)
    {
        return $query->where('empresa_id', $empresaId)->first();
    }
}
