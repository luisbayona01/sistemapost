<?php

namespace App\Jobs;

use App\Services\CinemaService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ReleaseStaleSeatReservations implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Número de intentos en caso de fallo
     */
    public $tries = 3;

    /**
     * Timeout del job (30 segundos)
     */
    public $timeout = 30;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     * 
     * Libera automáticamente asientos RESERVADO con más de 5 minutos
     */
    public function handle(CinemaService $cinemaService): void
    {
        try {
            $liberados = $cinemaService->liberarReservasExpiradas();

            if ($liberados > 0) {
                Log::info("[ReleaseStaleSeatReservations] Liberados {$liberados} asientos expirados");
            }
        } catch (\Exception $e) {
            Log::error("[ReleaseStaleSeatReservations] Error al liberar asientos: " . $e->getMessage());
            throw $e; // Re-lanzar para que Laravel maneje el retry
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::critical("[ReleaseStaleSeatReservations] Job falló después de {$this->tries} intentos: " . $exception->getMessage());
    }
}
