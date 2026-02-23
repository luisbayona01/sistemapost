<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PrecioEntrada;
use App\Models\Funcion;
use App\Models\Empresa;

class CinemaPricesSeeder extends Seeder
{
    public function run()
    {
        $empresas = Empresa::all();

        foreach ($empresas as $empresa) {
            // 1. Crear precios base para esta empresa
            $precios = [
                ['nombre' => 'General', 'precio' => 30000, 'activo' => true],
                ['nombre' => 'Niños/Adulto Mayor', 'precio' => 22000, 'activo' => true],
                ['nombre' => 'Promoción Miércoles', 'precio' => 15000, 'activo' => true],
            ];

            $createdPrecios = [];
            foreach ($precios as $p) {
                $createdPrecios[] = PrecioEntrada::updateOrCreate(
                    ['empresa_id' => $empresa->id, 'nombre' => $p['nombre']],
                    ['precio' => $p['precio'], 'activo' => $p['activo']]
                );
            }

            // 2. Asociar estos precios a TODAS las funciones de esta empresa
            $funciones = Funcion::withoutGlobalScope('empresa')
                ->where('empresa_id', $empresa->id)
                ->get();

            foreach ($funciones as $funcion) {
                // Sincronizar para evitar duplicados si se corre varias veces
                $funcion->precios()->sync(collect($createdPrecios)->pluck('id'));
            }
        }
    }
}
