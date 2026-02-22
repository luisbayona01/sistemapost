<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Insumo;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\Kardex;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OperationalReportController extends Controller
{
    /**
     * Reporte de Inventario Valorizado
     */
    public function valorizado()
    {
        $insumos = Insumo::all()->map(function ($insumo) {
            return [
                'nombre' => $insumo->nombre,
                'cantidad' => $insumo->stock_actual,
                'costo' => $insumo->costo_unitario,
                'total' => $insumo->stock_actual * $insumo->costo_unitario
            ];
        });

        $productos = Producto::retail()->get()->map(function ($producto) {
            return [
                'nombre' => $producto->nombre,
                'cantidad' => $producto->inventario->cantidad ?? 0,
                'costo' => $producto->costo_unitario ?? 0,
                'total' => ($producto->inventario->cantidad ?? 0) * ($producto->costo_unitario ?? 0)
            ];
        });

        $totalGeneral = $insumos->sum('total') + $productos->sum('total');

        return view('admin.reports.inventory_value', compact('insumos', 'productos', 'totalGeneral'));
    }

    /**
     * Reporte de Ventas por Día, Canal y Producto
     */
    public function ventas(Request $request)
    {
        $fechaInicio = $request->get('inicio', now()->startOfMonth()->toDateString());
        $fechaFin = $request->get('fin', now()->endOfDay()->toDateString());

        $ventasPorDia = Venta::whereBetween('fecha_hora', [$fechaInicio, $fechaFin])
            ->select(DB::raw('DATE(fecha_hora) as date'), DB::raw('SUM(total) as total'))
            ->groupBy('date')
            ->get();

        $ventasPorCanal = Venta::whereBetween('fecha_hora', [$fechaInicio, $fechaFin])
            ->select('canal', DB::raw('SUM(total) as total'))
            ->groupBy('canal')
            ->get();

        $ventasPorProducto = DB::table('producto_venta')
            ->join('ventas', 'ventas.id', '=', 'producto_venta.venta_id')
            ->join('productos', 'productos.id', '=', 'producto_venta.producto_id')
            ->where('productos.es_venta_retail', true)
            ->whereBetween('ventas.fecha_hora', [$fechaInicio, $fechaFin])
            ->select('productos.nombre', DB::raw('SUM(producto_venta.cantidad) as cantidad'), DB::raw('SUM(producto_venta.cantidad * producto_venta.precio_venta) as total'))
            ->groupBy('productos.id', 'productos.nombre')
            ->get();

        return view('admin.reports.sales', compact('ventasPorDia', 'ventasPorCanal', 'ventasPorProducto', 'fechaInicio', 'fechaFin'));
    }

    /**
     * Reporte de Márgenes y Ránking
     */
    public function marginalidad()
    {
        $items = Producto::retail()->get()->map(function ($producto) {
            $costo = $producto->costo_unitario ?? 0;
            $precio = $producto->precio ?? 0;
            $utilidad = $precio - $costo;

            return [
                'nombre' => $producto->nombre,
                'precio' => $precio,
                'costo' => $costo,
                'utilidad' => $utilidad,
                'margen' => $precio > 0 ? ($utilidad / $precio) * 100 : 0,
                'vendidos' => DB::table('producto_venta')->where('producto_id', $producto->id)->sum('cantidad')
            ];
        });

        $topVendidos = $items->sortByDesc('vendidos')->take(5);
        $bottomVendidos = $items->sortBy('vendidos')->take(5);
        $topUtilidad = $items->sortByDesc('utilidad')->take(5);
        $bottomUtilidad = $items->sortBy('utilidad')->take(5);

        return view('admin.reports.profitability', compact('topVendidos', 'bottomVendidos', 'topUtilidad', 'bottomUtilidad', 'items'));
    }
}
