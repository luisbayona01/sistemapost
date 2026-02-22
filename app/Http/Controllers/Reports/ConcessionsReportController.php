<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConcessionsReportController extends Controller
{
    public function index(Request $request)
    {
        $empresaId = auth()->user()->empresa_id;
        $fechaInicio = $request->input('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', now()->format('Y-m-d'));

        // Top productos vendidos
        $topProductos = DB::table('producto_venta')
            ->join('productos', 'producto_venta.producto_id', '=', 'productos.id')
            ->join('ventas', 'producto_venta.venta_id', '=', 'ventas.id')
            ->where('productos.empresa_id', $empresaId)
            ->where('productos.es_venta_retail', true)
            ->whereBetween('ventas.fecha_hora', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
            ->select(
                'productos.nombre',
                DB::raw('SUM(producto_venta.cantidad) as total_vendido'),
                DB::raw('SUM(producto_venta.cantidad * producto_venta.precio_venta) as ingreso_total')
            )
            ->groupBy('productos.id', 'productos.nombre')
            ->orderByDesc('total_vendido')
            ->limit(10)
            ->get();

        // Total de ventas de confitería (incluyendo las mixtas)
        $totalVentas = DB::table('ventas')
            ->where('empresa_id', $empresaId)
            ->whereIn('canal', ['confiteria', 'mixta'])
            ->whereBetween('fecha_hora', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
            ->where('estado_pago', 'PAGADA')
            ->sum('total');

        return view('admin.reportes.confiteria', compact('topProductos', 'totalVentas', 'fechaInicio', 'fechaFin'));
    }
}
