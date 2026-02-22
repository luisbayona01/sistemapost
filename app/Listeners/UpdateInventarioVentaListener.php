<?php

namespace App\Listeners;

use App\Events\CreateVentaEvent;
use App\Models\Venta;
use App\Models\Inventario;
use App\Models\Kardex;
use App\Enums\TipoTransaccionEnum;
use App\Services\Inventory\InventoryService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateInventarioVentaListener
{
    protected $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * Handle the event.
     */
    public function handle(CreateVentaEvent $event): void
    {
        $venta = $event->venta;

        // ⚠️ PROTECCIÓN: Verificar que no se haya descontado antes (Idempotencia)
        if ($venta->inventario_descontado_at) {
            Log::warning("Intento de doble descuento bloqueado - Venta ID: {$venta->id}");
            return;
        }

        // Solo se ejecuta si la venta está PAGADA
        if ($venta->estado_pago !== 'PAGADA') {
            Log::info("Postergando descuento de inventario para venta PENDIENTE: {$venta->id}");
            return;
        }

        DB::transaction(function () use ($venta) {
            // Cargar productos con sus recetas (insumos)
            $venta->load('productos.insumos');

            foreach ($venta->productos as $producto) {
                $cantidadVendida = $producto->pivot->cantidad;

                // 1. SISTEMA AVANZADO (Basado en Insumos/Recetas)
                if ($producto->insumos->isNotEmpty()) {
                    foreach ($producto->insumos as $insumo) {
                        $cantidadEnReceta = $insumo->pivot->cantidad;
                        
                        // Conversión de Unidades:
                        // Convertimos la cantidad de la receta (ej: 15g) a la unidad del insumo (ej: kg)
                        try {
                            $cantidadConvertida = $this->inventoryService->convert($cantidadEnReceta, $insumo->pivot->unidad_medida, $insumo->unidad_medida);
                        } catch (\Exception $e) {
                            Log::warning("Error de conversión en venta #{$venta->id} para insumo {$insumo->nombre}: " . $e->getMessage());
                            $cantidadConvertida = $cantidadEnReceta;
                        }

                        $cantidadAReducir = $cantidadVendida * $cantidadConvertida;

                        // Aplicar Merma de Receta
                        $merma = $insumo->pivot->merma_esperada ?? 0;
                        if ($merma > 0) {
                            $cantidadAReducir = $cantidadAReducir / (1 - ($merma / 100));
                        }

                        // reducirStockFIFO ya registra Kardex para el INSUMO
                        $this->inventoryService->reducirStockFIFO($insumo->id, $cantidadAReducir, [
                            'venta_id' => $venta->id,
                            'tipo_transaccion' => TipoTransaccionEnum::Venta,
                            'descripcion' => "Venta #{$venta->id} - Item: {$producto->nombre}"
                        ]);
                    }
                }
                // 2. SISTEMA LEGACY (Producto Simple sin Receta)
                else {
                    $registro = Inventario::where('producto_id', $producto->id)
                        ->lockForUpdate()
                        ->first();

                    if ($registro) {
                        $registro->update([
                            'cantidad' => ($registro->cantidad - $cantidadVendida)
                        ]);

                        // Registrar en Kardex para el PRODUCTO (Legacy)
                        (new Kardex())->crearRegistro([
                            'venta_id' => $venta->id,
                            'producto_id' => $producto->id,
                            'cantidad' => $cantidadVendida,
                            'costo_unitario' => Kardex::where('producto_id', $producto->id)->latest('id')->value('costo_unitario') ?? 0,
                            'descripcion' => "Venta (Legacy) #{$venta->id}"
                        ], TipoTransaccionEnum::Venta);
                    }
                }
            }

            // Marcar que el inventario ya fue descontado
            $venta->update(['inventario_descontado_at' => now()]);
            Log::info("Inventario descontado exitosamente para venta ID: {$venta->id}");
        });
    }
}
