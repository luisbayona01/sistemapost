<?php

namespace App\Services;

use App\Models\PeriodoOperativo;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AccountingService
{
    /**
     * Obtener el día operativo vigente para una empresa.
     * Si no existe uno abierto, se crea uno nuevo (hoy).
     */
    public function getActiveDay(int $empresaId): Carbon
    {
        $periodo = PeriodoOperativo::where('empresa_id', $empresaId)
            ->where('estado', 'ABIERTO')
            ->orderBy('fecha_operativa', 'desc')
            ->first();

        if (!$periodo) {
            // Si no hay periodo abierto, creamos el de hoy
            $hoy = now();

            // Validar que no exista ya un periodo cerrado para hoy (evitar duplicados raros)
            $existeCerrado = PeriodoOperativo::where('empresa_id', $empresaId)
                ->where('fecha_operativa', $hoy->format('Y-m-d'))
                ->exists();

            $fecha = $hoy;
            if ($existeCerrado) {
                // Si hoy ya se cerró, el siguiente activo es mañana
                $fecha->addDay();
            }

            $periodo = PeriodoOperativo::create([
                'empresa_id' => $empresaId,
                'fecha_operativa' => $fecha->format('Y-m-d'),
                'estado' => 'ABIERTO'
            ]);
        }

        return Carbon::parse($periodo->fecha_operativa);
    }

    /**
     * Cerrar el día operativo manualmente.
     */
    public function closeDay(int $empresaId, int $userId): array
    {
        return DB::transaction(function () use ($empresaId, $userId) {
            $periodo = PeriodoOperativo::where('empresa_id', $empresaId)
                ->where('estado', 'ABIERTO')
                ->first();

            if (!$periodo) {
                return [
                    'success' => false,
                    'message' => 'No hay un día operativo abierto para cerrar.'
                ];
            }

            // Validar que no haya cajas abiertas
            $cajasAbiertas = \App\Models\Caja::where('empresa_id', $empresaId)
                ->where('estado', 'ABIERTA')
                ->count();

            if ($cajasAbiertas > 0) {
                return [
                    'success' => false,
                    'message' => "No se puede cerrar el día. Hay {$cajasAbiertas} cajas abiertas. Cierra todas las cajas primero."
                ];
            }

            $periodo->update([
                'estado' => 'CERRADO',
                'fecha_cierre' => now(),
                'cerrado_por' => $userId
            ]);

            return [
                'success' => true,
                'message' => 'El día operativo ha sido cerrado correctamente.'
            ];
        });
    }

    /**
     * Aplicar regla de redondeo definida en config.
     */
    public function applyRounding(float $amount): float
    {
        $decimals = config('impuestos.regla_redondeo', 0);
        return round($amount, $decimals);
    }
}
