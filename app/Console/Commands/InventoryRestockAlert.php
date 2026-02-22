<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class InventoryRestockAlert extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory:restock-alert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera y notifica las sugerencias de compra semanal para cada empresa';

    protected $predictionService;

    public function __construct()
    {
        parent::__construct();
        $this->predictionService = app(\App\Services\Inventory\PredictionService::class);
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $empresas = \App\Models\Empresa::all();

        foreach ($empresas as $empresa) {
            $this->info("Analizando inventario para: " . $empresa->nombre);

            try {
                $plan = $this->predictionService->generarPlanComprasSemanal($empresa->id);

                if (count($plan) > 0) {
                    $this->warn(" -> Se detectaron " . count($plan) . " insumos críticos.");

                    // Aquí iría la lógica de notificación (Email/Slack/WhatsApp)
                    // Por ahora, registramos en log del sistema
                    \Illuminate\Support\Facades\Log::info("ALERTA DE ABASTECIMIENTO SEMANAL - Empresa: {$empresa->nombre}", [
                        'sugerencias' => $plan
                    ]);

                    // Simulación de "Disparo proactivo"
                    $headers = ["Insumo", "Stock", "Sugerido", "A Comprar"];
                    $data = collect($plan)->map(function ($item) {
                        return [
                            $item['insumo'],
                            $item['stock_actual'],
                            $item['sugerido_total'],
                            $item['compra_necesaria']
                        ];
                    })->toArray();

                    $this->table($headers, $data);
                } else {
                    $this->info(" -> Inventario saludable. No se requieren compras.");
                }

            } catch (\Exception $e) {
                $this->error("Error al procesar empresa {$empresa->id}: " . $e->getMessage());
            }
        }
    }
}
