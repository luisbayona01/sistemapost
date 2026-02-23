<?php

namespace App\Interfaces;

use App\Models\DocumentoFiscal;
use App\Models\Venta;

interface FiscalProviderInterface
{
    /**
     * Enviar el documento al proveedor DIAN
     */
    public function enviarDocumento(DocumentoFiscal $documento): array;

    /**
     * Consultar el estado de un documento emitido
     */
    public function consultarEstado(DocumentoFiscal $documento): array;

    /**
     * Descargar el XML del documento
     */
    public function descargarXml(DocumentoFiscal $documento): string;

    /**
     * Descargar el PDF del documento
     */
    public function descargarPdf(DocumentoFiscal $documento): string;
}
