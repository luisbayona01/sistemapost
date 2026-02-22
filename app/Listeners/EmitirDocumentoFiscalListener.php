<?php

namespace App\Listeners;

use App\Events\CreateVentaEvent;
use App\Services\Fiscal\EmisionFiscalService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class EmitirDocumentoFiscalListener implements ShouldQueue
{
    use InteractsWithQueue;

    protected $emisionService;

    /**
     * Create the event listener.
     */
    public function __construct(EmisionFiscalService $emisionService)
    {
        $this->emisionService = $emisionService;
    }

    /**
     * Handle the event.
     */
    public function handle(CreateVentaEvent $event): void
    {
        $venta = $event->venta;

        // Verificar si la emisión automática está activa
        if (!config('fiscal.emision_automatica', true)) {
            return;
        }

        Log::info("Iniciando emisión fiscal para Venta #{$venta->id}");

        $documento = $this->emisionService->emitirDesdeVenta($venta);

        if ($documento) {
            Log::info("Venta #{$venta->id} procesada fiscalmente: {$documento->numero_completo} ({$documento->estado})");
        } else {
            Log::error("No se pudo generar documento fiscal para Venta #{$venta->id}");
        }
    }
}
