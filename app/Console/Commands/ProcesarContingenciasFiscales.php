<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ContingenciaFiscalService;

class ProcesarContingenciasFiscales extends Command
{
    protected $signature = 'fiscal:procesar-contingencias';
    protected $description = 'Procesa documentos fiscales en contingencia pendientes';

    public function handle(ContingenciaFiscalService $servicio)
    {
        $this->info('🔄 Procesando documentos en contingencia...');

        $resultados = $servicio->procesarDocumentosEnContingencia();

        $this->info("✅ Procesados: {$resultados['procesados']}");
        $this->info("✅ Exitosos: {$resultados['exitosos']}");
        $this->error("❌ Fallidos: {$resultados['fallidos']}");

        return 0;
    }
}
