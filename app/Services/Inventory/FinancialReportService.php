<?php

namespace App\Services\Inventory;

use App\Models\Compra;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FinancialReportService
{
    /**
     * Muestra el resumen de entradas (Compras) vs Salidas (Ventas)
     * y la utilidad/pérdida resultante
     */
    public function obtenerResumenFinanciero($empresaId, $periodo = 'mes')
    {
        $inicio = now()->startOfMonth();
        $fin = now()->endOfMonth();

        if ($periodo === 'dia') {
            $inicio = now()->startOfDay();
            $fin = now()->endOfDay();
        } elseif ($periodo === 'semana') {
            $inicio = now()->startOfWeek();
            $fin = now()->endOfWeek();
        }

        // 1. Total Compras (Lo que entró en Facturas)
        $totalCompras = Compra::where('empresa_id', $empresaId)
            ->whereBetween('fecha_hora', [$inicio, $fin])
            ->sum('total');

        // 2. Total Ventas (Lo que se vendió)
        $totalVentas = Venta::where('empresa_id', $empresaId)
            ->whereBetween('fecha_hora', [$inicio, $fin])
            ->where('estado_pago', 'PAGADA') // Solo ventas cobradas
            ->sum('total');

        // 3. Cálculo de Ganancia/Pérdida (COGS Real)

        // Calcular costo de lo vendido (Products sold * Recipe cost)
        $costoVentas = DB::table('producto_venta as pv')
            ->join('ventas as v', 'v.id', '=', 'pv.venta_id')
            ->join('recetas as r', 'r.producto_id', '=', 'pv.producto_id')
            ->join('insumos as i', 'i.id', '=', 'r.insumo_id')
            ->where('v.empresa_id', $empresaId)
            ->whereBetween('v.fecha_hora', [$inicio, $fin])
            ->where('v.estado_pago', 'PAGADA')
            ->select(DB::raw('SUM(pv.cantidad * r.cantidad * i.costo_unitario) as total_cost'))
            ->value('total_cost') ?? 0;

        // Margen Bruto Real
        $utilidadBruta = $totalVentas - $costoVentas;

        // Flujo de Caja (Para referencia)
        $flujoCaja = $totalVentas - $totalCompras;

        $estado = $utilidadBruta >= 0 ? 'GANANCIA' : 'PERDIDA';

        return [
            'periodo_inicio' => $inicio->format('d/m/Y'),
            'periodo_fin' => $fin->format('d/m/Y'),
            'total_compras_facturas' => $totalCompras,
            'total_ventas_ingresos' => $totalVentas,
            'costo_ventas_cogs' => $costoVentas,
            'utilidad_bruta' => $utilidadBruta,
            'flujo_caja_neto' => $flujoCaja,
            'diferencia_neta' => $utilidadBruta, // Usamos Utilidad Bruta como KPI principal ahora
            'estado' => $estado
        ];
    }
}
