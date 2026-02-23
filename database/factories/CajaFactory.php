<?php

namespace Database\Factories;

use App\Models\Empresa;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Caja>
 */
class CajaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'empresa_id' => Empresa::factory(),
            'user_id' => User::factory(),
            'nombre' => fake()->word(),
            'fecha_apertura' => now(),
            'monto_inicial' => fake()->randomFloat(2, 0, 1000),
            'estado' => 1,
        ];
    }
}
