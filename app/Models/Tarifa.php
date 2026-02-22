<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Tarifa extends Model
{
    protected $fillable = [
        'empresa_id',
        'nombre',
        'precio',
        'dias_semana',
        'aplica_festivos',
        'es_default',
        'activa',
        'color',
    ];

    protected $casts = [
        'dias_semana' => 'array',
        'aplica_festivos' => 'boolean',
        'es_default' => 'boolean',
        'activa' => 'boolean',
        'precio' => 'decimal:2',
    ];

    public static function obtenerParaFecha(Carbon $fecha, $empresaId)
    {
        $dia = $fecha->dayOfWeek;
        $esFestivo = self::esFestivoColombia($fecha);

        $tarifas = self::where('empresa_id', $empresaId)
            ->where('activa', true)
            ->get();

        foreach ($tarifas as $tarifa) {
            $dias = $tarifa->dias_semana ?? [];
            if ($esFestivo && $tarifa->aplica_festivos)
                return $tarifa;
            if (in_array($dia, $dias))
                return $tarifa;
        }

        return self::where('empresa_id', $empresaId)
            ->where('es_default', true)
            ->first();
    }

    public static function esFestivoColombia(Carbon $fecha): bool
    {
        $festivos = [
            '2025-01-01',
            '2025-01-06',
            '2025-03-24',
            '2025-04-17',
            '2025-04-18',
            '2025-05-01',
            '2025-06-02',
            '2025-06-23',
            '2025-06-30',
            '2025-07-20',
            '2025-08-07',
            '2025-08-18',
            '2025-10-13',
            '2025-11-03',
            '2025-11-17',
            '2025-12-08',
            '2025-12-25',
            '2026-01-01',
            '2026-01-12',
            '2026-03-23',
            '2026-04-02',
            '2026-04-03',
            '2026-05-01',
            '2026-05-18',
            '2026-06-08',
            '2026-06-15',
            '2026-07-20',
            '2026-08-07',
            '2026-08-17',
            '2026-10-12',
            '2026-11-02',
            '2026-11-16',
            '2026-12-08',
            '2026-12-25',
        ];
        return in_array($fecha->format('Y-m-d'), $festivos);
    }
}
