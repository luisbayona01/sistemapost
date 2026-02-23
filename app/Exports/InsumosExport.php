<?php

namespace App\Exports;

use App\Models\Insumo;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;

class InsumosExport implements FromCollection, WithHeadings, WithTitle, WithMapping
{
    private $empresaId;

    public function __construct($empresaId)
    {
        $this->empresaId = $empresaId;
    }

    public function collection()
    {
        return Insumo::where('empresa_id', $this->empresaId)->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nombre',
            'Unidad',
            'Costo',
            'Stock Actual',
            'Stock Mínimo',
            'Código Interno'
        ];
    }

    public function map($insumo): array
    {
        return [
            $insumo->id,
            $insumo->nombre,
            $insumo->unidad_medida,
            $insumo->costo_unitario,
            $insumo->stock_actual,
            $insumo->stock_minimo,
            $insumo->codigo
        ];
    }

    public function title(): string
    {
        return 'INSUMOS';
    }
}
