<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categoria;
use App\Models\Caracteristica;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            'comida',
            'bebidas',
            'bebidas calientes',
            'licores y vinos',
            'postres'
        ];

        foreach ($categories as $cat) {
            $char = Caracteristica::firstOrCreate(
                ['nombre' => $cat],
                ['descripcion' => 'Categoría de POS: ' . $cat, 'estado' => 1]
            );

            Categoria::firstOrCreate(['caracteristica_id' => $char->id]);
        }
    }
}
