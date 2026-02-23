<?php

namespace App\Services;

use App\Models\Venta;
use App\Models\Empresa;
use Barryvdh\DomPDF\Facade\Pdf;

class TicketService
{
    /**
     * Genera un PDF de ticket térmico/comprobante
     * Es agnóstico al nicho, recibe la venta y la vista.
     */
    public function generarTicketPDF(Venta $venta, string $view = 'pdf.comprobante-venta')
    {
        $venta->load(['productos', 'cliente', 'paymentTransactions', 'user', 'asientosCinema.funcion.sala']);
        $empresa = $venta->empresa;

        // Si no tiene empresa vinculada (falla de seguridad previa), usamos la primera
        if (!$empresa) {
            $empresa = Empresa::first();
        }

        $pdf = Pdf::loadView($view, [
            'venta' => $venta,
            'empresa' => $empresa
        ]);

        // Formato ticket térmico (80mm o similar)
        // 226pt x 841pt es un estándar aproximado para 80mm
        $pdf->setPaper([0, 0, 226, 841], 'portrait');

        return $pdf;
    }
}
