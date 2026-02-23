<?php

namespace App\Http\Controllers;

use App\Models\Inventario;
use App\Models\Kardex;
use App\Models\Producto;
use App\Enums\TipoTransaccionEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventarioCargaMasivaController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:Root|Gerente|administrador']);
    }

    /**
     * Vista de carga masiva
     */
    public function index()
    {
        return view('inventario.carga-masiva');
    }

    /**
     * Descargar plantilla CSV
     */
    public function descargarPlantilla()
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="plantilla_inventario.csv"',
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');
            // BOM para Excel en Windows
            fputs($handle, "\xEF\xBB\xBF");
            // Encabezados
            fputcsv($handle, ['codigo_producto', 'cantidad', 'motivo']);
            // Filas de ejemplo
            fputcsv($handle, ['PRO-0001', '100', 'Compra semanal']);
            fputcsv($handle, ['PRO-0002', '50', 'Reposición inventario']);
            fputcsv($handle, ['PRO-0003', '200', 'Carga inicial']);
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Procesar el CSV subido
     */
    public function procesar(Request $request)
    {
        $request->validate([
            'archivo_csv' => 'required|file|mimes:csv,txt|max:2048',
        ], [
            'archivo_csv.required' => 'Debes subir un archivo CSV.',
            'archivo_csv.mimes' => 'Solo se aceptan archivos .csv',
        ]);

        $empresaId = auth()->user()->empresa_id;
        $file = $request->file('archivo_csv');
        $handle = fopen($file->getRealPath(), 'r');

        // Saltar encabezado
        fgetcsv($handle);

        $procesados = 0;
        $errores = [];
        $linea = 1;

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle)) !== false) {
                $linea++;

                // Ignorar líneas vacías
                if (empty(array_filter($row)))
                    continue;

                [$codigo, $cantidad, $motivo] = array_pad($row, 3, '');

                $codigo = trim($codigo);
                $cantidad = floatval(trim($cantidad));
                $motivo = trim($motivo) ?: 'Carga masiva desde CSV';

                if (!$codigo || $cantidad <= 0) {
                    $errores[] = "Línea $linea: Código '$codigo' inválido o cantidad $cantidad ≤ 0. Omitida.";
                    continue;
                }

                $producto = Producto::where('codigo', $codigo)
                    ->where('empresa_id', $empresaId)
                    ->first();

                if (!$producto) {
                    $errores[] = "Línea $linea: Producto '$codigo' no encontrado en esta empresa.";
                    continue;
                }

                // Actualizar o crear registro de inventario
                $inventario = Inventario::firstOrNew(['producto_id' => $producto->id]);
                $inventario->cantidad = ($inventario->cantidad ?? 0) + $cantidad;
                $inventario->empresa_id = $empresaId;
                $inventario->save();

                // Registrar en Kardex
                (new Kardex())->crearRegistro([
                    'producto_id' => $producto->id,
                    'cantidad' => $cantidad,
                    'costo_unitario' => $producto->precio ?? 0,
                    'descripcion' => "Carga masiva CSV — $motivo",
                ], TipoTransaccionEnum::Compra);

                $procesados++;
            }

            fclose($handle);
            DB::commit();

            $msg = "$procesados producto(s) actualizados correctamente.";
            if (!empty($errores)) {
                $msg .= ' Con ' . count($errores) . ' advertencia(s).';
            }

            return back()
                ->with('success', $msg)
                ->with('carga_errores', $errores);

        } catch (\Throwable $e) {
            DB::rollBack();
            fclose($handle);
            Log::error('Carga masiva inventario fallida', ['error' => $e->getMessage()]);
            return back()->with('error', 'Error al procesar el archivo: ' . $e->getMessage());
        }
    }
}
