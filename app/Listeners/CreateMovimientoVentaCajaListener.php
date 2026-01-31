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
        $caja = Caja::where('user_id', Auth::id())->where('estado', 1)->first();

        if (!$caja) {
            Log::warning('Evento de venta sin caja abierta', ['user_id' => Auth::id()]);
            return;
        }

        try {
            Movimiento::create([
                'tipo' => TipoMovimientoEnum::Venta,
                'descripcion' => 'Venta n° ' . $event->venta->numero_comprobante,
                'monto' => $event->venta->total,
                'metodo_pago' => $event->venta->metodo_pago,
                'caja_id' => $caja->id
            ]);
        } catch (Exception $e) {
            Log::error(
                'Error en el Listener CreateMovimientoVentaCajaListener',
                ['error' => $e->getMessage()]
            );
        }
    }
}
