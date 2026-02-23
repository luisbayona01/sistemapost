<?php

namespace App\Imports;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Caracteristica;
use App\Models\Presentacione;
use App\Models\Marca;
use App\Models\Inventario;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Str;

class ProductImport implements ToModel, WithHeadingRow, WithValidation
{
    private $empresaId;

    public function __construct($empresaId)
    {
        $this->empresaId = $empresaId;
    }

    public function model(array $row)
    {
        $nombre = trim($row['nombre']);
        if (empty($nombre))
            return null;

        // Evitar duplicados
        $existente = Producto::where('nombre', $nombre)
            ->where('empresa_id', $this->empresaId)
            ->first();

        if ($existente) {
            return null; // Omitir duplicados silenciosamente o lanzar error según preferencia
        }

        // Buscar o crear Categoría
        $catName = $row['categoria'] ?? 'General';
        $caract = Caracteristica::firstOrCreate(
            ['nombre' => $catName, 'empresa_id' => $this->empresaId],
            ['estado' => 1]
        );
        $categoria = Categoria::firstOrCreate(
            ['caracteristica_id' => $caract->id, 'empresa_id' => $this->empresaId]
        );

        // Buscar o crear Presentación (Unidad)
        $presName = $row['unidad'] ?? 'UND';
        $presentacion = Presentacione::firstOrCreate(
            ['sigla' => strtoupper($presName), 'empresa_id' => $this->empresaId],
            ['nombre' => strtoupper($presName)]
        );

        // Marca / Proveedor (Opcional)
        $marcaName = $row['proveedor'] ?? 'Genérico';
        $marca = Marca::firstOrCreate(
            ['nombre' => $marcaName, 'empresa_id' => $this->empresaId]
        );

        // Generar código si no viene
        $codigo = $row['codigo_interno'] ?? null;
        if (empty($codigo)) {
            // Dejar null para que el Trait 'booted' del modelo lo genere,
            // O generarlo explícitamente aquí si el trait no es confiable en bulk imports.
            // Asumimos que el modelo lo maneja, pero por seguridad pasamos null.
        }

        $producto = Producto::create([
            'empresa_id' => $this->empresaId,
            'nombre' => $nombre,
            'codigo' => $codigo,
            'precio' => $row['precio'] ?? 0,
            'categoria_id' => $categoria->id,
            'presentacione_id' => $presentacion->id,
            'marca_id' => $marca->id,
            'es_venta_retail' => true,
            'gasto_operativo_fijo' => 0,
            'costo_total_unitario' => $row['costo'] ?? 0,
            'stock_minimo' => $row['stock_minimo'] ?? 5,
        ]);

        // Crear registro inicial de inventario con stock inicial si se provee
        $stockInicial = isset($row['stock_inicial']) ? (float) $row['stock_inicial'] : 0;

        Inventario::create([
            'producto_id' => $producto->id,
            'cantidad' => $stockInicial,
            'empresa_id' => $this->empresaId
        ]);

        return $producto;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string',
            'precio' => 'required|numeric|min:0',
            // 'costo' => 'required|numeric|min:0', // Recomendado pero opcional por ahora si el cliente así lo prefiere
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nombre.required' => 'El nombre del producto es obligatorio.',
            'precio.required' => 'El precio de venta es obligatorio.',
        ];
    }
}
