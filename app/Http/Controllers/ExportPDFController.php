<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Venta;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Crypt;

class ExportPDFController extends Controller
{
    /**
     * Exportar en formato PDF el comprobante de venta
     */
    public function exportPdfComprobanteVenta(Request $request): Response
    {
        try {
            $id = Crypt::decrypt($request->id);
            $venta = Venta::with(['productos.presentacione', 'cliente.persona', 'user.empleado', 'comprobante', 'empresa.moneda', 'asientosCinema'])->findOrFail($id);
            $empresa = Empresa::first();

            // Lógica de Agrupación
            // Separar tickets de cinema vs productos de confitería
            // Cinema: venta con canal='ventanilla'
            // Confitería: productos normales

            $tickets = $venta->productos->filter(fn($p) => !$p->es_venta_retail);
            $snacks = $venta->productos->filter(fn($p) => $p->es_venta_retail);

            $groupedSnacks = $snacks->groupBy('id')->map(function ($group) {
                $first = $group->first();
                $first->pivot->cantidad = $group->sum('pivot.cantidad');
                return $first;
            });

            // Obtener asientos si existen
            $asientos = $venta->asientosCinema->pluck('columna', 'fila')->map(function ($col, $fila) {
                return "{$fila}{$col}";
            })->values()->implode(', ');

            // Si la info de asientos está en 'asientosCinema' (relación directa)
            // Revisar estructura de FuncionAsiento. Usualmente tiene 'letra_fila' y 'numero_columna' o 'codigo'
            if ($venta->asientosCinema->isNotEmpty()) {
                $asientos = $venta->asientosCinema->map(function ($asiento) {
                    // Asumiendo que el modelo FuncionAsiento tiene un método o atributo para el código
                    return $asiento->codigo ?? ($asiento->fila . $asiento->columna);
                })->implode(', ');
            } else {
                $asientos = '';
            }

            $pdf = Pdf::loadView('pdf.ticket-termico', [
                'venta' => $venta,
                'empresa' => $empresa,
                'tickets' => $tickets,
                'snacks' => $groupedSnacks,
                'asientos' => $asientos
            ]);

            // Configurar tamaño para térmica 80mm (Ancho: 226pt aprox, Alto: variable)
            $pdf->setPaper([0, 0, 226, 800], 'portrait');

            $filename = 'ticket-' . $venta->numero_comprobante . '.pdf';

            if ($request->has('download')) {
                return $pdf->download($filename);
            }

            return $pdf->stream($filename);

        } catch (\Exception $e) {
            // Global Error Handling (Pilar 2) fallback
            return response()->make("Error al generar el PDF: " . $e->getMessage(), 500);
        }
    }

    /**
     * Exportar en formato PDF el documento fiscal (DIAN)
     */
    public function exportPdfDocumentoFiscal(Request $request): Response
    {
        try {
            $id = Crypt::decrypt($request->id);
            $doc = \App\Models\DocumentoFiscal::with(['lineas', 'empresa'])->findOrFail($id);

            $pdf = Pdf::loadView('pdf.documento-fiscal', [
                'doc' => $doc
            ]);

            // Formato ticket térmico 80mm
            $pdf->setPaper([0, 0, 226, 600], 'portrait');

            return $pdf->stream('fiscal-' . $doc->numero_completo . '.pdf');

        } catch (\Exception $e) {
            return response()->make("Error al generar el PDF Fiscal: " . $e->getMessage(), 500);
        }
    }
}
