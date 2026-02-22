<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Pelicula;
use App\Models\Funcion;
use App\Models\Sala;
use App\Models\Empresa;
use App\Models\Producto;
use App\Models\Categoria;

class CinemaSeeder extends Seeder
{
    public function run()
    {
        // 1. Limpiar datos existentes (orden inverso para respetar FK)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('funcion_asientos')->truncate();
        DB::table('funcion_precio')->truncate(); // Si existe tabla pivote de precios
        DB::table('funciones')->truncate();
        DB::table('peliculas')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $empresa = Empresa::first();
        if (!$empresa) {
            $this->command->error('No hay empresa registrada. Ejecuta EmpresaSeeder primero.');
            return;
        }

        // 2. Generar JSON de Mapa de Asientos
        $mapaSala1 = $this->generarMapa_6x8();
        $mapaSala2 = $this->generarMapa_5x5();

        // 3. Crear Salas
        $sala1 = Sala::updateOrCreate(
            ['nombre' => 'Sala 1', 'empresa_id' => $empresa->id],
            [
                'capacidad' => 48,
                'configuracion_json' => $mapaSala1
            ]
        );

        $sala2 = Sala::updateOrCreate(
            ['nombre' => 'Sala 2', 'empresa_id' => $empresa->id],
            [
                'capacidad' => 25,
                'configuracion_json' => $mapaSala2
            ]
        );

        // 4. Crear Películas de Ejemplo
        $peliculasData = [
            [
                'titulo' => 'Avatar: El Camino del Agua',
                'sinopsis' => 'Jake Sully vive con su nueva familia en el planeta Pandora. Cuando una amenaza conocida regresa, deben trabajar juntos para mantenerse a salvo.',
                'duracion' => '192',
                'clasificacion' => 'PG-13',
                'genero' => 'Ciencia Ficción',
                'afiche' => null, // Dejamos null para probar el placeholder
                'empresa_id' => $empresa->id,
                'activo' => true
            ],
            [
                'titulo' => 'Super Mario Bros. La Película',
                'sinopsis' => 'Un fontanero llamado Mario viaja a través de un laberinto subterráneo con su hermano, Luigi, tratando de salvar a una princesa capturada.',
                'duracion' => '92',
                'clasificacion' => 'G',
                'genero' => 'Animación',
                'afiche' => null,
                'empresa_id' => $empresa->id,
                'activo' => true
            ],
            [
                'titulo' => 'John Wick 4',
                'sinopsis' => 'John Wick descubre un camino para derrotar a la Alta Mesa. Pero antes de poder ganar su libertad, Wick debe enfrentarse a un nuevo enemigo.',
                'duracion' => '169',
                'clasificacion' => 'R',
                'genero' => 'Acción',
                'afiche' => null,
                'empresa_id' => $empresa->id,
                'activo' => true
            ],
            [
                'titulo' => 'El Exorcista: Creyentes',
                'sinopsis' => 'Secuela de la película de 1973 sobre una niña de 12 años que es poseída por una entidad demoníaca misteriosa.',
                'duracion' => '121',
                'clasificacion' => 'R',
                'genero' => 'Terror',
                'afiche' => null,
                'empresa_id' => $empresa->id,
                'activo' => true
            ],
            [
                'titulo' => 'Oppenheimer',
                'sinopsis' => 'La historia del científico estadounidense J. Robert Oppenheimer y su papel en el desarrollo de la bomba atómica.',
                'duracion' => '180',
                'clasificacion' => 'R',
                'genero' => 'Drama',
                'afiche' => null,
                'empresa_id' => $empresa->id,
                'activo' => true
            ]
        ];

        $peliculasModels = [];
        foreach ($peliculasData as $data) {
            $peliculasModels[] = Pelicula::create($data);
        }

        // Asegurar que exista al menos una categoría y un producto para referencia legacy
        $categoria = Categoria::first();
        if (!$categoria) {
            // No podemos crear categoría fácilmente sin factories completos, buscamos una o la ignoramos si no es strict
            // Asumimos que existe si el sistema corre. Si no, creamos dummy con raw DB
            $catId = DB::table('categorias')->insertGetId([
                'empresa_id' => $empresa->id,
                'nombre' => 'General',
                'descripcion' => 'General',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } else {
            $catId = $categoria->id;
        }

        $productoDummy = Producto::first();
        if (!$productoDummy) {
            // Crear producto dummy raw para evitar problemas de modelo
            $prodId = DB::table('productos')->insertGetId([
                'empresa_id' => $empresa->id,
                'codigo' => 'DUMMY001',
                'nombre' => 'Producto Base Sistema',
                'precio' => 0,
                'categoria_id' => $catId,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $productoDummy = Producto::find($prodId);
        }

        // 5. Crear Funciones para Hoy y Mañana
        $horarios = ['14:00', '16:30', '19:00', '21:30'];
        $dias = [Carbon::today(), Carbon::tomorrow()];

        foreach ($dias as $dia) {
            foreach ($horarios as $index => $hora) {
                // Alternar películas y salas
                $pelicula = $peliculasModels[$index % count($peliculasModels)];
                $sala = ($index % 2 == 0) ? $sala1 : $sala2;

                $fechaHora = $dia->copy()->setTimeFromTimeString($hora);

                Funcion::create([
                    'empresa_id' => $empresa->id,
                    'pelicula_id' => $pelicula->id,
                    'sala_id' => $sala->id,
                    'fecha_hora' => $fechaHora,
                    'precio' => 5000 + ($index * 500),
                    'activo' => true
                ]);
            }
        }

        $this->command->info('CinemaSeeder completado: Películas y funciones creadas correctamente.');
    }

    private function generarMapa_6x8()
    {
        $mapa = [];
        $filas = ['A', 'B', 'C', 'D', 'E', 'F'];
        $cols = 8;

        foreach ($filas as $rowIndex => $letra) {
            for ($c = 1; $c <= $cols; $c++) {
                // Pasillo en columna 5
                if ($c == 5) {
                    $mapa[] = [
                        'fila' => $letra,
                        'col' => $c,
                        'tipo' => 'pasillo',
                        'num' => null
                    ];
                    continue;
                }

                // Ajustar número real de asiento saltando el pasillo
                $numReal = ($c > 5) ? $c - 1 : $c;

                $mapa[] = [
                    'fila' => $letra,
                    'col' => $c,
                    'tipo' => 'asiento',
                    'num' => (string) $numReal
                ];
            }
        }
        return $mapa;
    }

    private function generarMapa_5x5()
    {
        $mapa = [];
        $filas = ['A', 'B', 'C', 'D', 'E'];
        $cols = 5; // 4 seats + 1 aisle

        foreach ($filas as $letra) {
            for ($c = 1; $c <= $cols; $c++) {
                // Pasillo en columna 3 (Mitad de 4 asientos: 1,2 | 3,4 -> Columna 3 pasillo real)
                if ($c == 3) {
                    $mapa[] = [
                        'fila' => $letra,
                        'col' => $c,
                        'tipo' => 'pasillo',
                        'num' => null
                    ];
                    continue;
                }

                // Si estamos después del pasillo (col > 3), el número de asiento es c-1
                $numReal = ($c > 3) ? $c - 1 : $c;

                $mapa[] = [
                    'fila' => $letra,
                    'col' => $c,
                    'tipo' => 'asiento',
                    'num' => (string) $numReal
                ];
            }
        }
        return $mapa;
    }
}
