<?php

namespace App\Services\Fiscal\Providers;

use App\Interfaces\FiscalProviderInterface;
use App\Models\DocumentoFiscal;

class NullFiscalProvider implements FiscalProviderInterface
{
    /**
     * Enviar el documento al proveedor DIAN (Dummy / Simulado)
     */
    public function enviarDocumento(DocumentoFiscal $documento): array
    {
        // Simplemente devuelve contingencia inmediata sin error real
        return [
            'success' => true,
            'cufe' => null,
            'cude' => null,
            'pdf_path' => null,
            'xml_path' => null,
            'mensaje' => 'Emisión en contingencia - proveedor pendiente de configuración',
        ];
    }

    /**
     * Consultar el estado de un documento emitido (Dummy / Simulado)
     */
    public function consultarEstado(DocumentoFiscal $documento): array
    {
        return [
            'success' => true,
            'estado' => 'contingencia'
        ];
    }

    /**
     * Descargar el XML del documento (Dummy / Simulado)
     */
    public function descargarXml(DocumentoFiscal $documento): string
    {
        return '';
    }

    /**
     * Descargar el PDF del documento (Dummy / Simulado)
     */
    public function descargarPdf(DocumentoFiscal $documento): string
    {
        return '';
    }
}
