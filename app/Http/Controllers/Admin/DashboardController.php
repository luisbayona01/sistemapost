<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Venta, Producto, Funcion, FuncionAsiento};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Dashboard ejecutivo principal
     */
    public function index(Request $request)
    {
        $empresaId = auth()->user()->empresa_id;

        // KPIs de HOY
        $hoy = $this->getKPIsDelDia($empresaId, Carbon::today());

        // KPIs de AYER (para comparación)
        $ayer = $this->getKPIsDelDia($empresaId, Carbon::yesterday());

        // KPIs de ESTA SEMANA
        $estaSemana = $this->getKPIsPeriodo($empresaId, Carbon::now()->startOfWeek(), Carbon::now());

        // KPIs de SEMANA ANTERIOR
        $semanaAnterior = $this->getKPIsPeriodo(
            $empresaId,
            Carbon::now()->subWeek()->startOfWeek(),
            Carbon::now()->subWeek()->endOfWeek()
        );

        // KPIs de ESTE MES
        $esteMes = $this->getKPIsPeriodo($empresaId, Carbon::now()->startOfMonth(), Carbon::now());

        // KPIs de MES ANTERIOR
        $mesAnterior = $this->getKPIsPeriodo(
            $empresaId,
            Carbon::now()->subMonth()->startOfMonth(),
            Carbon::now()->subMonth()->endOfMonth()
        );

        // Tendencias para gráficos
        $tendenciaVentas = $this->getTendenciaVentas($empresaId);
        $tendenciaOcupacion = $this->getTendenciaOcupacion($empresaId);

        // Alertas Activas (Widget)
        $alertasActivas = \App\Models\Alerta::where('empresa_id', $empresaId)
            ->where('resuelta', false)
            ->orderBy('tipo', 'asc') // CRITICA primero
            ->take(5)
            ->get();

        return view('admin.dashboard.index', compact(
            'hoy',
            'ayer',
            'estaSemana',
            'semanaAnterior',
            'esteMes',
            'mesAnterior',
            'tendenciaVentas',
            'tendenciaOcupacion',
            'alertasActivas'
        ));
    }

    /**
     * Top Performers
     */
    public function topPerformers(Request $request)
    {
        $empresaId = auth()->user()->empresa_id;
        $fechaInicio = $request->input('fecha_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', Carbon::now()->format('Y-m-d'));

        // Top 10 películas por ingreso
        $topPeliculas = DB::table('ventas')
            ->join('venta_funcion_asientos', 'ventas.id', '=', 'venta_funcion_asientos.venta_id')
            ->join('funcion_asientos', 'venta_funcion_asientos.funcion_asiento_id', '=', 'funcion_asientos.id')
            ->join('funciones', 'funcion_asientos.funcion_id', '=', 'funciones.id')
            ->join('peliculas', 'funciones.pelicula_id', '=', 'peliculas.id')
            ->where('ventas.empresa_id', $empresaId)
            ->where('ventas.estado_pago', 'PAGADA')
            ->whereBetween('ventas.fecha_hora', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
            ->select(
                'peliculas.titulo',
                DB::raw('COUNT(DISTINCT venta_funcion_asientos.funcion_asiento_id) as tickets_vendidos'),
                DB::raw('SUM(ventas.total) as ingreso_total')
            )
            ->groupBy('peliculas.id', 'peliculas.titulo')
            ->orderByDesc('ingreso_total')
            ->limit(10)
            ->get();

        // Top 10 productos confitería por unidades vendidas
        $topProductosUnidades = DB::table('producto_venta')
            ->join('productos', 'producto_venta.producto_id', '=', 'productos.id')
            ->join('ventas', 'producto_venta.venta_id', '=', 'ventas.id')
            ->where('productos.empresa_id', $empresaId)
            ->where('productos.es_venta_retail', true)
            ->where('ventas.estado_pago', 'PAGADA')
            ->whereBetween('ventas.fecha_hora', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
            ->select(
                'productos.nombre',
                DB::raw('SUM(producto_venta.cantidad) as total_vendido'),
                DB::raw('SUM(producto_venta.subtotal) as ingreso_total')
            )
            ->groupBy('productos.id', 'productos.nombre')
            ->orderByDesc('total_vendido')
            ->limit(10)
            ->get();

        // Top 10 productos confitería por ingreso
        $topProductosIngreso = DB::table('producto_venta')
            ->join('productos', 'producto_venta.producto_id', '=', 'productos.id')
            ->join('ventas', 'producto_venta.venta_id', '=', 'ventas.id')
            ->where('productos.empresa_id', $empresaId)
            ->where('productos.es_venta_retail', true)
            ->where('ventas.estado_pago', 'PAGADA')
            ->whereBetween('ventas.fecha_hora', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
            ->select(
                'productos.nombre',
                DB::raw('SUM(producto_venta.cantidad) as total_vendido'),
                DB::raw('SUM(producto_venta.subtotal) as ingreso_total')
            )
            ->groupBy('productos.id', 'productos.nombre')
            ->orderByDesc('ingreso_total')
            ->limit(10)
            ->get();

        // Horarios más rentables
        $horariosRentables = DB::table('ventas')
            ->join('venta_funcion_asientos', 'ventas.id', '=', 'venta_funcion_asientos.venta_id')
            ->join('funcion_asientos', 'venta_funcion_asientos.funcion_asiento_id', '=', 'funcion_asientos.id')
            ->join('funciones', 'funcion_asientos.funcion_id', '=', 'funciones.id')
            ->where('ventas.empresa_id', $empresaId)
            ->where('ventas.estado_pago', 'PAGADA')
            ->whereBetween('ventas.fecha_hora', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
            ->select(
                DB::raw('HOUR(funciones.fecha_hora) as hora'),
                DB::raw('COUNT(DISTINCT funciones.id) as total_funciones'),
                DB::raw('COUNT(DISTINCT venta_funcion_asientos.funcion_asiento_id) as tickets_vendidos'),
                DB::raw('SUM(ventas.total) as ingreso_total')
            )
            ->groupBy('hora')
            ->orderByDesc('ingreso_total')
            ->get();

        return view('admin.dashboard.top-performers', compact(
            'topPeliculas',
            'topProductosUnidades',
            'topProductosIngreso',
            'horariosRentables',
            'fechaInicio',
            'fechaFin'
        ));
    }

    /**
     * Reporte de Ocupación
     */
    public function ocupacion(Request $request)
    {
        $empresaId = auth()->user()->empresa_id;
        $fechaInicio = $request->input('fecha_inicio', Carbon::today()->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', Carbon::today()->addDays(7)->format('Y-m-d'));

        // Ocupación por función
        $funciones = Funcion::with(['pelicula', 'sala'])
            ->where('empresa_id', $empresaId)
            ->whereBetween('fecha_hora', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
            ->get()
            ->map(function ($funcion) {
                $totalAsientos = $funcion->sala->capacidad_total ?? 0;
                $asientosVendidos = FuncionAsiento::where('funcion_id', $funcion->id)
                    ->where('estado', 'VENDIDO')
                    ->count();

                $ocupacion = $totalAsientos > 0 ? ($asientosVendidos / $totalAsientos) * 100 : 0;

                $ingreso = DB::table('venta_funcion_asientos')
                    ->join('ventas', 'venta_funcion_asientos.venta_id', '=', 'ventas.id')
                    ->join('funcion_asientos', 'venta_funcion_asientos.funcion_asiento_id', '=', 'funcion_asientos.id')
                    ->where('funcion_asientos.funcion_id', $funcion->id)
                    ->where('ventas.estado_pago', 'PAGADA')
                    ->sum('ventas.total');

                return [
                    'funcion_id' => $funcion->id,
                    'pelicula' => $funcion->pelicula->titulo ?? 'Sin título',
                    'sala' => $funcion->sala->nombre ?? 'Sin sala',
                    'fecha_hora' => $funcion->fecha_hora->format('d/m/Y H:i'),
                    'fecha_corta' => $funcion->fecha_hora->format('d/m'),
                    'hora' => $funcion->fecha_hora->format('H:i'),
                    'dia_semana' => $funcion->fecha_hora->locale('es')->dayName,
                    'capacidad_total' => $totalAsientos,
                    'asientos_vendidos' => $asientosVendidos,
                    'asientos_disponibles' => $totalAsientos - $asientosVendidos,
                    'ocupacion_porcentaje' => round($ocupacion, 1),
                    'ingreso_total' => $ingreso,
                ];
            });

        // Datos para heatmap (día x horario)
        $heatmapData = $funciones->groupBy('dia_semana')->map(function ($funcionesDia) {
            return $funcionesDia->groupBy('hora')->map(function ($funcionesHora) {
                return [
                    'ocupacion_promedio' => $funcionesHora->avg('ocupacion_porcentaje'),
                    'funciones_count' => $funcionesHora->count(),
                ];
            });
        });

        return view('admin.dashboard.ocupacion', compact('funciones', 'heatmapData', 'fechaInicio', 'fechaFin'));
    }

    /**
     * Análisis de Confitería
     */
    public function confiteria(Request $request)
    {
        $empresaId = auth()->user()->empresa_id;
        $fechaInicio = $request->input('fecha_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', Carbon::now()->format('Y-m-d'));

        // Productos con margen real (si tienen costo calculado)
        $productosConMargen = Producto::where('empresa_id', $empresaId)
            ->where('es_venta_retail', true)
            ->whereNotNull('costo_total_unitario')
            ->select(
                'id',
                'nombre',
                'precio',
                'costo_total_unitario',
                DB::raw('(precio - costo_total_unitario) as margen_absoluto'),
                DB::raw('((precio - costo_total_unitario) / precio * 100) as margen_porcentual')
            )
            ->get();

        // Productos con bajo movimiento (sin ventas en el período)
        $productosBajoMovimiento = Producto::where('empresa_id', $empresaId)
            ->where('es_venta_retail', true)
            ->whereDoesntHave('ventas', function ($query) use ($fechaInicio, $fechaFin) {
                $query->whereBetween('fecha_hora', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
                    ->where('estado_pago', 'PAGADA');
            })
            ->select('id', 'nombre', 'stock_actual', 'precio')
            ->get();

        // Total de ventas de confitería (Sólo los productos, sin incluir tickets de ventas mixtas)
        $totalVentas = DB::table('producto_venta')
            ->join('ventas', 'producto_venta.venta_id', '=', 'ventas.id')
            ->where('ventas.empresa_id', $empresaId)
            ->where('ventas.estado_pago', 'PAGADA')
            ->whereBetween('ventas.fecha_hora', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
            ->sum(DB::raw('producto_venta.precio_venta * producto_venta.cantidad'));

        return view('admin.dashboard.confiteria', compact(
            'productosConMargen',
            'productosBajoMovimiento',
            'totalVentas',
            'fechaInicio',
            'fechaFin'
        ));
    }

    // ============================================
    // MÉTODOS AUXILIARES
    // ============================================

    // Tendencia de ventas últimos 30 días
    private function getTendenciaVentas($empresaId)
    {
        $fechaInicio = Carbon::now()->subDays(30);
        $fechaFin = Carbon::now();

        $ventas = Venta::select(
            DB::raw('DATE(fecha_hora) as fecha'),
            DB::raw('SUM(total) as total')
        )
            ->where('empresa_id', $empresaId)
            ->where('estado_pago', 'PAGADA')
            ->whereBetween('fecha_hora', [$fechaInicio, $fechaFin])
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        return $ventas->map(function ($v) {
            return [
                'fecha' => Carbon::parse($v->fecha)->format('d/m'),
                'total' => $v->total
            ];
        });
    }

    // Ocupación promedio por día de la semana (histórico reciente)
    private function getTendenciaOcupacion($empresaId)
    {
        // Analizar últimos 3 meses
        $fechaInicio = Carbon::now()->subMonths(3);

        $funciones = Funcion::with('sala')
            ->where('empresa_id', $empresaId)
            ->where('fecha_hora', '>=', $fechaInicio)
            ->get();

        $ocupacionPorDia = [
            'Monday' => [],
            'Tuesday' => [],
            'Wednesday' => [],
            'Thursday' => [],
            'Friday' => [],
            'Saturday' => [],
            'Sunday' => []
        ];

        foreach ($funciones as $funcion) {
            $total = $funcion->sala->capacidad_total ?? 0;
            if ($total == 0)
                continue;

            $vendidos = FuncionAsiento::where('funcion_id', $funcion->id)->where('estado', 'VENDIDO')->count();
            $porcentaje = ($vendidos / $total) * 100;

            $dia = $funcion->fecha_hora->format('l'); // Monday, Tuesday...
            $ocupacionPorDia[$dia][] = $porcentaje;
        }

        $promedios = [];
        $diasEsp = [
            'Monday' => 'Lun',
            'Tuesday' => 'Mar',
            'Wednesday' => 'Mié',
            'Thursday' => 'Jue',
            'Friday' => 'Vie',
            'Saturday' => 'Sáb',
            'Sunday' => 'Dom'
        ];

        foreach ($ocupacionPorDia as $diaIngles => $valores) {
            $promedio = count($valores) > 0 ? array_sum($valores) / count($valores) : 0;
            $promedios[$diasEsp[$diaIngles]] = round($promedio, 1);
        }

        // Ordenar lun a dom
        return [
            'Lun' => $promedios['Lun'],
            'Mar' => $promedios['Mar'],
            'Mié' => $promedios['Mié'],
            'Jue' => $promedios['Jue'],
            'Vie' => $promedios['Vie'],
            'Sáb' => $promedios['Sáb'],
            'Dom' => $promedios['Dom']
        ];
    }

    private function getKPIsDelDia($empresaId, $fecha)
    {
        $inicio = $fecha->copy()->startOfDay();
        $fin = $fecha->copy()->endOfDay();

        return $this->getKPIsPeriodo($empresaId, $inicio, $fin);
    }

    private function getKPIsPeriodo($empresaId, $inicio, $fin)
    {
        // Ingresos totales
        $ingresoTotal = Venta::where('empresa_id', $empresaId)
            ->where('estado_pago', 'PAGADA')
            ->whereBetween('fecha_hora', [$inicio, $fin])
            ->sum('total');

        // Tickets vendidos
        $ticketsVendidos = DB::table('venta_funcion_asientos')
            ->join('ventas', 'venta_funcion_asientos.venta_id', '=', 'ventas.id')
            ->where('ventas.empresa_id', $empresaId)
            ->where('ventas.estado_pago', 'PAGADA')
            ->whereBetween('ventas.fecha_hora', [$inicio, $fin])
            ->count();

        // Ocupación promedio
        $funcionesEnPeriodo = Funcion::where('empresa_id', $empresaId)
            ->whereBetween('fecha_hora', [$inicio, $fin])
            ->get();

        $ocupacionPromedio = 0;
        if ($funcionesEnPeriodo->count() > 0) {
            $totalOcupacion = 0;
            foreach ($funcionesEnPeriodo as $funcion) {
                $totalAsientos = $funcion->sala->capacidad_total ?? 0;
                $vendidos = FuncionAsiento::where('funcion_id', $funcion->id)
                    ->where('estado', 'VENDIDO')
                    ->count();
                if ($totalAsientos > 0) {
                    $totalOcupacion += ($vendidos / $totalAsientos) * 100;
                }
            }
            $ocupacionPromedio = $totalOcupacion / $funcionesEnPeriodo->count();
        }

        // Productos confitería vendidos
        $productosVendidos = DB::table('producto_venta')
            ->join('ventas', 'producto_venta.venta_id', '=', 'ventas.id')
            ->join('productos', 'producto_venta.producto_id', '=', 'productos.id')
            ->where('ventas.empresa_id', $empresaId)
            ->where('ventas.estado_pago', 'PAGADA')
            ->where('productos.es_venta_retail', true)
            ->whereBetween('ventas.fecha_hora', [$inicio, $fin])
            ->sum('producto_venta.cantidad');

        return [
            'ingreso_total' => $ingresoTotal,
            'tickets_vendidos' => $ticketsVendidos,
            'ocupacion_promedio' => round($ocupacionPromedio, 1),
            'productos_vendidos' => $productosVendidos,
        ];
    }
}
