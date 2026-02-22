<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FuncionAsiento;
use Illuminate\Support\Facades\Log;

class CleanCinemaSeats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cinema:clean-seats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Libera asientos bloqueados temporalmente que han expirado (Ghost Seats)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando limpieza de Ghost Seats...');

        // Usamos el scope existente que busca RESERVADO + expirado
        $query = FuncionAsiento::reservasExpiradas();

        $count = $query->count();

        if ($count === 0) {
            $this->info('No se encontraron asientos expirados.');
            return 0;
        }

        $this->info("Se encontraron {$count} asientos expirados. Liberando...");

        // Procesar en chunks por seguridad si fueran miles
        $liberados = 0;
        $errores = 0;

        // Iteramos sobre los registros para ejecutar la lógica de liberar() modelo por modelo
        // Esto asegura que se limpie la tabla pivote si existe lógica en el modelo
        foreach ($query->lazy() as $asiento) {
            try {
                if ($asiento->liberar()) {
                    $liberados++;
                } else {
                    $errores++;
                }
            } catch (\Exception $e) {
                Log::error("Error liberando asiento ID {$asiento->id}: " . $e->getMessage());
                $errores++;
            }
        }

        Log::info("Cinema Cleaner: Liberados {$liberados} asientos. Errores: {$errores}.");

        $this->info("Proceso finalizado. Liberados: {$liberados}. Errores: {$errores}.");

        return 0;
    }
}
