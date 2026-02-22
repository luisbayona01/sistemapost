<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Identificar productos que son películas (tienen distribuidor o campos de cine)
        // Usamos raw SQL o DB Query Builder para no depender de modelos Eloquent
        $productosPelicula = DB::table('productos')
            ->whereNotNull('distribuidor_id')
            ->get();

        foreach ($productosPelicula as $prod) {
            // Verificar si la película ya existe (por título y empresa) para evitar duplicados en re-runs
            $existe = DB::table('peliculas')
                ->where('titulo', $prod->nombre)
                ->where('empresa_id', $prod->empresa_id)
                ->first();

            if (!$existe) {
                // Insertar en peliculas
                $peliculaId = DB::table('peliculas')->insertGetId([
                    'empresa_id' => $prod->empresa_id,
                    'titulo' => $prod->nombre,
                    // Mapeo seguro de campos, usando null coalesce por si no existen en objeto raw
                    'sinopsis' => $prod->sinopsis ?? null,
                    'duracion' => $prod->duracion ?? null,
                    'clasificacion' => $prod->clasificacion ?? null,
                    'genero' => $prod->genero ?? null,
                    'afiche' => $prod->imagen ?? null, // Asumiendo 'imagen' en productos
                    'trailer_url' => $prod->trailer_url ?? null,
                    'distribuidor_id' => $prod->distribuidor_id,
                    'fecha_estreno' => $prod->fecha_estreno ?? null,
                    'fecha_fin_exhibicion' => $prod->fecha_fin_exhibicion ?? null,
                    'activo' => $prod->activo ?? true,
                    'created_at' => $prod->created_at,
                    'updated_at' => $prod->updated_at,
                ]);
            } else {
                $peliculaId = $existe->id;
            }

            // 2. Actualizar las funciones que apuntaban a este producto
            DB::table('funciones')
                ->where('producto_id', $prod->id)
                ->update(['pelicula_id' => $peliculaId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No es fácil revertir la migración de datos sin backup
        // Pero podriamos vaciar peliculas y nullificar pelicula_id en funciones
    }
};
