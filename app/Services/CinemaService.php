<?php

namespace App\Services;

use App\Models\FuncionAsiento;
use App\Events\AsientoBloqueado;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CinemaService
{
    /**
     * CRÍTICO: Reservar asiento con transacción atómica
     * 
     * @param int $funcionId
     * @param string $codigoAsiento
     * @param string $sessionId
     * @param int|null $userId
     * @return bool
     */
    public function reservarAsiento(int $funcionId, string $codigoAsiento, string $sessionId, ?int $userId = null): bool
    {
        return DB::transaction(function () use ($funcionId, $codigoAsiento, $sessionId, $userId) {
            // 1. Bloquear fila para evitar race conditions
            $asiento = FuncionAsiento::where('funcion_id', $funcionId)
                ->where('codigo_asiento', $codigoAsiento)
                ->lockForUpdate()
                ->first();

            if (!$asiento) {
                Log::warning("Asiento no encontrado: {$codigoAsiento} en función {$funcionId}");
                return false;
            }

            // 2. Toggle: Si ya está reservado por esta sesión, liberar
            if ($asiento->isReservedBy($sessionId)) {
                $asiento->liberar();
                AsientoBloqueado::dispatch($asiento);
                return true;
            }

            // 3. Verificar disponibilidad real
            if (!$asiento->isAvailable()) {
                Log::info("Asiento {$codigoAsiento} no disponible. Estado: {$asiento->estado}");
                return false;
            }

            // 4. Reservar el asiento
            $asiento->update([
                'estado' => FuncionAsiento::ESTADO_RESERVADO,
                'reservado_hasta' => now()->addMinutes(FuncionAsiento::RESERVATION_TIMEOUT_MINUTES),
                'session_id' => $sessionId,
                'reservado_por' => $userId,
            ]);

            AsientoBloqueado::dispatch($asiento);

            Log::info("Asiento {$codigoAsiento} reservado hasta {$asiento->reservado_hasta}");

            return true;
        });
    }

    /**
     * CRÍTICO: Confirmar venta con rollback automático en caso de error
     * 
     * @param int $funcionId
     * @param string $codigoAsiento
     * @param string $sessionId
     * @param int $ventaId
     * @return bool
     */
    public function confirmarVenta(int $funcionId, string $codigoAsiento, string $sessionId, int $ventaId): bool
    {
        return DB::transaction(function () use ($funcionId, $codigoAsiento, $sessionId, $ventaId) {
            // 1. Bloquear fila
            $asiento = FuncionAsiento::where('funcion_id', $funcionId)
                ->where('codigo_asiento', $codigoAsiento)
                ->lockForUpdate()
                ->first();

            if (!$asiento) {
                Log::error("Asiento no encontrado al confirmar venta: {$codigoAsiento}");
                return false;
            }

            // 2. Caso A: Reservado por esta sesión (flujo normal web/reserva)
            if ($asiento->isReservedBy($sessionId)) {
                $asiento->marcarVendido($ventaId);
                Log::info("Venta confirmada para asiento reservado: {$codigoAsiento}");
                return true;
            }

            // 3. Caso B: Disponible o reserva expirada (venta directa POS)
            if ($asiento->isAvailable()) {
                $asiento->marcarVendido($ventaId);
                Log::info("Venta directa confirmada: {$codigoAsiento}");
                return true;
            }

            // 4. Caso C: No disponible (vendido o reservado por otro)
            Log::warning("Asiento {$codigoAsiento} no disponible para venta. Estado: {$asiento->estado}");
            return false;
        });
    }

    /**
     * CRÍTICO: Liberar asientos de una venta cancelada
     * 
     * @param int $ventaId
     * @return int Cantidad de asientos liberados
     */
    public function liberarAsientosPorVenta(int $ventaId): int
    {
        return DB::transaction(function () use ($ventaId) {
            $asientos = FuncionAsiento::where('venta_id', $ventaId)
                ->lockForUpdate()
                ->get();

            $liberados = 0;
            foreach ($asientos as $asiento) {
                if ($asiento->estado === FuncionAsiento::ESTADO_VENDIDO) {
                    $asiento->liberar();
                    $liberados++;
                }
            }

            Log::info("Liberados {$liberados} asientos de venta cancelada #{$ventaId}");

            return $liberados;
        });
    }

    /**
     * CRÍTICO: Liberar reservas expiradas (llamado por Job)
     * 
     * @return int Cantidad de asientos liberados
     */
    public function liberarReservasExpiradas(): int
    {
        return DB::transaction(function () {
            $asientos = FuncionAsiento::reservasExpiradas()
                ->lockForUpdate()
                ->get();

            $liberados = 0;
            foreach ($asientos as $asiento) {
                $asiento->liberar();
                $liberados++;
            }

            if ($liberados > 0) {
                Log::info("Liberadas {$liberados} reservas expiradas automáticamente");
            }

            return $liberados;
        });
    }

    /**
     * SOPORTE: Liberar todas las reservas de una función específica
     * 
     * @param int $funcionId
     * @return int Cantidad de asientos liberados
     */
    public function liberarReservasPorFuncion(int $funcionId): int
    {
        return DB::transaction(function () use ($funcionId) {
            $asientos = FuncionAsiento::porFuncion($funcionId)
                ->reservados()
                ->lockForUpdate()
                ->get();

            $liberados = 0;
            foreach ($asientos as $asiento) {
                $asiento->liberar();
                $liberados++;
            }

            Log::warning("SOPORTE: Liberadas {$liberados} reservas de función #{$funcionId}");

            return $liberados;
        });
    }

    /**
     * SOPORTE: Liberar TODAS las reservas del sistema (emergencia)
     * 
     * @return int Cantidad de asientos liberados
     */
    public function liberarTodasLasReservas(): int
    {
        return DB::transaction(function () {
            $asientos = FuncionAsiento::reservados()
                ->lockForUpdate()
                ->get();

            $liberados = 0;
            foreach ($asientos as $asiento) {
                $asiento->liberar();
                $liberados++;
            }

            Log::critical("SOPORTE: Liberadas TODAS las reservas del sistema ({$liberados} asientos)");

            return $liberados;
        });
    }

    /**
     * Obtener estadísticas de asientos por función
     * 
     * @param int $funcionId
     * @return array
     */
    public function getEstadisticasFuncion(int $funcionId): array
    {
        $total = FuncionAsiento::porFuncion($funcionId)->count();
        $disponibles = FuncionAsiento::porFuncion($funcionId)->disponibles()->count();
        $reservados = FuncionAsiento::porFuncion($funcionId)->reservasActivas()->count();
        $vendidos = FuncionAsiento::porFuncion($funcionId)->vendidos()->count();
        $expirados = FuncionAsiento::porFuncion($funcionId)->reservasExpiradas()->count();

        return [
            'total' => $total,
            'disponibles' => $disponibles,
            'reservados' => $reservados,
            'vendidos' => $vendidos,
            'expirados' => $expirados,
            'ocupacion_porcentaje' => $total > 0 ? round(($vendidos / $total) * 100, 2) : 0,
        ];
    }
}

