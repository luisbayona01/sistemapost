<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Producto;
use App\Models\Insumo;
use App\Models\Categoria;
use App\Models\Empresa;
use App\Models\Presentacione;

class TestingInventorySeeder extends Seeder
{
    public function run(): void
    {
        $empresa = Empresa::first();
        if (!$empresa)
            return;

        $catSnacks = Categoria::firstOrCreate(['nombre' => 'SNACKS', 'empresa_id' => $empresa->id]);
        $catBebidas = Categoria::firstOrCreate(['nombre' => 'BEBIDAS', 'empresa_id' => $empresa->id]);
        $presUni = Presentacione::firstOrCreate(['nombre' => 'UNIDAD', 'sigla' => 'UNI', 'empresa_id' => $empresa->id]);

        // 1. Insumos Base
        $insumos = [
            ['nombre' => 'Pan Perro', 'unidad_medida' => 'UNI', 'costo_unitario' => 500, 'stock_actual' => 100, 'stock_minimo' => 20],
            ['nombre' => 'Salchicha Americana', 'unidad_medida' => 'UNI', 'costo_unitario' => 1200, 'stock_actual' => 100, 'stock_minimo' => 20],
            ['nombre' => 'Maíz Pira (kg)', 'unidad_medida' => 'KG', 'costo_unitario' => 8000, 'stock_actual' => 50, 'stock_minimo' => 5],
            ['nombre' => 'Aceite Vegetal (lt)', 'unidad_medida' => 'LT', 'costo_unitario' => 7000, 'stock_actual' => 20, 'stock_minimo' => 2],
            ['nombre' => 'Vaso 16oz', 'unidad_medida' => 'UNI', 'costo_unitario' => 150, 'stock_actual' => 500, 'stock_minimo' => 50],
            ['nombre' => 'Pitillo', 'unidad_medida' => 'UNI', 'costo_unitario' => 20, 'stock_actual' => 1000, 'stock_minimo' => 100],
            ['nombre' => 'Caja Crispetas L', 'unidad_medida' => 'UNI', 'costo_unitario' => 400, 'stock_actual' => 200, 'stock_minimo' => 20],
            ['nombre' => 'Queso Cheddar (gr)', 'unidad_medida' => 'GR', 'costo_unitario' => 30, 'stock_actual' => 5000, 'stock_minimo' => 500],
            ['nombre' => 'Nachos (bolsa)', 'unidad_medida' => 'UNI', 'costo_unitario' => 2500, 'stock_actual' => 50, 'stock_minimo' => 10],
            ['nombre' => 'Agua Mineral 500ml', 'unidad_medida' => 'UNI', 'costo_unitario' => 1000, 'stock_actual' => 48, 'stock_minimo' => 12],
        ];

        $insumosModels = [];
        foreach ($insumos as $i) {
            $insumosModels[$i['nombre']] = Insumo::updateOrCreate(
                ['nombre' => $i['nombre'], 'empresa_id' => $empresa->id],
                $i
            );
        }

        // 2. Productos Retail
        $productos = [
            ['nombre' => 'Perro Caliente Sencillo', 'precio' => 12000, 'categoria_id' => $catSnacks->id, 'gasto_operativo_fijo' => 500],
            ['nombre' => 'Perro Caliente Especial', 'precio' => 18000, 'categoria_id' => $catSnacks->id, 'gasto_operativo_fijo' => 800],
            ['nombre' => 'Crispetas Saladas G', 'precio' => 15000, 'categoria_id' => $catSnacks->id, 'gasto_operativo_fijo' => 300],
            ['nombre' => 'Crispetas Dulces M', 'precio' => 10000, 'categoria_id' => $catSnacks->id, 'gasto_operativo_fijo' => 300],
            ['nombre' => 'Combo Pareja XL', 'precio' => 35000, 'categoria_id' => $catSnacks->id, 'gasto_operativo_fijo' => 1200],
            ['nombre' => 'Nachos con Queso', 'precio' => 14000, 'categoria_id' => $catSnacks->id, 'gasto_operativo_fijo' => 400],
            ['nombre' => 'Gaseosa 16oz', 'precio' => 6000, 'categoria_id' => $catBebidas->id, 'gasto_operativo_fijo' => 100],
            ['nombre' => 'Agua Mineral', 'precio' => 4500, 'categoria_id' => $catBebidas->id, 'gasto_operativo_fijo' => 50],
            ['nombre' => 'Hot Dog Combo', 'precio' => 22000, 'categoria_id' => $catSnacks->id, 'gasto_operativo_fijo' => 1000],
            ['nombre' => 'Sandwich Pollo', 'precio' => 16000, 'categoria_id' => $catSnacks->id, 'gasto_operativo_fijo' => 1500],
        ];

        foreach ($productos as $p) {
            $prodModel = Producto::updateOrCreate(
                ['nombre' => $p['nombre'], 'empresa_id' => $empresa->id],
                array_merge($p, [
                    'presentacione_id' => $presUni->id,
                    'tipo_impuesto' => 'IMPOCONSUMO',
                    'porcentaje_impuesto' => 8
                ])
            );

            // Asignar Receta Básica
            if ($p['nombre'] == 'Perro Caliente Sencillo') {
                $prodModel->insumos()->sync([
                    $insumosModels['Pan Perro']->id => ['cantidad' => 1, 'unidad_medida' => 'UNI'],
                    $insumosModels['Salchicha Americana']->id => ['cantidad' => 1, 'unidad_medida' => 'UNI'],
                ]);
            }
            if (str_contains($p['nombre'], 'Crispetas')) {
                $prodModel->insumos()->sync([
                    $insumosModels['Maíz Pira (kg)']->id => ['cantidad' => 0.150, 'unidad_medida' => 'KG'],
                    $insumosModels['Aceite Vegetal (lt)']->id => ['cantidad' => 0.050, 'unidad_medida' => 'LT'],
                ]);
            }
        }
    }
}
