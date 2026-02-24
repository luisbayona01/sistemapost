<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

use App\Traits\HasEmpresaScope;

class FuncionAsiento extends Model
{
    use HasEmpresaScope;
    // ========================================
    // CONSTANTES DE ESTADO (FUENTE DE VERDAD)
    // ========================================
    public const ESTADO_DISPONIBLE = 'DISPONIBLE';
    public const ESTADO_RESERVADO = 'RESERVADO';
    public const ESTADO_VENDIDO = 'VENDIDO';

    // Tiempo de expiración de reservas (5 minutos)
    public const RESERVATION_TIMEOUT_MINUTES = 5;

    protected $fillable = [
        'funcion_id',
        'codigo_asiento',
        'estado',
        'reservado_hasta',
        'session_id',
        'venta_id',
        'reservado_por'
    ];

    protected $casts = [
        'reservado_hasta' => 'datetime',
    ];

    // ========================================
    // RELACIONES
    // ========================================

    public function funcion(): BelongsTo
    {
        return $this->belongsTo(Funcion::class);
    }

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class);
    }

    /**
     * Relación Muchos a Muchos (Mediante tabla pivote)
     * Algunos reportes y lógicas usan la tabla de enlace.
     */
    public function ventas(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Venta::class, 'venta_funcion_asientos');
    }

    public function reservadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reservado_por');
    }

    // ========================================
    // SCOPES (FILTROS REUTILIZABLES)
    // ========================================

    /**
     * Scope: Solo asientos disponibles
     */
    public function scopeDisponibles(Builder $query): Builder
    {
        return $query->where('estado', self::ESTADO_DISPONIBLE);
    }

    /**
     * Scope: Solo asientos reservados
     */
    public function scopeReservados(Builder $query): Builder
    {
        return $query->where('estado', self::ESTADO_RESERVADO);
    }

    /**
     * Scope: Solo asientos vendidos
     */
    public function scopeVendidos(Builder $query): Builder
    {
        return $query->where('estado', self::ESTADO_VENDIDO);
    }

    /**
     * Scope: Reservas expiradas (huérfanas)
     */
    public function scopeReservasExpiradas(Builder $query): Builder
    {
        return $query->where('estado', self::ESTADO_RESERVADO)
            ->where('reservado_hasta', '<', now());
    }

    /**
     * Scope: Reservas activas (no expiradas)
     */
    public function scopeReservasActivas(Builder $query): Builder
    {
        return $query->where('estado', self::ESTADO_RESERVADO)
            ->where('reservado_hasta', '>=', now());
    }

    /**
     * Scope: Por función específica
     */
    public function scopePorFuncion(Builder $query, int $funcionId): Builder
    {
        return $query->where('funcion_id', $funcionId);
    }

    // ========================================
    // MÉTODOS DE VALIDACIÓN
    // ========================================

    /**
     * Verifica si el asiento está realmente disponible
     * (DISPONIBLE o reserva expirada)
     */
    public function isAvailable(): bool
    {
        // Caso 1: Estado DISPONIBLE
        if ($this->estado === self::ESTADO_DISPONIBLE) {
            return true;
        }

        // Caso 2: RESERVADO pero expirado
        if ($this->estado === self::ESTADO_RESERVADO && $this->isReservationExpired()) {
            return true;
        }

        return false;
    }

    /**
     * Verifica si la reserva está expirada
     */
    public function isReservationExpired(): bool
    {
        if ($this->estado !== self::ESTADO_RESERVADO) {
            return false;
        }

        return $this->reservado_hasta && $this->reservado_hasta->isPast();
    }

    /**
     * Verifica si está reservado por una sesión específica
     */
    public function isReservedBy(string $sessionId): bool
    {
        return $this->estado === self::ESTADO_RESERVADO
            && $this->session_id === $sessionId
            && !$this->isReservationExpired();
    }

    /**
     * Verifica si está reservado por un usuario específico
     */
    public function isReservedByUser(int $userId): bool
    {
        return $this->estado === self::ESTADO_RESERVADO
            && $this->reservado_por === $userId
            && !$this->isReservationExpired();
    }

    // ========================================
    // MÉTODOS DE ACCIÓN
    // ========================================

    /**
     * Libera la reserva (vuelve a DISPONIBLE)
     */
    public function liberar(): bool
    {
        $updated = $this->update([
            'estado' => self::ESTADO_DISPONIBLE,
            'reservado_hasta' => null,
            'session_id' => null,
            'reservado_por' => null,
            'venta_id' => null,
        ]);

        if ($updated && \Illuminate\Support\Facades\Schema::hasTable('venta_funcion_asientos')) {
            \Illuminate\Support\Facades\DB::table('venta_funcion_asientos')
                ->where('funcion_asiento_id', $this->id)
                ->delete();
        }

        return $updated;
    }

    /**
     * Marca como vendido
     */
    public function marcarVendido(int $ventaId): bool
    {
        $updated = $this->update([
            'estado' => self::ESTADO_VENDIDO,
            'venta_id' => $ventaId,
            'reservado_hasta' => null,
            'session_id' => null,
            'reservado_por' => null,
        ]);

        if ($updated && \Illuminate\Support\Facades\Schema::hasTable('venta_funcion_asientos')) {
            \Illuminate\Support\Facades\DB::table('venta_funcion_asientos')->updateOrInsert(
                ['venta_id' => $ventaId, 'funcion_asiento_id' => $this->id],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }

        return $updated;
    }

    // ========================================
    // ACCESSORS
    // ========================================

    /**
     * Tiempo restante de reserva en segundos
     */
    public function getTiempoRestanteAttribute(): ?int
    {
        if ($this->estado !== self::ESTADO_RESERVADO || !$this->reservado_hasta) {
            return null;
        }

        $diff = now()->diffInSeconds($this->reservado_hasta, false);
        return $diff > 0 ? $diff : 0;
    }

    /**
     * Estado legible para humanos
     */
    public function getEstadoLegibleAttribute(): string
    {
        return match ($this->estado) {
            self::ESTADO_DISPONIBLE => 'Disponible',
            self::ESTADO_RESERVADO => 'Reservado',
            self::ESTADO_VENDIDO => 'Vendido',
            default => 'Desconocido',
        };
    }
}
