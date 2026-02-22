<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\{Funcion, FuncionAsiento, Sala};

class GenerarAsientosFunciones extends Command
{
    protected $signature = 'funciones:generar-asientos {--funcion_id=}';
    protected $description = 'Genera asientos para funciones que no los tienen';

    public function handle()
    {
        $funcionId = $this->option('funcion_id');

        $query = Funcion::with('sala');

        if ($funcionId) {
            $query->where('id', $funcionId);
        }

        $funciones = $query->get();

        foreach ($funciones as $funcion) {

            // Verificar si ya tiene asientos
            $asientosExistentes = FuncionAsiento::where('funcion_id', $funcion->id)->count();

            if ($asientosExistentes > 0) {
                $this->info("Función #{$funcion->id} ya tiene asientos");
                continue;
            }

            $sala = $funcion->sala;

            if (!$sala) {
                $this->error("Función #{$funcion->id} no tiene sala asignada");
                continue;
            }

            // Generar asientos
            for ($fila = 1; $fila <= $sala->filas; $fila++) {
                for ($columna = 1; $columna <= $sala->columnas; $columna++) {

                    $letraFila = chr(64 + $fila); // A, B, C, D...
                    $nombreAsiento = $letraFila . $columna;

                    FuncionAsiento::create([
                        'funcion_id' => $funcion->id,
                        'codigo_asiento' => $nombreAsiento,
                        'estado' => FuncionAsiento::ESTADO_DISPONIBLE,
                    ]);
                }
            }

            $this->info("✓ Función #{$funcion->id}: {$sala->filas}×{$sala->columnas} = " . ($sala->filas * $sala->columnas) . " asientos creados");
        }

        $this->info('✅ Proceso completado');
    }
}
