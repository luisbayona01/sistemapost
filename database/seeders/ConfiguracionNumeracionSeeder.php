<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ConfiguracionNumeracion;

class ConfiguracionNumeracionSeeder extends Seeder
{
    public function run(): void
    {
        // Documento Equivalente
        ConfiguracionNumeracion::create([
            'empresa_id' => 1,
            'tipo' => 'DE',
            'prefijo' => 'POS001',
            'consecutivo_actual' => 0,
            'consecutivo_inicial' => 1,
            'consecutivo_final' => null, // Sin límite
            'activo' => true,
            'notas' => 'Numeración para Documentos Equivalentes (POS)',
        ]);

        // Factura Electrónica (ejemplo, ajustar con resolución real)
        ConfiguracionNumeracion::create([
            'empresa_id' => 1,
            'tipo' => 'FE',
            'prefijo' => 'FE01',
            'consecutivo_actual' => 0,
            'consecutivo_inicial' => 1,
            'consecutivo_final' => 5000, // Ejemplo de resolución
            'resolucion_numero' => '18764123456789', // Cambiar por real
            'resolucion_fecha' => '2025-01-15',
            'resolucion_fecha_inicio' => '2025-01-15',
            'resolucion_fecha_fin' => '2026-01-15',
            'activo' => true,
            'notas' => 'Resolución DIAN para Facturación Electrónica',
        ]);
    }
}
