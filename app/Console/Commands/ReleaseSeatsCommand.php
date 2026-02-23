<?php

namespace App\Console\Commands;

use App\Services\CinemaService;
use Illuminate\Console\Command;

class ReleaseSeatsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cinema:release-seats 
                            {--funcion= : ID de la función específica}
                            {--all : Liberar TODAS las reservas del sistema (PELIGROSO)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Libera asientos reservados manualmente (para soporte)';

    /**
     * Execute the console command.
     */
    public function handle(CinemaService $cinemaService): int
    {
        $this->info('🎬 Sistema de Liberación de Asientos - Cinema Paraíso');
        $this->newLine();

        // Caso 1: Liberar TODAS las reservas (emergencia)
        if ($this->option('all')) {
            if (!$this->confirm('⚠️  ¿Estás SEGURO de liberar TODAS las reservas del sistema?', false)) {
                $this->warn('Operación cancelada.');
                return self::FAILURE;
            }

            $this->warn('Liberando TODAS las reservas...');
            $liberados = $cinemaService->liberarTodasLasReservas();

            $this->info("✅ {$liberados} asientos liberados en todo el sistema");
            return self::SUCCESS;
        }

        // Caso 2: Liberar por función específica
        if ($funcionId = $this->option('funcion')) {
            $this->info("Liberando reservas de la función #{$funcionId}...");
            $liberados = $cinemaService->liberarReservasPorFuncion((int) $funcionId);

            $this->info("✅ {$liberados} asientos liberados de la función #{$funcionId}");
            return self::SUCCESS;
        }

        // Caso 3: Liberar solo expiradas (por defecto)
        $this->info('Liberando solo reservas expiradas (>5 minutos)...');
        $liberados = $cinemaService->liberarReservasExpiradas();

        if ($liberados > 0) {
            $this->info("✅ {$liberados} asientos expirados liberados");
        } else {
            $this->comment('No hay reservas expiradas para liberar');
        }

        return self::SUCCESS;
    }
}
