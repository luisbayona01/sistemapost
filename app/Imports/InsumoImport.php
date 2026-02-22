<?php

namespace App\Imports;

use App\Models\Insumo;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Str;

class InsumoImport implements ToModel, WithHeadingRow, WithValidation
{
    private $empresaId;

    public function __construct($empresaId)
    {
        $this->empresaId = $empresaId;
    }

    public function model(array $row)
    {
        $nombre = $row['nombre'] ?? 'Insumo Genérico ' . Str::random(5);

        // Evitar duplicados
        $existente = Insumo::where('nombre', $nombre)
            ->where('empresa_id', $this->empresaId)
            ->first();

        if ($existente) {
            return null;
        }

        return new Insumo([
            'empresa_id' => $this->empresaId,
            'nombre' => $nombre,
            'codigo' => $row['codigo_interno'] ?? null,
            'unidad_medida' => strtoupper($row['unidad'] ?? 'UND'),
            'costo_unitario' => $row['costo'] ?? 0,
            'stock_actual' => $row['stock_inicial'] ?? 0,
            'stock_minimo' => $row['stock_minimo'] ?? 0,
        ]);
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string',
        ];
    }
}
