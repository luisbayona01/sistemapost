<?php

namespace App\Services\Fiscal\Providers;

use App\Interfaces\FiscalProviderInterface;
use App\Models\DocumentoFiscal;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AlegraProvider implements FiscalProviderInterface
{
    protected $baseUrl;
    protected $auth;

    public function __construct()
    {
        $config = config('fiscal.providers.alegra');
        $this->baseUrl = $config['endpoint'];
        $this->auth = base64_encode($config['username'] . ':' . $config['token']);
    }

    public function enviarDocumento(DocumentoFiscal $documento): array
    {
        try {
            // Mapeo al formato de Alegra API
            $payload = $this->mapearPayload($documento);

            Log::debug("Enviando a Alegra: " . json_encode($payload));

            // Si es ambiente de desarrollo o no hay credenciales, simulamos éxito
            if (config('app.env') === 'local' && empty(config('fiscal.providers.alegra.token'))) {
                return [
                    'success' => true,
                    'cufe' => 'MOCK-CUFE-' . uniqid(),
                    'xml_path' => 'fiscal/xml/' . $documento->numero_completo . '.xml',
                    'pdf_path' => 'fiscal/pdf/' . $documento->numero_completo . '.pdf',
                ];
            }

            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $this->auth,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . 'invoices', $payload);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'cufe' => $data['cufe'] ?? $data['id'],
                    'id_externo' => $data['id'],
                ];
            }

            return [
                'success' => false,
                'error' => $response->body(),
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function consultarEstado(DocumentoFiscal $documento): array
    {
        return ['status' => 'ACCEPTED'];
    }

    public function descargarXml(DocumentoFiscal $documento): string
    {
        return "";
    }

    public function descargarPdf(DocumentoFiscal $documento): string
    {
        return "";
    }

    private function mapearPayload(DocumentoFiscal $doc): array
    {
        $items = [];
        foreach ($doc->lineas as $linea) {
            $items[] = [
                'id' => $linea->codigo,
                'name' => $linea->descripcion,
                'price' => $linea->precio_unitario,
                'quantity' => $linea->cantidad,
                'tax' => $linea->aplica_inc ? [['id' => 'INC', 'percentage' => 8]] : [],
            ];
        }

        return [
            'date' => $doc->created_at->format('Y-m-d'),
            'dueDate' => $doc->created_at->format('Y-m-d'),
            'client' => [
                'id' => $doc->cliente_documento,
                'name' => $doc->cliente_nombre,
                'identification' => $doc->cliente_documento,
            ],
            'items' => $items,
            'numberTemplate' => [
                'prefix' => $doc->prefijo,
                'number' => $doc->numero,
            ],
            'type' => $doc->tipo_documento === 'FE' ? 'electronic_invoice' : 'pos_invoice',
        ];
    }
}
