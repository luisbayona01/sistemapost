<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categoria;
use App\Models\Presentacione;
use App\Models\Marca;
use App\Models\Caracteristica;

class FixCinemaBasicsSeeder extends Seeder
{
    public function run()
    {
        // 1. Asegurar Características
        $caracCat = Caracteristica::firstOrCreate(['nombre' => 'Entradas']);
        $caracPres = Caracteristica::firstOrCreate(['nombre' => 'Unidad']);
        $caracMarca = Caracteristica::firstOrCreate(['nombre' => 'Cine Paraíso']);

        // 2. Asegurar Registros en sus respectivas tablas
        Categoria::firstOrCreate(['caracteristica_id' => $caracCat->id]);
        Presentacione::firstOrCreate(['caracteristica_id' => $caracPres->id], ['sigla' => 'UND']);
        Marca::firstOrCreate(['caracteristica_id' => $caracMarca->id]);
    }
}
