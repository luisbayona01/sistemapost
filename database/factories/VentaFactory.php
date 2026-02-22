<?php

namespace Database\Factories;

use App\Models\Empresa;
use App\Models\User;
use App\Models\Caja;
use App\Models\Cliente;
use App\Models\Comprobante;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Venta>
 */
class VentaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'empresa_id' => Empresa::factory(),
            'user_id' => User::factory(),
            'cliente_id' => Cliente::factory(),
            'caja_id' => Caja::factory(),
            'comprobante_id' => Comprobante::factory(), // Updated
            'numero_comprobante' => fake()->unique()->numerify('B###-#######'),
            'fecha_hora' => now(),
            'impuesto' => 0,
            'subtotal' => 100,
            'total' => 100,
            'metodo_pago' => 'EFECTIVO',
            'estado_pago' => 'PAGADA',
            'monto_recibido' => 100,
            'vuelto_entregado' => 0,
        ];
    }
}
