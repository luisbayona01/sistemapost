<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ConfiguracionNumeracion extends Model
{
    protected $table = 'configuracion_numeracion';

    protected $fillable = [
        'empresa_id',
        'tipo',
        'prefijo',
        'consecutivo_actual',
        'consecutivo_inicial',
        'consecutivo_final',
        'resolucion_numero',
        'resolucion_fecha',
        'resolucion_fecha_inicio',
        'resolucion_fecha_fin',
        'activo',
        'notas',
    ];

    protected $casts = [
        'consecutivo_actual' => 'integer',
        'consecutivo_inicial' => 'integer',
        'consecutivo_final' => 'integer',
        'resolucion_fecha' => 'date',
        'resolucion_fecha_inicio' => 'date',
        'resolucion_fecha_fin' => 'date',
        'activo' => 'boolean',
    ];

    /**
     * Obtener el siguiente número de forma segura (con lock)
     */
    public function obtenerSiguienteNumero(): array
    {
        return DB::transaction(function () {
            // Lock para evitar duplicados en ambiente concurrente
            $config = self::where('id', $this->id)
                ->lockForUpdate()
                ->first();

            $siguienteNumero = $config->consecutivo_actual + 1;

            // Validar que no exceda el límite (solo para FE)
            if ($config->tipo === 'FE' && $config->consecutivo_final) {
                if ($siguienteNumero > $config->consecutivo_final) {
                    throw new \Exception(
                        "Se agotó la resolución DIAN. " .
                        "Consecutivo máximo: {$config->consecutivo_final}"
                    );
                }
            }

            // Incrementar
            $config->update([
                'consecutivo_actual' => $siguienteNumero,
            ]);

            $numeroCompleto = $config->prefijo . '-' . str_pad($siguienteNumero, 8, '0', STR_PAD_LEFT);

            return [
                'prefijo' => $config->prefijo,
                'numero' => $siguienteNumero,
                'numero_completo' => $numeroCompleto,
            ];
        });
    }

    /**
     * Verificar si la resolución está por vencer
     */
    public function estaProximaAVencer(int $diasAnticipacion = 30): bool
    {
        if (!$this->resolucion_fecha_fin) {
            return false;
        }

        return $this->resolucion_fecha_fin->lte(now()->addDays($diasAnticipacion));
    }

    /**
     * Verificar si quedan pocos consecutivos
     */
    public function tienePocosConsecutivos(int $umbral = 100): bool
    {
        if ($this->tipo === 'DE' || !$this->consecutivo_final) {
            return false;
        }

        $restantes = $this->consecutivo_final - $this->consecutivo_actual;
        return $restantes <= $umbral;
    }
}
