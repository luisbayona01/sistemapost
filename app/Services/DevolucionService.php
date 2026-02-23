<?php

namespace App\Services;

use App\Enums\TipoTransaccionEnum;
use App\Models\Devolucion;
use App\Models\Movimiento;
use App\Models\Venta;
use App\Models\Inventario;
use App\Models\Kardex;
use App\Enums\TipoMovimientoEnum;
use App\Services\Inventory\InventoryService;
use App\Services\CinemaService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DevolucionService
{
    protected $inventoryService;
    protected $cinemaService;

    public function __construct(InventoryService $inventoryService, CinemaService $cinemaService)
    {
        $this->inventoryService = $inventoryService;
        $this->cinemaService = $cinemaService;
    }

    /**
     * Procesar una devolución completa (Blindaje Operativo)
     */
    public function procesarDevolucion(Venta $venta, string $motivo, bool $reintegrarStock = true): Devolucion
    {
        return DB::transaction(function () use ($venta, $motivo, $reintegrarStock) {

            // 1. Validaciones de Integridad
            if ($venta->estado_pago === 'DEVUELTA') {
                throw new \Exception("Esta venta ya ha sido devuelta anteriormente.");
            }

            $user = auth()->user();
            $empresaId = $user->empresa_id;

            // 2. Crear Registro de Devolución (Auditoría Inmutable)
            $devolucion = Devolucion::create([
                'empresa_id' => $empresaId,
                'venta_id' => $venta->id,
                'user_id' => $user->id,
                'monto_total' => $venta->total_final > 0 ? $venta->total_final : $venta->total,
                'motivo' => $motivo,
                'reintegrar_inventario' => $reintegrarStock,
                'metodo_pago_devolucion' => $venta->metodo_pago->value ?? 'EFECTIVO'
            ]);

            // 3. Reversión de Inventario (Si aplica)
            if ($reintegrarStock && $venta->inventario_descontado_at) {
                $this->revertirInventario($venta);
            }

            // 4. Liberar Asientos (Si es Cinema)
            if ($venta->canal === 'ventanilla' || $venta->canal === 'mixta') {
                $this->cinemaService->liberarAsientosPorVenta($venta->id);
            }

            // 5. Registrar Movimiento de Caja (Egreso para compensar la venta)
            Movimiento::create([
                'empresa_id' => $empresaId,
                'caja_id' => $venta->caja_id,
                'venta_id' => $venta->id,
                'user_id' => $user->id,
                'tipo' => TipoMovimientoEnum::DEVOLUCION,
                'monto' => $venta->total_final > 0 ? $venta->total_final : $venta->total,
                'metodo_pago' => $venta->metodo_pago,
                'descripcion' => "DEVOLUCIÓN de Venta #{$venta->id}. Motivo: $motivo",
            ]);

            // 6. Actualizar Estado de la Venta
            $venta->update([
                'estado_pago' => 'DEVUELTA',
                // Guardar quién devolvió en algún campo si fuera necesario, 
                // pero ya está en la tabla devoluciones.
            ]);

            Log::info("Devolución procesada correctamente para Venta #{$venta->id}");

            return $devolucion;
        });
    }

    /**
     * Lógica interna para devolver el stock al inventario
     */
    private function revertirInventario(Venta $venta): void
    {
        $venta->load('productos.insumos');

        foreach ($venta->productos as $producto) {
            $cantidadADevolver = $producto->pivot->cantidad;

            // Caso A: Insumos (Receta)
            if ($producto->insumos->isNotEmpty()) {
                foreach ($producto->insumos as $insumo) {
                    $cantidadUnitaria = $insumo->pivot->cantidad;

                    // Conversión de Unidades:
                    try {
                        $cantidadConvertida = $this->inventoryService->convert($cantidadUnitaria, $insumo->pivot->unidad_medida, $insumo->unidad_medida);
                    } catch (\Exception $e) {
                        Log::warning("Error de conversión en devolución #{$venta->id} para insumo {$insumo->nombre}: " . $e->getMessage());
                        $cantidadConvertida = $cantidadUnitaria;
                    }

                    $cantidadTotalADevolver = $cantidadADevolver * $cantidadConvertida;

                    // Ajustar por Merma (debemos devolver lo que se descontó originalmente)
                    $merma = $insumo->pivot->merma_esperada ?? 0;
                    if ($merma > 0) {
                        $cantidadTotalADevolver = $cantidadTotalADevolver / (1 - ($merma / 100));
                    }

                    // Registrar entrada técnica
                    $ultimoCosto = DB::table('insumo_lotes')->where('insumo_id', $insumo->id)->latest()->value('costo_unitario') ?? 0;

                    $this->inventoryService->registrarEntrada($insumo->id, $cantidadTotalADevolver, $ultimoCosto, "DEVOLUCIÓN VENTA #{$venta->id}", null, [
                        'tipo_transaccion' => TipoTransaccionEnum::Devolucion,
                        'descripcion' => "Reintegro por Devolución de Venta #{$venta->id}",
                        'empresa_id' => $venta->empresa_id
                    ]);

                    // Actualizar Stock Actual del Insumo
                    $insumo->increment('stock_actual', $cantidadTotalADevolver);
                }
            }
            // Caso B: Producto Legacy (Simple)
            else {
                $registro = Inventario::where('producto_id', $producto->id)->first();
                if ($registro) {
                    $registro->increment('cantidad', $cantidadADevolver);

                    // Kardex Legacy
                    (new Kardex())->crearRegistro([
                        'venta_id' => $venta->id,
                        'producto_id' => $producto->id,
                        'cantidad' => $cantidadADevolver,
                        'costo_unitario' => Kardex::where('producto_id', $producto->id)->latest('id')->value('costo_unitario') ?? 0,
                        'descripcion' => "Reintegro Venta (Legacy) #{$venta->id}",
                        'empresa_id' => $venta->empresa_id
                    ], TipoTransaccionEnum::Devolucion);
                }
            }
        }
    }
}
