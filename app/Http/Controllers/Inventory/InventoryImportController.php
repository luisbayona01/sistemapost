<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductImport;
use App\Imports\InsumoImport;
use App\Exports\InventoryTemplateExport;
use App\Models\ActivityLog;
use App\Services\ActivityLogService;

class InventoryImportController extends Controller
{
    public function show()
    {
        return view('admin.inventario-avanzado.import.show');
    }

    public function downloadTemplate()
    {
        return Excel::download(new InventoryTemplateExport, 'plantilla_inventario.xlsx');
    }

    public function exportCurrent()
    {
        $empresaId = auth()->user()->empresa_id;
        return Excel::download(new \App\Exports\InventoryFullExport($empresaId), 'inventario_actual_' . now()->format('Y-m-d') . '.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        $empresaId = auth()->user()->empresa_id;
        $file = $request->file('file');

        try {
            // Importar productos de la primera hoja
            Excel::import(new ProductImport($empresaId), $file, null, \Maatwebsite\Excel\Excel::XLSX);

            // Importar insumos de la segunda hoja (si el usuario la llenó correctamente)
            // Nota: Para importar de hojas específicas necesitamos usar WithMultipleSheets en el Import también
            // Pero por simplicidad en esta fase, podemos instruir al usuario o hacer que el Import maneje las hojas.

            // Mejorar: Usar un Import que extienda WithMultipleSheets
            Excel::import(
                new class ($empresaId) implements \Maatwebsite\Excel\Concerns\WithMultipleSheets {
                private $empresaId;
                public function __construct($empresaId)
                {
                    $this->empresaId = $empresaId; }
                public function sheets(): array
                {
                    return [
                    0 => new ProductImport($this->empresaId),
                    1 => new InsumoImport($this->empresaId),
                    ];
                }
                },
                $file
            );

            ActivityLogService::log('Importación Masiva', 'Inventario', [
                'filename' => $file->getClientOriginalName(),
                'empresa_id' => $empresaId
            ]);

            return redirect()->route('inventario-avanzado.index')->with('success', 'Inventario importado correctamente.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al importar: ' . $e->getMessage());
        }
    }
}
