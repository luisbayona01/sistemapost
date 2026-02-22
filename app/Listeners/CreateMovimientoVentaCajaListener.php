<?php

namespace App\Listeners;

use App\Enums\TipoMovimientoEnum;
use App\Events\CreateVentaEvent;
use App\Models\Caja;
use App\Models\Movimiento;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CreateMovimientoVentaCajaListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CreateVentaEvent $event): void
    {
        $venta = $event->venta;

        // ⚠️ PROTECCIÓN: Verificar que no se haya creado el movimiento antes (Idempotencia)
        if ($venta->movimiento_creado_at) {
            Log::warning('Intento de duplicación de movimiento bloqueado', [
                'venta_id' => $venta->id,
                'movimiento_creado_at' => $venta->movimiento_creado_at
            ]);
            return;
        }

        $caja = Caja::where('user_id', Auth::id())->where('estado', 'ABIERTA')->first();

        if (!$caja) {
            Log::warning('Evento de venta sin caja abierta', ['user_id' => Auth::id()]);
            return;
        }

        try {
            Movimiento::create([
                'tipo' => TipoMovimientoEnum::Venta,
                'descripcion' => 'Venta n° ' . $venta->numero_comprobante,
                'monto' => $venta->total,
                'metodo_pago' => $venta->metodo_pago,
                'caja_id' => $caja->id
            ]);

            // Marcar que el movimiento ya fue creado (FASE 4.1)
            $venta->update(['movimiento_creado_at' => now()]);

            Log::info('Movimiento de caja creado exitosamente', [
                'venta_id' => $venta->id,
                'caja_id' => $caja->id,
                'monto' => $venta->total
            ]);
        } catch (Exception $e) {
            Log::error(
                'Error en el Listener CreateMovimientoVentaCajaListener',
                ['error' => $e->getMessage()]
            );
        }
    }
}
