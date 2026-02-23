<?php

namespace App\Console\Commands;

use App\Models\Categoria;
use App\Models\Funcion;
use App\Models\FuncionAsiento;
use App\Models\SalaAsiento;
use App\Models\Producto;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FixCinemaIntegrity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:cinema-integrity';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Repara integridad de asientos de funciones (Sala 2) y unifica categorías duplicadas (Tragos).';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando proceso de reparación de integridad... 🛠️');

        $this->fixFuncionAsientos();
        $this->fixDuplicatedCategories();

        $this->info('¡Proceso completado exitosamente! ✅');
        return 0;
    }

    private function fixFuncionAsientos()
    {
        $this->info('--- Diagnóstico de Integridad de Asientos (Sala 2) ---');

        // 1. Obtener funciones futuras o recientes (limite razonable para no barrer todo el historial)
        $funciones = Funcion::where('fecha_hora', '>=', now()->subDays(7))->get();
        $fixedCount = 0;

        foreach ($funciones as $funcion) {
            $count = FuncionAsiento::where('funcion_id', $funcion->id)->count();

            if ($count === 0) {
                $this->warn("Función ID {$funcion->id} (Sala {$funcion->sala_id}) no tiene asientos generados. Reparando...");

                // Obtener plantilla de asientos de la sala
                $sala = \App\Models\Sala::find($funcion->sala_id);

                if (!$sala || empty($sala->configuracion_json)) {
                    $this->error("¡ERROR! La Sala {$funcion->sala_id} no tiene configuración JSON definida. No se puede reparar función {$funcion->id}.");
                    continue;
                }

                $mapa = $sala->configuracion_json; // Cast array automágico por el modelo
                if (is_string($mapa)) {
                    $mapa = json_decode($mapa, true);
                }

                DB::transaction(function () use ($funcion, $mapa) {
                    $nuevosAsientos = [];
                    foreach ($mapa as $seat) {
                        // Validar estructura del asiento
                        if (!isset($seat['tipo']) || $seat['tipo'] !== 'asiento') {
                            continue;
                        }

                        // Generar código (A1, B2...)
                        $codigo = ($seat['fila'] ?? 'A') . ($seat['num'] ?? '1');

                        $nuevosAsientos[] = [
                            'funcion_id' => $funcion->id,
                            'codigo_asiento' => $codigo,
                            'estado' => 'DISPONIBLE', // Usar string directo si constante no accesible
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }

                    // Insertar en lotes
                    foreach (array_chunk($nuevosAsientos, 100) as $chunk) {
                        FuncionAsiento::insert($chunk);
                    }
                });

                $fixedCount++;
                $this->info("✔ Función {$funcion->id} reparada: asientos generados desde JSON.");
            }
        }

        if ($fixedCount === 0) {
            $this->info("Todas las funciones activas tienen asientos. No se requirieron correcciones.");
        } else {
            $this->info("Total de funciones reparadas: {$fixedCount}");
        }
    }

    private function fixDuplicatedCategories()
    {
        $this->info('--- Unificación de Categorías Duplicadas (Tragos) ---');

        $categorias = Categoria::all();
        $grouped = $categorias->groupBy(function ($item) {
            return strtolower(trim($item->nombre));
        });

        foreach ($grouped as $nombre => $group) {
            if ($group->count() > 1) {
                $this->warn("Encontradas " . $group->count() . " categorías para '{$nombre}': " . $group->pluck('id')->implode(', '));

                // 1. Identificar la principal (la que tenga más productos o la más antigua)
                // Estrategia: Quedarse con la primera creada (ID más bajo)
                $principal = $group->sortBy('id')->first();
                $duplicados = $group->where('id', '!=', $principal->id);

                DB::transaction(function () use ($principal, $duplicados) {
                    foreach ($duplicados as $duplicado) {
                        // Mover productos
                        $affected = Producto::where('categoria_id', $duplicado->id)->update(['categoria_id' => $principal->id]);
                        if ($affected > 0) {
                            $this->info("Movidos {$affected} productos de Cat ID {$duplicado->id} a {$principal->id}.");
                        }

                        // Eliminar duplicado
                        $duplicado->delete();
                        $this->info("Categoría duplicada ID {$duplicado->id} eliminada.");
                    }
                });

                $this->info("✔ Unificación completada para '{$nombre}'. Principal: ID {$principal->id}");
            }
        }
    }
}
