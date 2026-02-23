<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Producto;
use App\Models\Inventario;
use App\Models\Kardex;
use App\Models\Ubicacione;

class InventarioConfiteriaSeeder extends Seeder
{
    public function run()
    {
        // Obtener todos los productos de confitería (sin distribuidor)
        $productos = Producto::whereNull('distribuidor_id')->get();

        // Obtener la ubicación por defecto
        $ubicacion = Ubicacione::firstOrCreate(
            ['nombre' => 'Bodega Central'],
            ['descripcion' => 'Almacén principal', 'estado' => 1]
        );

        foreach ($productos as $producto) {
            // Verificar si ya tiene inventario
            $inventarioExistente = Inventario::where('producto_id', $producto->id)->first();

            if (!$inventarioExistente) {
                // Crear inventario inicial
                $stockInicial = rand(50, 200); // Stock aleatorio entre 50 y 200 unidades

                $inventario = Inventario::create([
                    'empresa_id' => $producto->empresa_id,
                    'producto_id' => $producto->id,
                    'ubicacione_id' => $ubicacion->id,
                    'cantidad' => $stockInicial,
                    'cantidad_minima' => 10,
                    'cantidad_maxima' => 500,
                ]);

                // Crear registro en Kardex para el inventario inicial
                Kardex::create([
                    'empresa_id' => $producto->empresa_id,
                    'producto_id' => $producto->id,
                    'tipo_transaccion' => 'COMPRA',
                    'entrada' => $stockInicial,
                    'salida' => 0,
                    'costo_unitario' => $producto->precio * 0.6, // Costo estimado al 60% del precio de venta
                    'saldo' => $stockInicial,
                    'descripcion_transaccion' => 'Inventario inicial - Seeder',
                ]);

                echo "✓ Inventario creado para: {$producto->nombre} ({$stockInicial} unidades)\n";
            } else {
                echo "- Ya existe inventario para: {$producto->nombre}\n";
            }
        }

        echo "\n✅ Proceso completado. Total productos procesados: " . $productos->count() . "\n";
    }
}
