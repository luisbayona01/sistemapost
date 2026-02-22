<?php

namespace App\Jobs;

use App\Models\Venta;
use App\Models\FuncionAsiento;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ExpireStaleWebSales implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        // Buscar ventas web pendientes con más de 1 hora de antigüedad
        $ventasExpiradas = Venta::where('estado_pago', 'PENDIENTE')
            ->where('canal', 'web')
            ->where('created_at', '<', Carbon::now()->subHour())
            ->get();

        /** @var \App\Models\Venta $venta */
        foreach ($ventasExpiradas as $venta) {
            DB::transaction(function () use ($venta) {

                // Marcar venta como expirada
                $venta->update(['estado_pago' => 'EXPIRADA']);

                // Liberar asientos reservados
                foreach ($venta->asientosCinema as $asiento) {
                    $asiento->liberar();
                }

                // Marcar transacciones de pago como fallidas
                $venta->paymentTransactions()
                    ->where('status', 'PENDING')
                    ->update(['status' => 'FAILED']);

                Log::info("Venta web expirada: {$venta->id} - Asientos liberados");
            });
        }

        if ($ventasExpiradas->count() > 0) {
            Log::info("ExpireStaleWebSales: {$ventasExpiradas->count()} ventas expiradas");
        }
    }
}
