<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Producto, Insumo, Pelicula, Funcion, Sala, Categoria, Presentacione, Marca};

class FullSystemTestSeeder extends Seeder
{
    public function run(): void
    {
        $empresaId = auth()->user()->empresa_id ?? 1;

        // 1. Asegurar categorías y marcas básicas si no existen
        $caractSnacks = \App\Models\Caracteristica::firstOrCreate(['nombre' => 'Snacks', 'empresa_id' => $empresaId]);
        $categoria = Categoria::firstOrCreate(['caracteristica_id' => $caractSnacks->id, 'empresa_id' => $empresaId]);

        $caractUnidad = \App\Models\Caracteristica::firstOrCreate(['nombre' => 'Unidad', 'empresa_id' => $empresaId]);
        $presentacion = Presentacione::firstOrCreate(['caracteristica_id' => $caractUnidad->id, 'empresa_id' => $empresaId]);

        $caractMarca = \App\Models\Caracteristica::firstOrCreate(['nombre' => 'Generica', 'empresa_id' => $empresaId]);
        $marca = Marca::firstOrCreate(['caracteristica_id' => $caractMarca->id, 'empresa_id' => $empresaId]);

        // 2. Crear insumos con stock
        $insumoMaiz = Insumo::updateOrCreate(
            ['nombre' => 'Maíz Pira', 'empresa_id' => $empresaId],
            ['stock_actual' => 100, 'stock_minimo' => 20, 'costo_unitario' => 5000, 'unidad_medida' => 'KG']
        );
        $insumoAceite = Insumo::updateOrCreate(
            ['nombre' => 'Aceite', 'empresa_id' => $empresaId],
            ['stock_actual' => 50, 'stock_minimo' => 10, 'costo_unitario' => 8000, 'unidad_medida' => 'KG']
        );

        // 3. Crear productos con recetas
        $palomitas = Producto::updateOrCreate(
            ['nombre' => 'Palomitas Grandes', 'empresa_id' => $empresaId],
            [
                'precio' => 15000,
                'stock_actual' => 100,
                'es_venta_retail' => true,
                'categoria_id' => $categoria->id,
                'presentacione_id' => $presentacion->id,
                'marca_id' => $marca->id,
            ]
        );

        // Vincular con insumos (receta)
        if ($palomitas->insumos()->count() == 0) {
            $palomitas->insumos()->attach([
                $insumoMaiz->id => ['cantidad' => 0.2, 'unidad_medida' => 'KG'],
                $insumoAceite->id => ['cantidad' => 0.05, 'unidad_medida' => 'KG'],
            ]);
        }

        // 4. Crear películas y funciones
        $pelicula = Pelicula::updateOrCreate(
            ['titulo' => 'Película de Prueba', 'empresa_id' => $empresaId],
            [
                'duracion' => 120,
                'clasificacion' => '+13',
            ]
        );

        $sala = Sala::where('empresa_id', $empresaId)->first();
        if (!$sala) {
            $sala = Sala::create(['nombre' => 'Sala 1', 'capacidad_total' => 50, 'empresa_id' => $empresaId]);
        }

        $funcion = Funcion::create([
            'pelicula_id' => $pelicula->id,
            'sala_id' => $sala->id,
            'fecha_hora' => now()->addHours(2),
            'precio' => 10000,
            'empresa_id' => $empresaId,
            'activo' => true,
        ]);

        // Generar asientos para la función si no existen
        // (Asumiendo que hay un generador o lo hacemos manual)
        for ($i = 1; $i <= 10; $i++) {
            \App\Models\FuncionAsiento::create([
                'funcion_id' => $funcion->id,
                'codigo_asiento' => 'A' . $i,
                'estado' => 'DISPONIBLE',
            ]);
        }

        $this->command->info('✅ Datos de prueba creados');
    }
}
