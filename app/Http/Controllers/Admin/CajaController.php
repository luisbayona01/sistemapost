<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Caja, Movimiento, Venta};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class CajaController extends Controller
{
    /**
     * VISTA 1 — Estado de Cajas (Admin: ve todas las cajas de hoy)
     * Cajero: ve solo SU caja + botón Cerrar Caja
     */
    public function index()
    {
        $user = auth()->user();
        $isAdmin = $user->hasRole(['Root', 'Gerente', 'administrador']);

        if ($isAdmin) {
            // ADMIN: Ve todas las cajas ABIERTAS (sin importar fecha) + las cerradas de HOY
            $cajas = Caja::with('user')
                ->withSum(['ventas' => fn($q) => $q->where('estado_pago', 'PAGADA')], 'total')
                ->where(function ($query) {
                    $query->where('estado', 'ABIERTA')
                        ->orWhereDate('fecha_apertura', Carbon::today());
                })
                ->latest()
                ->paginate(15);

            return view('admin.caja.index', compact('cajas', 'isAdmin'));
        }

        // CAJERO: Ve solo SU caja de hoy
        $miCaja = Caja::with('user')
            ->withSum(['ventas' => fn($q) => $q->where('estado_pago', 'PAGADA')], 'total')
            ->where('user_id', $user->id)
            ->where('estado', 'ABIERTA')
            ->first();

        $totales = $miCaja ? $this->calcularTotalesContables($miCaja) : null;

        return view('admin.caja.mi-caja', compact('miCaja', 'totales'));
    }

    /**
     * Abrir una nueva caja (Solo admin manual — el POS auto-abre)
     */
    public function abrirCaja(Request $request)
    {
        $request->validate([
            'monto_inicial' => 'required|numeric|min:0',
        ]);

        $empresaId = auth()->user()->empresa_id;

        $cajaAbierta = Caja::where('user_id', auth()->id())
            ->where('empresa_id', $empresaId)
            ->where('estado', 'ABIERTA')
            ->first();

        if ($cajaAbierta) {
            return back()->with('error', 'Ya tienes una caja abierta.');
        }

        $caja = Caja::create([
            'empresa_id' => $empresaId,
            'user_id' => auth()->id(),
            'fecha_apertura' => now(),
            'monto_inicial' => $request->monto_inicial,
            'estado' => 'ABIERTA',
            'nombre' => 'Caja ' . auth()->user()->name . ' - ' . now()->format('d/m'),
        ]);

        return redirect()->route('pos.index')
            ->with('success', "Caja abierta correctamente con $" . number_format($request->monto_inicial));
    }

    /**
     * VISTA 2 — Cierre de Caja (por cajero)
     */
    public function mostrarCierre($cajaId)
    {
        $caja = Caja::with(['user', 'movimientos'])
            ->where('empresa_id', auth()->user()->empresa_id)
            ->findOrFail($cajaId);

        if ($caja->estado !== 'ABIERTA') {
            return redirect()->route('admin.cajas.index')->with('info', 'La caja ya fue cerrada.');
        }

        $totales = $this->calcularTotalesContables($caja);

        return view('admin.caja.cierre', compact('caja', 'totales'));
    }

    /**
     * VISTA NEW — Cierre de Caja (Wizard)
     */
    public function mostrarCierreWizard($cajaId)
    {
        $caja = Caja::with(['user', 'movimientos'])
            ->where('empresa_id', auth()->user()->empresa_id)
            ->findOrFail($cajaId);

        if ($caja->estado !== 'ABIERTA') {
            return redirect()->route('admin.cajas.index')->with('info', 'La caja ya fue cerrada.');
        }

        $totales = $this->calcularTotalesContables($caja);

        return view('admin.caja.cierre-wizard', compact('caja', 'totales'));
    }

    /**
     * Procesar el cierre manual del cajero
     */
    public function cerrar(Request $request, $cajaId)
    {
        $request->validate([
            'monto_declarado' => 'required|numeric|min:0',
            'efectivo_declarado' => 'nullable|numeric|min:0',
            'tarjeta_declarada' => 'nullable|numeric|min:0',
            'otros_declarado' => 'nullable|numeric|min:0',
            'notas' => 'nullable|string|max:500',
            'motivo_diferencia' => 'nullable|string|max:1000',
        ]);

        $caja = Caja::where('empresa_id', auth()->user()->empresa_id)
            ->whereIn('estado', ['ABIERTA', 'CERRADA'])
            ->findOrFail($cajaId);

        if ($caja->estado === 'CERRADA' && $caja->estado_cierre !== 'reabierto_admin') {
            return back()->with('error', 'Esta caja ya está cerrada.');
        }

        // Validar Ventas Pendientes
        $ventasPendientes = Venta::where('caja_id', $cajaId)
            ->where('estado_pago', 'PENDIENTE')
            ->count();

        if ($ventasPendientes > 0) {
            return back()->with('error', "No se puede cerrar la caja. Hay $ventasPendientes ventas en proceso/pendientes.");
        }

        $totales = $this->calcularTotalesCaja($caja);

        // 1. Calcular Diferencia Consolidada (Fuente de Verdad Única)
        $monto_declarado_total = (float) $request->monto_declarado;
        $monto_esperado_total = (float) $totales['monto_final_esperado_total'];
        $diferencia_consolidada = $monto_declarado_total - $monto_esperado_total;

        // 2. Desglose informativo (Opcional, pero se guarda en base de datos)
        $efectivo_declarado = $request->efectivo_declarado ?? 0;
        $tarjeta_declarada = $request->tarjeta_declarada ?? 0;
        $otros_declarado = $request->otros_declarado ?? 0;

        // 3. Validación de motivo por el total, no por desgloses individuales
        $umbralMotivo = config('caja.umbral_diferencia_motivo', 3000);
        if (abs($diferencia_consolidada) > $umbralMotivo && empty($request->motivo_diferencia)) {
            return back()->with('error', "Es obligatorio justificar la diferencia consolidada mayor a $$umbralMotivo.");
        }

        DB::transaction(function () use ($caja, $request, $totales, $diferencia_consolidada, $tarjeta_declarada, $otros_declarado, $efectivo_declarado) {
            $isReopening = $caja->estado_cierre === 'reabierto_admin';

            $caja->update([
                'fecha_cierre' => now(),
                'monto_final_declarado' => $request->monto_declarado,
                'monto_final_esperado' => $totales['monto_final_esperado_total'],
                'diferencia' => $diferencia_consolidada,
                'efectivo_declarado' => $efectivo_declarado,
                'tarjeta_declarado' => $tarjeta_declarada,
                'otros_declarado' => $otros_declarado,
                'tarjeta_esperada' => $totales['ventas_tarjeta'],
                // Guardamos desgloses de diferencias si el usuario quiere auditar después, 
                // pero el sistema ya no bloquea ni alerta por ellos individualmente.
                'diferencia_tarjeta' => $tarjeta_declarada - $totales['ventas_tarjeta'],
                'estado' => 'CERRADA',
                'estado_cierre' => 'normal',
                'cerrado_por' => auth()->id(),
                'cierre_user_id' => auth()->id(),
                'cierre_at' => now(),
                'notas_cierre' => $request->notas,
                'motivo_diferencia' => $request->motivo_diferencia,
                'saldo_final' => $request->efectivo_declarado ?? $request->monto_declarado, // El saldo físico real es el efectivo
                'cierre_version' => $isReopening ? DB::raw('cierre_version + 1') : 1,
            ]);
        });

        return redirect()->route('admin.cajas.reporte-cierre', $caja->id)
            ->with('success', 'Caja cerrada exitosamente. Arqueo completado.');
    }

    /**
     * ACCIÓN ADMINISTRATIVA: Reabrir Cierre (Corrección)
     */
    public function reabrirCierre(Request $request, $cajaId)
    {
        if (!auth()->user()->hasRole(['Root', 'Gerente', 'administrador'])) {
            abort(403, 'No tienes permisos para reabrir cierres.');
        }

        $request->validate([
            'motivo' => 'required|string|min:10|max:500',
        ]);

        $caja = Caja::where('empresa_id', auth()->user()->empresa_id)
            ->where('estado', 'CERRADA')
            ->findOrFail($cajaId);

        if ($caja->fecha_cierre && $caja->fecha_cierre->diffInDays(now()) > 7) {
            return back()->with('error', 'No se puede reabrir un cierre de hace más de 7 días.');
        }

        $caja->update([
            'estado_cierre' => 'reabierto_admin',
            'estado' => 'ABIERTA',
            'reabierto_por_user_id' => auth()->id(),
            'reabierto_at' => now(),
            'motivo_reapertura' => $request->motivo,
        ]);

        return redirect()->route('admin.cajas.mostrar-cierre-wizard', $caja->id)
            ->with('warning', 'Caja reabierta para corrección. Por favor, realice el cierre nuevamente.');
    }

    /**
     * VISTA 3 — Cierre del Día (Consolidado Real - Accounting View)
     * Suma TODAS las ventas del día independientemente del estado de la caja.
     */
    public function cierreDia()
    {
        $accountingService = app(\App\Services\AccountingService::class);
        $empresaId = auth()->user()->empresa_id;
        $fechaOperativa = $accountingService->getActiveDay($empresaId);

        // 1. Cajas del día (para control operativo)
        $cajasAbiertas = Caja::where('empresa_id', $empresaId)
            ->whereDate('fecha_apertura', $fechaOperativa)
            ->where('estado', 'ABIERTA')
            ->count();

        // 2. CONSOLIDADO CONTABLE (Fuente de verdad: Ventas filtradas por DÍA OPERATIVO)

        // Total General
        $totalGeneral = Venta::where('empresa_id', $empresaId)
            ->where('fecha_operativa', $fechaOperativa->format('Y-m-d'))
            ->where('estado_pago', 'PAGADA')
            ->sum('total');

        // Confitería 
        $ventasConfiteria = DB::table('producto_venta')
            ->join('ventas', 'producto_venta.venta_id', '=', 'ventas.id')
            ->where('ventas.empresa_id', $empresaId)
            ->where('ventas.fecha_operativa', $fechaOperativa->format('Y-m-d'))
            ->where('ventas.estado_pago', 'PAGADA')
            ->sum(DB::raw('producto_venta.precio_venta * producto_venta.cantidad'));

        // Boletería 
        $ventasBoleteria = max(0, $totalGeneral - $ventasConfiteria);

        // Desglose por Método de Pago
        $efectivoSystem = Venta::where('empresa_id', $empresaId)
            ->where('fecha_operativa', $fechaOperativa->format('Y-m-d'))
            ->where('estado_pago', 'PAGADA')
            ->where('metodo_pago', 'EFECTIVO')
            ->sum('total');

        $tarjetaSystem = Venta::where('empresa_id', $empresaId)
            ->where('fecha_operativa', $fechaOperativa->format('Y-m-d'))
            ->where('estado_pago', 'PAGADA')
            ->where('metodo_pago', 'TARJETA')
            ->sum('total');

        // 3. CONSOLIDADO DE DINERO (Lo que hay en las cajas CERRADAS)
        $cajasCerradas = Caja::where('empresa_id', $empresaId)
            ->whereDate('fecha_apertura', $fechaOperativa)
            ->where('estado', 'CERRADA')
            ->get();

        $dineroDeclarado = $cajasCerradas->sum('monto_final_declarado');
        $diferenciaEfectivo = $cajasCerradas->sum('diferencia');
        $diferenciaTarjeta = $cajasCerradas->sum('diferencia_tarjeta');

        // INC Consolidado
        $totalInc = Venta::where('empresa_id', $empresaId)
            ->where('fecha_operativa', $fechaOperativa->format('Y-m-d'))
            ->where('estado_pago', 'PAGADA')
            ->sum('inc_total');

        // Estructura para la vista
        $consolidado = [
            'total_general' => (float) $totalGeneral,
            'total_entradas' => (float) $ventasBoleteria,
            'total_dulceria' => (float) $ventasConfiteria,
            'total_inc' => (float) $totalInc,
            'total_neto_dulceria' => (float) ($ventasConfiteria - $totalInc),
            'total_efectivo' => (float) $efectivoSystem,
            'total_tarjeta' => (float) $tarjetaSystem,
            'dinero_declarado' => (float) $dineroDeclarado,
            'diferencia_efectivo' => (float) $diferenciaEfectivo,
            'diferencia_tarjeta' => (float) $diferenciaTarjeta,
            'cajas_cerradas_count' => $cajasCerradas->count(),
            'fecha' => $fechaOperativa->format('d/m/Y')
        ];

        // Retrieve open boxes data for preview
        $cajasAbiertasData = [];
        if ($cajasAbiertas > 0) {
            $cajasAbiertasData = Caja::with('user')
                ->withSum(['ventas' => fn($q) => $q->where('estado_pago', 'PAGADA')], 'total')
                ->where('empresa_id', $empresaId)
                ->whereDate('fecha_apertura', $fechaOperativa)
                ->where('estado', 'ABIERTA')
                ->get();
        }

        // Listado de Ventas para Auditabilidad (Pilar 3)
        $ventasHoy = Venta::with(['cliente.persona', 'user', 'asientosCinema'])
            ->where('empresa_id', $empresaId)
            ->where('fecha_operativa', $fechaOperativa->format('Y-m-d'))
            ->where('estado_pago', 'PAGADA')
            ->latest()
            ->get();

        return view('admin.caja.cierre-dia', compact('consolidado', 'cajasAbiertas', 'cajasAbiertasData', 'ventasHoy'));
    }

    public function reporteCierre($cajaId)
    {
        $caja = Caja::with(['user', 'movimientos.venta'])
            ->where('empresa_id', auth()->user()->empresa_id)
            ->findOrFail($cajaId);

        $totales = $this->calcularTotalesCaja($caja);

        return view('admin.caja.reporte-cierre', compact('caja', 'totales'));
    }

    public function descargarPDF($cajaId)
    {
        $caja = Caja::with(['user', 'movimientos'])
            ->where('empresa_id', auth()->user()->empresa_id)
            ->findOrFail($cajaId);

        $totales = $this->calcularTotalesCaja($caja); // Usar el método unificado
        $pdf = Pdf::loadView('admin.caja.cierre-pdf', compact('caja', 'totales'));
        return $pdf->download("cierre-caja-{$caja->id}.pdf");
    }

    public function descargarExcel($cajaId)
    {
        $caja = Caja::with(['user', 'movimientos.venta'])
            ->where('empresa_id', auth()->user()->empresa_id)
            ->findOrFail($cajaId);

        $totales = $this->calcularTotalesCaja($caja); // Usar el método unificado
        return view('admin.caja.cierre-excel', compact('caja', 'totales'));
    }

    // ============================================
    // LÓGICA CONTABLE QUIRÚRGICA (Sin venta mixta)
    // ============================================

    /**
     * Deprecated: Use calcularTotalesCaja which is now unified.
     * Keeping for safety if used elsewhere, but redirecting logic.
     */
    private function calcularTotalesContables(Caja $caja)
    {
        return $this->calcularTotalesCaja($caja);
    }

    /**
     * Helper antiguo para consolidado por IDs. 
     * Ya no se usa en cierreDia nuevo (usa fechas), pero se mantiene por si acaso.
     */
    private function calcularConsolidadoIds($ids)
    {
        // ... (Keep existing implementation or refactor if strictly needed, 
        // but cierreDia ignores this now)
        return [];
    }

    /**
     * Calcular totales de caja para el cierre - VERSIÓN MAESTRA FASE 4
     */
    private function calcularTotalesCaja(Caja $caja)
    {
        // 1. Total General Vendido
        $totalVentas = \App\Models\Venta::where('caja_id', $caja->id)
            ->where('estado_pago', 'PAGADA')
            ->sum('total');

        // 2. Calcular Venta de Productos (Confitería) Global
        $ventasConfiteria = DB::table('producto_venta')
            ->join('ventas', 'producto_venta.venta_id', '=', 'ventas.id')
            ->where('ventas.caja_id', $caja->id)
            ->where('ventas.estado_pago', 'PAGADA')
            ->sum(DB::raw('producto_venta.precio_venta * producto_venta.cantidad'));

        // 3. Calcular INC (Impuesto Nacional al Consumo - 8%) sobre confitería
        $incTotal = \App\Models\Venta::where('caja_id', $caja->id)
            ->where('estado_pago', 'PAGADA')
            ->sum('inc_total');

        // 4. Calcular Venta de Entradas (Boletería) por diferencia
        $ventasBoleteria = max(0, $totalVentas - $ventasConfiteria);

        // 5. Ventas Mixtas eliminadas conceptualmente
        $ventasMixtas = 0;

        $ventasEfectivo = \App\Models\Venta::where('caja_id', $caja->id)
            ->where('estado_pago', 'PAGADA')
            ->where('metodo_pago', 'EFECTIVO')
            ->sum('total');

        $ingresos = Movimiento::where('caja_id', $caja->id)
            ->where('tipo', 'INGRESO')
            ->whereNull('venta_id')
            ->sum('monto');

        $egresos = Movimiento::where('caja_id', $caja->id)
            ->whereIn('tipo', ['EGRESO', 'DEVOLUCION'])
            ->sum('monto');

        $base = $caja->monto_inicial ?? 0;

        // Efectivo que DEBERÍA haber en caja
        $efectivoEsperado = $base + $ventasEfectivo + $ingresos - $egresos;

        $ventasTarjeta = \App\Models\Venta::where('caja_id', $caja->id)
            ->where('estado_pago', 'PAGADA')
            ->where('metodo_pago', 'TARJETA')
            ->sum('total');

        return [
            'ventas_boleteria' => (float) $ventasBoleteria,
            'ventas_entradas' => (float) $ventasBoleteria,
            'ventas_confiteria' => (float) $ventasConfiteria,
            'ventas_dulceria' => (float) $ventasConfiteria,
            'total_inc' => (float) $incTotal,
            'ventas_netas_confiteria' => (float) ($ventasConfiteria - $incTotal),
            'ventas_mixtas' => (float) $ventasMixtas,
            'total_ventas' => (float) $totalVentas,
            'total_general' => (float) $totalVentas,
            'ventas_efectivo' => (float) $ventasEfectivo,
            'ventas_tarjeta' => (float) $ventasTarjeta,
            'ingresos_manuales' => (float) $ingresos,
            'egresos_manuales' => (float) $egresos,
            'efectivo_esperado' => (float) $efectivoEsperado,
            'monto_final_esperado_total' => (float) ($efectivoEsperado + $ventasTarjeta),
            'monto_inicial' => (float) $base,
            'cantidad_transacciones' => \App\Models\Venta::where('caja_id', $caja->id)->where('estado_pago', 'PAGADA')->count(),
        ];
    }

    /**
     * Mostrar formulario de cierre simplificado
     */
    public function cerrarSimple($cajaId)
    {
        $caja = Caja::with(['user'])
            ->where('empresa_id', auth()->user()->empresa_id)
            ->where('estado', 'ABIERTA')
            ->findOrFail($cajaId);

        $totales = $this->calcularTotalesCaja($caja);

        return view('admin.caja.cerrar-simple', compact('caja', 'totales'));
    }

    /**
     * Procesar cierre de caja con validación
     */
    public function procesarCierre(Request $request, $cajaId)
    {
        $request->validate([
            'efectivo_declarado' => 'required|numeric|min:0',
            'tarjeta_declarado' => 'nullable|numeric|min:0',
            'otros_declarado' => 'nullable|numeric|min:0',
            'notas' => 'nullable|string|max:500',
        ]);

        $caja = Caja::where('empresa_id', auth()->user()->empresa_id)
            ->where('estado', 'ABIERTA')
            ->findOrFail($cajaId);

        $totales = $this->calcularTotalesCaja($caja);

        // Calcular totales declarados
        $efectivoDeclarado = floatval($request->efectivo_declarado);
        $tarjetaDeclarado = floatval($request->tarjeta_declarado ?? 0);
        $otrosDeclarado = floatval($request->otros_declarado ?? 0);
        $totalDeclarado = $efectivoDeclarado + $tarjetaDeclarado + $otrosDeclarado;

        // Calcular diferencia (Usamos el total esperado consolidado)
        $diferencia = $totalDeclarado - $totales['monto_final_esperado_total'];

        // CERRAR LA CAJA
        DB::transaction(function () use ($caja, $request, $totales, $totalDeclarado, $diferencia, $efectivoDeclarado, $tarjetaDeclarado, $otrosDeclarado) {
            $caja->update([
                'fecha_cierre' => now(),
                'monto_final_declarado' => $totalDeclarado,
                'monto_final_esperado' => $totales['monto_final_esperado_total'],
                'diferencia' => $diferencia,
                'estado' => 'CERRADA',
                'cerrado_por' => auth()->id(),
                'notas_cierre' => $request->notas,
                'efectivo_declarado' => $efectivoDeclarado,
                'tarjeta_declarado' => $tarjetaDeclarado,
                'otros_declarado' => $otrosDeclarado,
            ]);
        });

        return redirect()->route('admin.caja.reporte-cierre', $caja->id)
            ->with('success', 'Caja cerrada exitosamente');
    }
}
