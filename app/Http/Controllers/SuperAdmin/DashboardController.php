<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Dashboard de Super Admin con métricas globales
     */
    public function index(): View
    {
        // Total de empresas
        $totalEmpresas = Empresa::count();

        // Empresas activas
        $empresasActivas = Empresa::where('estado', 'activa')
                                   ->where('estado_suscripcion', 'active')
                                   ->count();

        // Empresas con suscripciones en período de prueba
        $empresasEnTrial = Empresa::where('estado_suscripcion', 'trial')->count();

        // Empresas suspendidas
        $empresasSuspendidas = Empresa::where('estado', 'suspendida')->count();

        // Suscripciones vencidas
        $suscripcionesVencidas = Empresa::where('estado_suscripcion', 'past_due')->count();

        // Ingresos totales por suscripciones (suma de tarifas)
        $ingresosTotales = DB::table('empresa')
            ->where('estado', 'activa')
            ->sum('tarifa_servicio_monto');

        // Ventas totales en el sistema
        $ventasTotales = DB::table('ventas')->sum('total');

        // Últimas 10 empresas registradas
        $ultimasEmpresas = Empresa::latest('created_at')
                                   ->limit(10)
                                   ->get();

        return view('super-admin.dashboard', compact(
            'totalEmpresas',
            'empresasActivas',
            'empresasEnTrial',
            'empresasSuspendidas',
            'suscripcionesVencidas',
            'ingresosTotales',
            'ventasTotales',
            'ultimasEmpresas',
        ));
    }
}
