<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Caracteristica;
use App\Models\Presentacione;
use App\Models\Insumo;
use App\Models\Inventario;
use App\Models\Ubicacione;
use App\Models\Empresa;

class FixProductsSeeder extends Seeder
{
    public function run()
    {
        $empresa = Empresa::first();
        if (!$empresa)
            return;

        // 1. Asegurar Categoría 'Snacks' (ID 2 según mi lista previa, pero usemos nombres)
        $charCatSnacks = Caracteristica::where('nombre', 'Snacks')->first();
        if (!$charCatSnacks) {
            $charCatSnacks = Caracteristica::create([
                'nombre' => 'Snacks',
                'empresa_id' => $empresa->id,
                'descripcion' => 'Productos de confitería',
                'estado' => 1
            ]);
        }
        $categoriaSnacks = Categoria::firstOrCreate(
            ['caracteristica_id' => $charCatSnacks->id, 'empresa_id' => $empresa->id]
        );

        // 2. Asegurar Categoría 'Bebidas'
        $charCatBebidas = Caracteristica::where('nombre', 'Bebidas')->first();
        if (!$charCatBebidas) {
            $charCatBebidas = Caracteristica::create([
                'nombre' => 'Bebidas',
                'empresa_id' => $empresa->id,
                'descripcion' => 'Bebidas frías y jugos',
                'estado' => 1
            ]);
        }
        $categoriaBebidas = Categoria::firstOrCreate(
            ['caracteristica_id' => $charCatBebidas->id, 'empresa_id' => $empresa->id]
        );

        // 3. Asegurar Presentación 'UNIDAD'
        $charPres = Caracteristica::where('nombre', 'UNIDAD')->first();
        if (!$charPres) {
            $charPres = Caracteristica::create([
                'nombre' => 'UNIDAD',
                'empresa_id' => $empresa->id,
                'descripcion' => 'Unidad de medida estándar',
                'estado' => 1
            ]);
        }
        $presentacionUnidad = Presentacione::firstOrCreate(
            ['caracteristica_id' => $charPres->id, 'empresa_id' => $empresa->id],
            ['sigla' => 'und']
        );

        // 4. Ubicación
        $ubicacion = Ubicacione::firstOrCreate(
            ['nombre' => 'Bodega Central', 'empresa_id' => $empresa->id]
        );

        // --- PIZZA HAWAIANA ---
        $pizza = Producto::updateOrCreate(
            ['nombre' => 'Pizza Hawaiana', 'empresa_id' => $empresa->id],
            [
                'precio' => 37000,
                'categoria_id' => $categoriaSnacks->id,
                'presentacione_id' => $presentacionUnidad->id,
                'es_venta_retail' => true,
                'disponible_venta' => true,
                'estado' => 1
            ]
        );

        Inventario::updateOrCreate(
            ['producto_id' => $pizza->id, 'empresa_id' => $empresa->id],
            ['cantidad' => 50, 'cantidad_minima' => 5, 'ubicacione_id' => $ubicacion->id]
        );

        $masa = Insumo::updateOrCreate(
            ['nombre' => 'Masa Pizza', 'empresa_id' => $empresa->id],
            ['unidad_medida' => 'und', 'costo_unitario' => 2000, 'stock_actual' => 100, 'stock_minimo' => 10]
        );
        $pina = Insumo::updateOrCreate(
            ['nombre' => 'Piña Trozos', 'empresa_id' => $empresa->id],
            ['unidad_medida' => 'g', 'costo_unitario' => 5, 'stock_actual' => 5000, 'stock_minimo' => 500]
        );

        $pizza->insumos()->sync([
            $masa->id => ['cantidad' => 1, 'unidad_medida' => 'und'],
            $pina->id => ['cantidad' => 100, 'unidad_medida' => 'g']
        ]);

        // --- ZUMO DE LIMÓN ---
        $zumo = Producto::updateOrCreate(
            ['nombre' => 'Zumo de Limón', 'empresa_id' => $empresa->id],
            [
                'precio' => 3000,
                'categoria_id' => $categoriaBebidas->id,
                'presentacione_id' => $presentacionUnidad->id,
                'es_venta_retail' => true,
                'disponible_venta' => true,
                'estado' => 1
            ]
        );

        Inventario::updateOrCreate(
            ['producto_id' => $zumo->id, 'empresa_id' => $empresa->id],
            ['cantidad' => 100, 'cantidad_minima' => 10, 'ubicacione_id' => $ubicacion->id]
        );

        $limon = Insumo::updateOrCreate(
            ['nombre' => 'Limón Fresco', 'empresa_id' => $empresa->id],
            ['unidad_medida' => 'und', 'costo_unitario' => 200, 'stock_actual' => 200, 'stock_minimo' => 20]
        );

        $zumo->insumos()->sync([
            $limon->id => ['cantidad' => 3, 'unidad_medida' => 'und']
        ]);

        // --- Recalcular Rentabilidad para todos ---
        foreach (Producto::all() as $p) {
            if ($p->insumos->count() > 0) {
                $p->calcularRentabilidad();
            }
        }

        $this->command->info('Ajuste de productos y recetas completado.');
    }
}
