<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pelicula;
use App\Models\Funcion;
use App\Models\Sala;
use App\Models\Empresa;
use Carbon\Carbon;

class TestFuncionesSeeder extends Seeder
{
    public function run()
    {
        $empresa = Empresa::first();
        if (!$empresa)
            return;

        $peliculas = Pelicula::all();
        $salas = Sala::all();

        if ($peliculas->isEmpty() || $salas->isEmpty())
            return;

        $horarios = ['10:00', '13:00', '16:00', '19:00'];
        $hoy = Carbon::today();

        foreach ($horarios as $index => $hora) {
            $pelicula = $peliculas[$index % $peliculas->count()];
            $sala = $salas[$index % $salas->count()];
            $fechaHora = $hoy->copy()->setTimeFromTimeString($hora);

            Funcion::create([
                'empresa_id' => $empresa->id,
                'pelicula_id' => $pelicula->id,
                'sala_id' => $sala->id,
                'fecha_hora' => $fechaHora,
                'precio' => 25000,
                'activo' => true
            ]);
        }
    }
}
