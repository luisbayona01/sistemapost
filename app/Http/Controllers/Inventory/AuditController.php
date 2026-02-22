<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\AuditoriaInventario;
use App\Models\AuditoriaDetalle;
use App\Models\Insumo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuditController extends Controller
{
    public function store(Request $request)
    {
        return DB::transaction(function () {
            $auditoria = AuditoriaInventario::create([
                'empresa_id' => auth()->user()->empresa_id,
                'user_id' => auth()->id(),
                'fecha_auditoria' => now(),
                'estado' => 'abierta'
            ]);

            $insumos = Insumo::all();
            foreach ($insumos as $insumo) {
                AuditoriaDetalle::create([
                    'auditoria_id' => $auditoria->id,
                    'insumo_id' => $insumo->id,
                    'stock_teorico' => $insumo->stock_actual
                ]);
            }

            return redirect()->back()->with('success', 'Nueva auditoría ciega iniciada');
        });
    }

    public function finalize(Request $request, AuditoriaInventario $auditoria)
    {
        $conteos = $request->input('stock_fisico'); // Array [insumo_id => valor]

        DB::transaction(function () use ($auditoria, $conteos) {
            $totalDiferencia = 0;
            $inventoryService = app(\App\Services\Inventory\InventoryService::class);

            foreach ($conteos as $insumoId => $valorFisico) {
                $detalle = $auditoria->detalles()->where('insumo_id', $insumoId)->first();
                if ($detalle) {
                    $diferencia = $valorFisico - $detalle->stock_teorico;

                    // Calcular valor de la pérdida si falta stock
                    $ultimoCosto = \App\Models\InsumoLote::where('insumo_id', $insumoId)->latest()->value('costo_unitario') ?? 0;
                    $valorDif = abs($diferencia) * $ultimoCosto;

                    $detalle->update([
                        'stock_fisico' => $valorFisico,
                        'diferencia' => $diferencia,
                        'valor_diferencia' => $valorDif
                    ]);

                    // ✅ AJUSTE REAL DE STOCK: El sistema ahora es la realidad física
                    if ($diferencia != 0) {
                        $inventoryService->ajustarStockPorAuditoria($insumoId, $valorFisico, auth()->id());
                    }

                    if ($diferencia < 0)
                        $totalDiferencia += $valorDif;
                }
            }

            $auditoria->update([
                'estado' => 'finalizada',
                'total_diferencia_valor' => $totalDiferencia
            ]);
        });

        return redirect()->back()->with('success', 'Auditoría finalizada y guardada');
    }
}
