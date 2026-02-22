<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Venta, Caja, Alerta, Producto, Funcion};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Cache};
use Carbon\Carbon;

class MobileController extends Controller
{
    /**
     * Vista Ejecutiva Móvil (Dashboard simplificado)
     */
    public function index(Request $request)
    {
        $empresaId = auth()->user()->empresa_id;
        $cacheKey = "mobile_dashboard_{$empresaId}";

        // Opción para forzar actualización (?refresh=1)
        if ($request->has('refresh')) {
            Cache::forget($cacheKey);
            return redirect()->route('mobile.index');
        }

        // Cache por 5 minutos (Requerimiento explícito)
        $data = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($empresaId) {
            $hoy = Carbon::today();
            $ayer = Carbon::yesterday();

            return [
                'kpis' => $this->getKpis($empresaId, $hoy, $ayer),
                'caja' => Caja::where('empresa_id', $empresaId)
                    ->orderBy('created_at', 'desc')
                    ->first(),
                'alertas' => Alerta::where('empresa_id', $empresaId)
                    ->where('resuelta', false)
                    ->orderByRaw("CASE WHEN tipo = 'CRITICA' THEN 1 WHEN tipo = 'ADVERTENCIA' THEN 2 ELSE 3 END")
                    ->orderBy('created_at', 'desc')
                    ->take(3)
                    ->get(),
                'top' => $this->getTopDelDia($empresaId, $hoy)
            ];
        });

        return view('admin.mobile.index', $data);
    }

    private function getKpis($empresaId, $hoy, $ayer)
    {
        // Totales Hoy
        $ventasHoy = Venta::where('empresa_id', $empresaId)
            ->where('estado_pago', 'PAGADA')
            ->whereDate('fecha_hora', $hoy)
            ->select(
                DB::raw('SUM(total) as total'),
                DB::raw('COUNT(id) as transacciones'),
                DB::raw("SUM(CASE WHEN canal IN ('ventanilla', 'web') THEN total ELSE 0 END) as boleteria"),
                DB::raw("SUM(CASE WHEN canal IN ('confiteria', 'mixta') THEN total ELSE 0 END) as confiteria")
            )
            ->first();

        // Total Ayer (solo monto para comparativo rápido)
        $totalAyer = Venta::where('empresa_id', $empresaId)
            ->where('estado_pago', 'PAGADA')
            ->whereDate('fecha_hora', $ayer)
            ->sum('total');

        $diferencia = $totalAyer > 0
            ? (($ventasHoy->total - $totalAyer) / $totalAyer) * 100
            : ($ventasHoy->total > 0 ? 100 : 0);

        return [
            'total' => $ventasHoy->total ?? 0,
            'transacciones' => $ventasHoy->transacciones ?? 0,
            'boleteria' => $ventasHoy->boleteria ?? 0,
            'confiteria' => $ventasHoy->confiteria ?? 0,
            'diff_porcentaje' => round($diferencia, 1),
            'diff_signo' => $diferencia >= 0 ? '+' : '',
            'diff_color' => $diferencia >= 0 ? 'text-green-500' : 'text-red-500'
        ];
    }

    private function getTopDelDia($empresaId, $hoy)
    {
        // Top Película (Monto)
        $pelicula = DB::table('ventas')
            ->join('venta_funcion_asientos', 'ventas.id', '=', 'venta_funcion_asientos.venta_id')
            ->join('funcion_asientos', 'venta_funcion_asientos.funcion_asiento_id', '=', 'funcion_asientos.id')
            ->join('funciones', 'funcion_asientos.funcion_id', '=', 'funciones.id')
            ->join('peliculas', 'funciones.pelicula_id', '=', 'peliculas.id')
            ->where('ventas.empresa_id', $empresaId)
            ->where('ventas.estado_pago', 'PAGADA')
            ->whereDate('ventas.fecha_hora', $hoy)
            ->select('peliculas.titulo', DB::raw('SUM(ventas.total) as total'))
            ->groupBy('peliculas.id', 'peliculas.titulo')
            ->orderByDesc('total')
            ->first();

        // Top Producto (Monto) - Solo Retail
        $producto = DB::table('producto_venta')
            ->join('ventas', 'producto_venta.venta_id', '=', 'ventas.id')
            ->join('productos', 'producto_venta.producto_id', '=', 'productos.id')
            ->where('ventas.empresa_id', $empresaId)
            ->where('ventas.estado_pago', 'PAGADA')
            ->where('productos.es_venta_retail', true)
            ->whereDate('ventas.fecha_hora', $hoy)
            ->select('productos.nombre', DB::raw('SUM(producto_venta.subtotal) as total'))
            ->groupBy('productos.id', 'productos.nombre')
            ->orderByDesc('total')
            ->first();

        return [
            'pelicula' => $pelicula,
            'producto' => $producto
        ];
    }
}
