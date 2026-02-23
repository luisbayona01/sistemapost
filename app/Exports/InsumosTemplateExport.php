<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class InsumosTemplateExport implements FromCollection, WithHeadings, WithTitle
{
    public function collection()
    {
        return collect([
            [
                'nombre' => 'Ejemplo Insumo 1',
                'unidad' => 'KG',
                'costo' => 1500,
                'stock_inicial' => 50,
                'stock_minimo' => 5,
                'codigo_interno' => 'INS-001',
                'observaciones' => 'Ingrediente de prueba'
            ]
        ]);
    }

    public function headings(): array
    {
        return [
            'nombre',
            'unidad',
            'costo',
            'stock_inicial',
            'stock_minimo',
            'codigo_interno',
            'observaciones'
        ];
    }

    public function title(): string
    {
        return 'INSUMOS';
    }
}
