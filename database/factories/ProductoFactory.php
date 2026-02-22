<?php

namespace Database\Factories;

use App\Models\Empresa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Producto>
 */
class ProductoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'empresa_id' => Empresa::factory(),
            'codigo' => fake()->numerify('############'),
            'nombre' => fake()->word(),
            'descripcion' => fake()->sentence(),
            'precio' => fake()->randomFloat(2, 1, 100),
            'categoria_id' => 1,
            'marca_id' => 1,
            'presentacione_id' => 1,
            'estado' => 1,
        ];
    }
}
