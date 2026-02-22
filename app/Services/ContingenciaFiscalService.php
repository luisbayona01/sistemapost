<?php

namespace App\Services;

use App\Models\DocumentoFiscal;
use Illuminate\Support\Facades\Log;

class ContingenciaFiscalService
{
    /**
     * Activar modo contingencia para un documento
     */
    public function activarContingencia(DocumentoFiscal $documento, string $motivo): void
    {
        $documento->marcarComoContingencia($motivo);

        Log::warning('Documento en contingencia', [
            'documento_id' => $documento->id,
            'numero' => $documento->numero_completo,
            'motivo' => $motivo,
        ]);

        // TODO: Notificar al gerente (email/SMS)
    }

    /**
     * Procesar documentos en contingencia pendientes
     */
    public function procesarDocumentosEnContingencia(): array
    {
        $documentos = DocumentoFiscal::enContingencia()
            ->where('intentos_envio', '<', 3)
            ->orderBy('created_at')
            ->limit(50)
            ->get();

        $resultados = [
            'procesados' => 0,
            'exitosos' => 0,
            'fallidos' => 0,
        ];

        foreach ($documentos as $documento) {
            $resultados['procesados']++;

            try {
                // Intentar reenviar
                // Nota: EmisionFiscalService se implementará en la Parte 2.
                // Usamos app() para resolución diferida.
                if (class_exists(\App\Services\EmisionFiscalService::class)) {
                    $servicio = app(EmisionFiscalService::class);
                    $servicio->reenviarDocumento($documento);
                    $resultados['exitosos']++;
                    Log::info('Documento contingencia reenviado exitosamente', ['documento_id' => $documento->id]);
                } else {
                    throw new \Exception("EmisionFiscalService no encontrado (se implementará en Parte 2)");
                }

            } catch (\Exception $e) {
                $documento->incrementarIntentos();
                $resultados['fallidos']++;

                Log::error('Fallo al reenviar documento en contingencia', [
                    'documento_id' => $documento->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $resultados;
    }

    /**
     * Verificar salud del sistema fiscal
     */
    public function verificarSaludSistema(): array
    {
        return [
            'dian_alcanzable' => $this->pingDIAN(),
            'proveedor_disponible' => $this->pingProveedor(),
            'resolucion_vigente' => $this->verificarResolucion(),
            'certificado_valido' => $this->verificarCertificado(),
            'contingencias_activas' => DocumentoFiscal::enContingencia()->count(),
        ];
    }

    private function pingDIAN(): bool
    {
        // TODO: Implementar ping real a DIAN
        return true;
    }

    private function pingProveedor(): bool
    {
        // TODO: Implementar ping al proveedor (Siigo/Alegra)
        return true;
    }

    private function verificarResolucion(): bool
    {
        $config = \App\Models\ConfiguracionNumeracion::where('tipo', 'FE')
            ->where('activo', true)
            ->first();

        if (!$config)
            return false;

        return !$config->estaProximaAVencer(15);
    }

    private function verificarCertificado(): bool
    {
        // TODO: Verificar vigencia del certificado digital
        return true;
    }
}
