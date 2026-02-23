<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Empresa>
 */
class EmpresaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => fake()->company(),
            'propietario' => fake()->name(),
            'ruc' => fake()->numerify('##########'),
            'direccion' => fake()->address(),
            'correo' => fake()->companyEmail(),
            'telefono' => fake()->phoneNumber(),
            'moneda_id' => 1, // Asume que existe moneda con ID 1
            'porcentaje_impuesto' => 19,
            'abreviatura_impuesto' => 'IVA',
            'plan_id' => 1, // Asume que existe plan con ID 1
            'estado' => 'activa',
            'estado_suscripcion' => 'trial',
        ];
    }
}
