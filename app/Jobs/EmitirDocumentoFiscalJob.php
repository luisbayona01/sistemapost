<?php

namespace App\Jobs;

use App\Models\Venta;
use App\Services\Fiscal\EmisionFiscalService;
use App\Interfaces\FiscalProviderInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class EmitirDocumentoFiscalJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;                    // reintentos automáticos
    public $backoff = [10, 30, 60, 120];  // backoff exponencial
    public $timeout = 120;                // 2 minutos máximo por intento

    protected $ventaId;
    protected $esContingencia = false;

    public function __construct(int $ventaId, bool $esContingencia = false)
    {
        $this->ventaId = $ventaId;
        $this->esContingencia = $esContingencia;
    }

    public function handle(EmisionFiscalService $service)
    {
        $venta = Venta::with(['detalles', 'documentoFiscal'])->findOrFail($this->ventaId);

        try {
            // Utilizamos la lógica actual que ya está blindada en el servicio
            $documento = $service->emitirDesdeVenta($venta);

            if ($documento && $venta->documentoFiscal) {
                // El servicio pudo haber actualizado el documento existente o creado uno nuevo
                Log::info("Job de Emisión Fiscal completado para Venta #{$venta->id} - Estado: {$documento->estado}");
            }

        } catch (\Throwable $e) {
            if ($this->attempts() >= $this->tries) {
                if ($venta->documentoFiscal) {
                    $venta->documentoFiscal->update(['estado' => 'contingencia_permanente']);
                }

                \Illuminate\Support\Facades\Log::channel('single')->critical(
                    "🚨 FALLO DEFINITIVO DIAN - Venta #{$venta->id}",
                    ['empresa_id' => $venta->empresa_id, 'error' => $e->getMessage()]
                );

                \App\Models\ActivityLog::create([
                    'empresa_id' => $venta->empresa_id,
                    'user_id' => $venta->user_id ?? 1,
                    'accion' => 'ERROR_FISCAL_CRITICO',
                    'detalles' => "Venta {$venta->id} requiere emisión manual de contingencia. DIAN no respondió."
                ]);
            }
            throw $e;
        }
    }
}
