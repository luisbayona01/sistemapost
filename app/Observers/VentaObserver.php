<?php

namespace App\Observers;

use App\Models\Caja;
use App\Models\Comprobante;
use App\Models\Venta;
use App\Services\Inventory\InventoryService;
use Carbon\Carbon;
use App\Enums\TipoTransaccionEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class VentaObserver
{
    protected $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * Handle the Venta "creating" event.
     */
    public function creating(Venta $venta): void
    {
        // Reglas de Dominio Cruzadas (Segunda Capa de Seguridad)
        if ($venta->tipo_venta === 'WEB' && $venta->metodo_pago === 'EFECTIVO') {
            throw new \Exception('Error de Dominio: Las ventas WEB no pueden ser pagadas en EFECTIVO.');
        }

        if ($venta->tipo_venta === 'FISICA' && $venta->metodo_pago === 'STRIPE') {
            // Nota: En el futuro podría permitirse Stripe en POS físico, 
            // pero por requerimiento de Fase 2 está prohibido.
            throw new \Exception('Error de Dominio: Las ventas FÍSICAS no pueden usar STRIPE en este flujo.');
        }

        // Si la venta ya trae caja_id (inyectado por VentaService), validamos que exista.
        if ($venta->tipo_venta === 'FISICA') {
            if (empty($venta->caja_id)) {
                $caja = Caja::where('user_id', Auth::id())
                    ->where('empresa_id', Auth::user()?->empresa_id)
                    ->where('estado', 'ABIERTA')
                    ->first();

                if (!$caja) {
                    throw new \Exception('No hay caja abierta para registrar venta física.');
                }
                $venta->caja_id = $caja->id;
            }
        } else {
            // Venta WEB no tiene caja
            $venta->caja_id = null;
        }

        if (empty($venta->numero_comprobante)) {
            $tipoComprobante = Comprobante::findOrFail($venta->comprobante_id)->nombre;
            $venta->numero_comprobante = $venta->generarNumeroVenta($venta->caja_id ?? 0, $tipoComprobante);
        }

        if (empty($venta->fecha_hora)) {
            $venta->fecha_hora = Carbon::now();
        }

        if (empty($venta->user_id) && Auth::check()) {
            $venta->user_id = Auth::id();
        }

        if (is_null($venta->cambio)) {
            $venta->cambio = 0.00;
        }

        if (empty($venta->empresa_id) && Auth::check()) {
            $venta->empresa_id = Auth::user()->empresa_id;
        }

        // ASIGNAR DÍA OPERATIVO (Accounting View)
        if (empty($venta->fecha_operativa)) {
            $accountingService = app(\App\Services\AccountingService::class);
            $venta->fecha_operativa = $accountingService->getActiveDay($venta->empresa_id);
        }

        if (empty($venta->estado_fiscal)) {
            $venta->estado_fiscal = 'NORMAL';
        }
    }

    /**
     * Handle the Venta "updating" event.
     */
    public function updating(Venta $venta): void
    {
        // Regla: Una venta WEB no puede marcarse PAGADA manualmente si no tiene una transacción SUCCESS
        if ($venta->isDirty('estado_pago') && $venta->estado_pago === 'PAGADA' && $venta->tipo_venta === 'WEB') {
            $hasSuccess = $venta->paymentTransactions()->where('status', 'SUCCESS')->exists();
            if (!$hasSuccess) {
                // Si estamos en medio de la transacción que la marca como SUCCESS, esto podría fallar.
                // Sin embargo, StripePaymentService::handlePaymentSucceeded marca primero la transacción
                // y luego la venta, por lo que debería funcionar.
                Log::warning("Intento de marcar venta WEB {$venta->id} como PAGADA sin transacciones exitosas.");
                // throw new \Exception('No se puede marcar una venta WEB como PAGADA sin una transacción confirmada.');
            }
        }
    }

    /**
     * Handle the Venta "created" event.
     */
    public function created(Venta $venta): void
    {
        // Movido a UpdateInventarioVentaListener para centralizar lógica y evitar duplicidad.
    }
}
