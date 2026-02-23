<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Pelicula;
use App\Models\Producto;
use App\Models\Insumo;
use App\Models\InsumoLote;
use App\Models\Inventario;
use App\Models\Caja;
use App\Models\Venta;
use App\Models\Movimiento;
use Illuminate\Support\Facades\DB;

class SystemStabilizer extends Command
{
    protected $signature = 'system:stabilize';
    protected $description = 'Limpia el sistema, usuarios y establece un ambiente de prueba controlado';

    public function handle()
    {
        $this->info("=== ESTABILIZACIÓN DEL SISTEMA INICIADA ===");

        try {
            DB::beginTransaction();

            // 1. Limpieza de Usuarios (Solo ID 1 sobrevive)
            $this->info("1. Limpiando usuarios...");
            User::where('id', '>', 1)->delete();
            $this->info("   Usuarios secundarios eliminados.");

            // 2. Limpieza de Datos de Cine
            $this->info("2. Limpiando catálogo de películas...");
            Pelicula::query()->delete(); // Cascada debería limpiar funciones y asientos
            $this->info("   Catálogo de películas reseteado.");

            // 3. Reset de Inventario y Stock Realista
            $this->info("3. Configurando inventario realista (Capacidad para 50 ventas)...");

            // Limpiar inventarios y lotes actuales
            InsumoLote::query()->delete();
            Inventario::query()->delete();

            // Obtenemos los productos retail
            $productos = Producto::retail()->get();

            foreach ($productos as $producto) {
                // Stock para 50 productos finales
                $this->setupStockForProduct($producto, 50);
            }

            $this->info("   Inventario configurado.");

            // 4. Limpieza de Historial de Ventas (Para empezar de cero)
            $this->info("4. Limpiando historial de ventas y movimientos...");
            Venta::query()->delete();
            Movimiento::query()->delete();
            // No eliminamos la caja para no romper el flujo, pero la reiniciamos
            Caja::query()->update(['monto_inicial' => 0, 'saldo_final' => 0]);
            $this->info("   Historial limpiado.");

            // 5. Crear Película de Prueba
            $this->info("5. Creando película de prueba y función en Sala 1...");
            $pelicula = Pelicula::create([
                'empresa_id' => 1,
                'titulo' => 'PELÍCULA DE PRUEBA CONTROLADA',
                'sinopsis' => 'Esta es una película generada para validar el flujo del POS.',
                'duracion' => '120',
                'clasificacion' => 'PG-13',
                'genero' => 'Acción',
                'activo' => true
            ]);

            $sala = \App\Models\Sala::firstOrCreate(
                ['empresa_id' => 1, 'nombre' => 'Sala 1'],
                [
                    'capacidad' => 50,
                    'configuracion_json' => json_encode(['rows' => 5, 'cols' => 10])
                ]
            );

            $funcion = \App\Models\Funcion::create([
                'empresa_id' => 1,
                'sala_id' => $sala->id,
                'pelicula_id' => $pelicula->id,
                'fecha_hora' => now()->addHours(2),
                'precio' => 12000,
                'activo' => true
            ]);

            // Generar asientos para la función
            $this->generarAsientos($funcion);

            DB::commit();
            $this->info("\n=== SISTEMA ESTABILIZADO EXITOSAMENTE ===");
            $this->line("Usuario: admin@gmail.com / ID: 1");
            $this->line("Película: {$pelicula->titulo}");
            $this->line("Función: today " . $funcion->fecha_hora->format('H:i') . " en {$sala->nombre}");
            $this->line("Stock: Configurado para 50 ventas por producto");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Error durante la estabilización: " . $e->getMessage());
            $this->error($e->getTraceAsString());
        }
    }

    private function generarAsientos($funcion)
    {
        $filas = ['A', 'B', 'C', 'D', 'E'];
        foreach ($filas as $f) {
            for ($i = 1; $i <= 10; $i++) {
                \App\Models\FuncionAsiento::create([
                    'funcion_id' => $funcion->id,
                    'codigo_asiento' => $f . $i,
                    'estado' => 'DISPONIBLE'
                ]);
            }
        }
    }

    private function setupStockForProduct($producto, $desiredSales)
    {
        $producto->load('insumos');
        $empresaId = $producto->empresa_id ?? 1;

        // ESTADO DE PRUEBA: Seteamos siempre 50 en el inventario del producto
        // para que la interfaz del POS muestre stock disponible (no diga Agotado)
        Inventario::updateOrCreate(
            ['producto_id' => $producto->id],
            [
                'empresa_id' => $empresaId,
                'cantidad' => $desiredSales,
                'stock_minimo' => 5,
                'costo_promedio' => $producto->precio * 0.5,
                'ubicacione_id' => $this->getDefaultUbicacionId($empresaId)
            ]
        );

        // Si tiene insumos (receta), cargamos además los insumos para que la validación del Backend pase
        if ($producto->insumos->isNotEmpty()) {
            foreach ($producto->insumos as $insumo) {
                $cantidadPorVenta = $insumo->pivot->cantidad;
                $merma = $insumo->pivot->merma_esperada ?? 0;

                if ($merma > 0) {
                    $cantidadPorVenta = $cantidadPorVenta / (1 - ($merma / 100));
                }

                $totalNecesario = ceil($cantidadPorVenta * $desiredSales);

                // Crear Lote para el insumo
                InsumoLote::create([
                    'insumo_id' => $insumo->id,
                    'numero_lote' => 'ESTAB-' . now()->format('Ymd'),
                    'cantidad_inicial' => $totalNecesario,
                    'cantidad_actual' => $totalNecesario,
                    'costo_unitario' => $insumo->costo_unitario ?? 0,
                    'fecha_vencimiento' => now()->addYear()
                ]);

                // Actualizar stock actual del insumo
                $insumo->update(['stock_actual' => $totalNecesario]);
            }
        }
    }

    private function getDefaultUbicacionId($empresaId)
    {
        return \App\Models\Ubicacione::firstOrCreate(
            ['empresa_id' => $empresaId],
            ['nombre' => 'Almacén Principal']
        )->id;
    }
}
