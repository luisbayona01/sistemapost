<?php

namespace App\Exports;

use App\Models\Producto;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductsExport implements FromCollection, WithHeadings, WithTitle, WithMapping
{
    private $empresaId;

    public function __construct($empresaId)
    {
        $this->empresaId = $empresaId;
    }

    public function collection()
    {
        return Producto::where('empresa_id', $this->empresaId)
            ->where('es_venta_retail', true)
            ->with(['categoria.caracteristica', 'presentacione.caracteristica', 'marca.caracteristica'])
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nombre',
            'Categoría',
            'Unidad',
            'Precio',
            'Costo',
            'Proveedor',
            'Stock Mínimo',
            'Código Interno',
            'Estado'
        ];
    }

    public function map($producto): array
    {
        return [
            $producto->id,
            $producto->nombre,
            $producto->categoria->caracteristica->nombre ?? 'N/A',
            $producto->presentacione->caracteristica->nombre ?? 'N/A',
            $producto->precio,
            $producto->costo_total_unitario,
            $producto->marca->caracteristica->nombre ?? 'N/A',
            $producto->stock_minimo,
            $producto->codigo,
            $producto->estado ? 'Activo' : 'Inactivo'
        ];
    }

    public function title(): string
    {
        return 'PRODUCTOS';
    }
}
