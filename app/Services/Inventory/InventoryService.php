<?php

namespace App\Services\Inventory;

use App\Models\Insumo;
use App\Models\InsumoLote;
use App\Models\InsumoSalida;
use App\Models\Kardex;
use App\Enums\TipoTransaccionEnum;
use App\Traits\Inventory\UnitConversionTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryService
{
    use UnitConversionTrait;

    /**
     * Registra entrada de stock (Creación de Lote)
     */
    public function registrarEntrada($insumoId, $cantidad, $costoUnitario, $loteDesc = null, $fechaVenc = null, $context = [])
    {
        $lote = InsumoLote::create([
            'insumo_id' => $insumoId,
            'numero_lote' => $loteDesc,
            'cantidad_inicial' => $cantidad,
            'cantidad_actual' => $cantidad,
            'costo_unitario' => $costoUnitario,
            'fecha_vencimiento' => $fechaVenc
        ]);

        // Registrar en Kardex
        (new Kardex())->crearRegistro([
            'insumo_id' => $insumoId,
            'cantidad' => $cantidad,
            'costo_unitario' => $costoUnitario,
            'compra_id' => $context['compra_id'] ?? null,
            'descripcion' => $context['descripcion'] ?? "Entrada de stock: $loteDesc",
            'empresa_id' => $context['empresa_id'] ?? auth()->user()?->empresa_id
        ], $context['tipo_transaccion'] ?? TipoTransaccionEnum::Compra);

        return $lote;
    }

    /**
     * Reduce stock bajo lógica FIFO (First In, First Out)
     */
    public function reducirStockFIFO($insumoId, $cantidadAReducir, $context = [])
    {
        return DB::transaction(function () use ($insumoId, $cantidadAReducir, $context) {
            $lotes = InsumoLote::where('insumo_id', $insumoId)
                ->where('cantidad_actual', '>', 0)
                ->orderBy('created_at', 'asc') // FIFO
                ->lockForUpdate()
                ->get();

            $pendiente = $cantidadAReducir;
            $costoAcumulado = 0;
            $totalReducido = 0;

            foreach ($lotes as $lote) {
                /** @var \App\Models\InsumoLote $lote */
                if ($pendiente <= 0)
                    break;

                $cantidadEnEsteLote = 0;
                if ($lote->cantidad_actual >= $pendiente) {
                    $cantidadEnEsteLote = $pendiente;
                    $lote->cantidad_actual = (string) round((float) $lote->cantidad_actual - (float) $pendiente, 3);
                    $lote->save();
                    $pendiente = 0;
                } else {
                    $cantidadEnEsteLote = (float) $lote->cantidad_actual;
                    $pendiente -= $cantidadEnEsteLote;
                    $lote->cantidad_actual = '0.000';
                    $lote->save();
                }

                $costoAcumulado += ($cantidadEnEsteLote * $lote->costo_unitario);
                $totalReducido += $cantidadEnEsteLote;
            }

            // Actualizar stock global del insumo
            $insumo = Insumo::find($insumoId);
            $insumo->stock_actual = InsumoLote::where('insumo_id', $insumoId)->sum('cantidad_actual');
            $insumo->save();

            if ($pendiente > 0) {
                // Si no hay más lotes pero sobra pendiente, lo descontamos del último lote (aunque quede negativo)
                // o creamos un lote de ajuste técnico si no existe ningún lote.
                $ultimoLote = InsumoLote::where('insumo_id', $insumoId)->latest()->first();

                if ($ultimoLote) {
                    $ultimoLote->cantidad_actual = (string) round((float) $ultimoLote->cantidad_actual - $pendiente, 3);
                    $ultimoLote->save();
                    $costoAcumulado += ($pendiente * $ultimoLote->costo_unitario);
                    $totalReducido += $pendiente;
                } else {
                    // Si ni siquiera hay lotes, creamos uno de ajuste inicial para que el sistema no rompa
                    $loteAjuste = InsumoLote::create([
                        'insumo_id' => $insumoId,
                        'numero_lote' => 'AJUSTE-NEGATIVO-INICIAL',
                        'cantidad_inicial' => 0,
                        'cantidad_actual' => (string) -$pendiente,
                        'costo_unitario' => $insumo->costo_unitario ?? 0,
                    ]);
                    $costoAcumulado += ($pendiente * $loteAjuste->costo_unitario);
                    $totalReducido += $pendiente;
                }
            }

            // Registrar en Kardex
            $costoPromedio = $totalReducido > 0 ? ($costoAcumulado / $totalReducido) : (float) $insumo->costo_unitario;

            (new Kardex())->crearRegistro([
                'insumo_id' => $insumoId,
                'cantidad' => $cantidadAReducir,
                'costo_unitario' => (float) $costoPromedio,
                'venta_id' => $context['venta_id'] ?? null,
                'descripcion' => $context['descripcion'] ?? null,
                'empresa_id' => $context['empresa_id'] ?? $insumo->empresa_id
            ], $context['tipo_transaccion'] ?? TipoTransaccionEnum::Venta);

            return true;
        });
    }

    /**
     * Valuación total de activos en inventario
     */
    public function obtenerValorInventario($empresaId)
    {
        return InsumoLote::whereHas('insumo', function ($q) use ($empresaId) {
            $q->where('empresa_id', $empresaId);
        })
            ->where('cantidad_actual', '>', 0)
            ->select(DB::raw('SUM(cantidad_actual * costo_unitario) as total_valor'))
            ->first()->total_valor ?? 0;
    }

    /**
     * Registrar Salidas Especiales (Bajas, Cortesías, etc)
     */
    public function registrarSalidaEspecial($insumoId, $cantidad, $tipo, $motivo, $userId)
    {
        return DB::transaction(function () use ($insumoId, $cantidad, $tipo, $motivo, $userId) {
            $insumo = Insumo::findOrFail($insumoId);

            // Calcular costo estimado basado en el último lote con stock
            $ultimoCosto = InsumoLote::where('insumo_id', $insumoId)
                ->where('cantidad_actual', '>', 0)
                ->latest()
                ->value('costo_unitario') ?? $insumo->costo_unitario;

            $salida = InsumoSalida::create([
                'insumo_id' => $insumoId,
                'user_id' => $userId,
                'cantidad' => $cantidad,
                'tipo' => $tipo,
                'motivo' => $motivo,
                'costo_estimado' => $ultimoCosto * $cantidad
            ]);

            $this->reducirStockFIFO($insumoId, $cantidad, [
                'tipo_transaccion' => TipoTransaccionEnum::Ajuste,
                'motivo' => $motivo,
                'descripcion' => "Salida Especial ($tipo): $motivo",
                'user_id' => $userId
            ]);

            return $salida;
        });
    }

    /**
     * Sincroniza el stock del sistema con el conteo físico de una auditoría
     */
    public function ajustarStockPorAuditoria($insumoId, $cantidadFisica, $userId)
    {
        return DB::transaction(function () use ($insumoId, $cantidadFisica, $userId) {
            $insumo = Insumo::findOrFail($insumoId);
            $stockTeorico = $insumo->stock_actual;
            $diferencia = $cantidadFisica - $stockTeorico;

            if ($diferencia == 0)
                return true;

            if ($diferencia < 0) {
                // Hay menos de lo que debería: Registrar como MERMA/FUGUDA
                $cantidadAReducir = abs($diferencia);
                $this->registrarSalidaEspecial(
                    $insumoId,
                    $cantidadAReducir,
                    'AJUSTE_AUDITORIA',
                    'Diferencia negativa encontrada en auditoría ciega',
                    $userId
                );
            } else {
                // Hay más de lo que debería: Registrar entrada de ajuste
                $ultimoCosto = InsumoLote::where('insumo_id', $insumoId)->latest()->value('costo_unitario') ?? 0;
                $this->registrarEntrada(
                    $insumoId,
                    $diferencia,
                    $ultimoCosto,
                    'AJUSTE_POST_AUDITORIA',
                    null,
                    [
                        'tipo_transaccion' => TipoTransaccionEnum::Auditoria,
                        'descripcion' => "Ajuste positivo por auditoría física",
                        'auditoria_id' => $context['auditoria_id'] ?? null
                    ]
                );

                // Actualizar stock global
                $insumo->stock_actual = InsumoLote::where('insumo_id', $insumoId)->sum('cantidad_actual');
                $insumo->save();
            }

            return true;
        });
    }

    /**
     * Prevents DB deadlocks by ordering the IN list during locks
     */
    public function reservarInsumosOrdenados(array $productosRequeridos)
    {
        // Orden estricto por ID para evitar deadlocks en InnoDB
        $insumosIds = collect($productosRequeridos)
            ->pluck('insumo_id')
            ->unique()
            ->sort()
            ->values()
            ->all();

        return \Illuminate\Support\Facades\DB::transaction(function () use ($insumosIds, $productosRequeridos) {
            $insumosBloqueados = \App\Models\Insumo::whereIn('id', $insumosIds)
                ->lockForUpdate()
                ->orderBy('id', 'asc')
                ->get()
                ->keyBy('id');

            foreach ($productosRequeridos as $req) {
                if (isset($insumosBloqueados[$req['insumo_id']])) {
                    $insumo = $insumosBloqueados[$req['insumo_id']];
                    $insumo->decrement('stock_actual', $req['cantidad']);
                    // Tu lógica de kardex existente ya se llama desde aquí si quieres
                }
            }

            return true;
        });
    }
}
