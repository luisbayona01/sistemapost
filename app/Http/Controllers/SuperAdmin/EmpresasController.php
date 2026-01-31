<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class EmpresasController extends Controller
{
    /**
     * Listar todas las empresas
     */
    public function index(): View
    {
        $empresas = Empresa::with('plan')
                           ->orderBy('created_at', 'desc')
                           ->paginate(15);

        return view('super-admin.empresas.index', compact('empresas'));
    }

    /**
     * Ver detalle de una empresa
     */
    public function show(Empresa $empresa): View
    {
        $empresa->load([
            'plan',
            'moneda',
            'users',
            'ventas',
        ]);

        $estadisticas = [
            'total_ventas' => $empresa->ventas()->sum('total'),
            'numero_ventas' => $empresa->ventas()->count(),
            'usuarios' => $empresa->users()->count(),
            'tarifa_acumulada' => $empresa->tarifa_servicio_monto,
        ];

        return view('super-admin.empresas.show', compact('empresa', 'estadisticas'));
    }

    /**
     * Suspender una empresa
     */
    public function suspend(Empresa $empresa): RedirectResponse
    {
        $empresa->update([
            'estado' => 'suspendida',
        ]);

        return redirect()->route('super-admin.empresas.show', $empresa)
                       ->with('success', 'Empresa suspendida exitosamente.');
    }

    /**
     * Activar una empresa
     */
    public function activate(Empresa $empresa): RedirectResponse
    {
        $empresa->update([
            'estado' => 'activa',
        ]);

        return redirect()->route('super-admin.empresas.show', $empresa)
                       ->with('success', 'Empresa activada exitosamente.');
    }
}
