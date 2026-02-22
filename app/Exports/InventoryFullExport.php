<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class InventoryFullExport implements WithMultipleSheets
{
    private $empresaId;

    public function __construct($empresaId)
    {
        $this->empresaId = $empresaId;
    }

    public function sheets(): array
    {
        return [
            new ProductsExport($this->empresaId),
            new InsumosExport($this->empresaId),
        ];
    }
}
