<?php

namespace App\Console\Commands;

use App\Models\Producto;
use Illuminate\Console\Command;

class RecalcularRentabilidadProductos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'productos:recalcular-rentabilidad {--empresa_id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalcula la rentabilidad de todos los productos';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $empresaId = $this->option('empresa_id');

        $query = Producto::with(['insumos', 'empresa']);

        if ($empresaId) {
            $query->where('empresa_id', $empresaId);
        }

        $productos = $query->get();
        $count = $productos->count();

        $this->info("Recalculando rentabilidad de {$count} productos...");

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        foreach ($productos as $producto) {
            if ($producto->insumos()->exists()) {
                $producto->calcularRentabilidad();
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('✓ Rentabilidad recalculada exitosamente');

        return self::SUCCESS;
    }
}
