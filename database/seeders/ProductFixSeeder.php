<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Producto;
use App\Models\Categoria;

class ProductFixSeeder extends Seeder
{
    public function run()
    {
        $map = [
            'Combo Grande' => 4, // comida
            'Crispetas' => 4, // comida
            'Gaseosa' => 1, // bebidas
            'Nachos' => 4, // comida
            'Hot Dog' => 4, // comida
            'Jet' => 7, // postres
            'Agua' => 1, // bebidas
            'Combo Pareja' => 4, // comida
            'M&Ms' => 7, // postres
        ];

        foreach ($map as $name => $catId) {
            Producto::where('nombre', 'like', '%' . $name . '%')
                ->whereNull('distribuidor_id')
                ->update(['categoria_id' => $catId]);
        }
    }
}
