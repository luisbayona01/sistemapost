<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MostrarEstructuraTablas extends Command
{
    protected $signature = 'db:estructura';
    protected $description = 'Muestra la estructura real de las tablas principales';

    public function handle()
    {
        $tablas = ['cajas', 'ventas', 'funcion_asientos', 'productos'];

        foreach ($tablas as $tabla) {
            $this->info("📋 TABLA: {$tabla}");
            $columnas = DB::select("SHOW COLUMNS FROM {$tabla}");

            foreach ($columnas as $columna) {
                $this->line("  • {$columna->Field} ({$columna->Type})");
            }

            $this->line('');
        }
    }
}
