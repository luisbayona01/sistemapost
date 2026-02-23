<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class ProductsTemplateExport implements FromCollection, WithHeadings, WithTitle
{
    public function collection()
    {
        return collect([
            [
                'nombre' => 'Ejemplo Producto 1',
                'categoria' => 'SNACKS',
                'unidad' => 'UND',
                'precio' => 5000, // REQUERIDO
                'costo' => 2000,
                'stock_inicial' => 100, // REQUERIDO
                'proveedor' => 'Proveedor A',
                'stock_minimo' => 10,
                'codigo_interno' => '', // OPCIONAL: Dejar vacío para autogenerar
                'observaciones' => 'Producto de prueba'
            ]
        ]);
    }

    public function headings(): array
    {
        return [
            'nombre', // * REQUERIDO
            'categoria',
            'unidad',
            'precio', // * REQUERIDO: Precio de Venta
            'costo', // Costo de Adquisición
            'stock_inicial', // * REQUERIDO
            'proveedor',
            'stock_minimo',
            'codigo_interno', // Opcional
            'observaciones'
        ];
    }

    public function title(): string
    {
        return 'PRODUCTOS (Llenar aquí)';
    }
}
