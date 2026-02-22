<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Producto;
use App\Models\Funcion;
use App\Models\Distribuidor;
use App\Models\Empresa;
use App\Models\Sala;
use App\Models\Categoria;
use App\Models\Inventario;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TestDataSeeder extends Seeder
{
    public function run()
    {
        $empresaId = Empresa::value('id') ?? 1;
        $salaId = Sala::value('id') ?? 1;
        $categoriaId = Categoria::value('id') ?? 1;
        $presentacioneId = 1;
        $marcaId = 1;
        $ubicacioneId = DB::table('ubicaciones')->value('id');

        // 1. Crear Distribuidor si no existe
        $distribuidor = Distribuidor::firstOrCreate(
            ['nombre' => 'Warner Bros Test'],
            ['empresa_id' => $empresaId, 'contacto' => 'Test Contact']
        );

        // 2. Crear 3 Películas (como productos con distribuidor)
        $peliculasData = [
            [
                'nombre' => 'Avatar: El Camino del Agua',
                'precio' => 30000,
                'genero' => 'Ciencia Ficcion',
                'duracion' => '192 min',
                'clasificacion' => 'PG-13'
            ],
            [
                'nombre' => 'Super Mario Bros. La Película',
                'precio' => 25000,
                'genero' => 'Infantil',
                'duracion' => '92 min',
                'clasificacion' => 'G'
            ],
            [
                'nombre' => 'John Wick: Capítulo 4',
                'precio' => 28000,
                'genero' => 'Accion',
                'duracion' => '169 min',
                'clasificacion' => '+18'
            ]
        ];

        foreach ($peliculasData as $pd) {
            $pelicula = Producto::create([
                'empresa_id' => $empresaId,
                'distribuidor_id' => $distribuidor->id,
                'categoria_id' => $categoriaId,
                'presentacione_id' => $presentacioneId,
                'marca_id' => $marcaId,
                'nombre' => $pd['nombre'],
                'precio' => $pd['precio'],
                'genero' => $pd['genero'],
                'duracion' => $pd['duracion'],
                'clasificacion' => $pd['clasificacion'],
                'estado' => 1
            ]);

            // Crear Funciones en distintas horas
            $horas = ['14:00', '18:30', '21:00'];
            foreach ($horas as $hora) {
                Funcion::create([
                    'empresa_id' => $empresaId,
                    'sala_id' => $salaId,
                    'producto_id' => $pelicula->id,
                    'fecha_hora' => Carbon::today()->setTimeFromTimeString($hora),
                    'precio' => $pd['precio'],
                    'activo' => true
                ]);
            }
        }

        // 3. Crear 10 Productos de Confitería
        $confiteriaData = [
            ['nombre' => 'Combo Grande (Cotas + Gaseosa)', 'precio' => 35000, 'stock' => 50],
            ['nombre' => 'Crispetas de Sal Mediana', 'precio' => 12000, 'stock' => 8],
            ['nombre' => 'Gaseosa PET 600ml', 'precio' => 7000, 'stock' => 100],
            ['nombre' => 'Nachos con Queso', 'precio' => 15000, 'stock' => 20],
            ['nombre' => 'Hot Dog Americano', 'precio' => 18000, 'stock' => 15],
            ['nombre' => 'Chocolatina Jet Gigante', 'precio' => 5000, 'stock' => 5],
            ['nombre' => 'Agua Mineral 500ml', 'precio' => 4500, 'stock' => 80],
            ['nombre' => 'Combo Pareja (2 Gaseosas + Crispetas)', 'precio' => 45000, 'stock' => 30],
            ['nombre' => 'M&Ms Medianos', 'precio' => 9500, 'stock' => 25],
            ['nombre' => 'Crispetas Caramelo Grande', 'precio' => 18000, 'stock' => 12]
        ];

        foreach ($confiteriaData as $cd) {
            $producto = Producto::create([
                'empresa_id' => $empresaId,
                'categoria_id' => $categoriaId,
                'presentacione_id' => $presentacioneId,
                'marca_id' => $marcaId,
                'nombre' => $cd['nombre'],
                'precio' => $cd['precio'],
                'estado' => 1
            ]);

            // Asignar stock en inventario con ubicacione_id obligatorio
            Inventario::updateOrCreate(
                ['empresa_id' => $empresaId, 'producto_id' => $producto->id],
                [
                    'cantidad' => $cd['stock'],
                    'cantidad_minima' => 10,
                    'ubicacione_id' => $ubicacioneId
                ]
            );
        }
    }
}
