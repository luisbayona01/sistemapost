<?php

namespace Database\Factories;

use App\Models\Documento;
use Illuminate\Database\Eloquent\Factories\Factory;

class PersonaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'razon_social' => fake()->name(),
            'tipo' => 'NATURAL',
            'documento_id' => Documento::factory(),
            'numero_documento' => fake()->numerify('########'),
            'estado' => 1,
        ];
    }
}
