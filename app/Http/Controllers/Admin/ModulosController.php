<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessConfiguration;
use Illuminate\Http\Request;

class ModulosController extends Controller
{
    public function index()
    {
        $empresaId = auth()->user()->empresa_id;
        $config = BusinessConfiguration::where('empresa_id', $empresaId)->firstOrFail();

        return view('admin.configuracion.modulos', compact('config'));
    }

    public function update(Request $request)
    {
        $empresaId = auth()->user()->empresa_id;
        $config = BusinessConfiguration::where('empresa_id', $empresaId)->firstOrFail();

        $modules = [
            'cinema' => $request->has('module_cinema'),
            'pos' => $request->has('module_pos'),
            'inventory' => $request->has('module_inventory'),
            'reports' => $request->has('module_reports'),
            'api' => $request->has('module_api'),
        ];

        // Si cambió el tipo de negocio y no se marcaron módulos manualmente (o es una petición de cambio de tipo),
        // podríamos aplicar presets. Por ahora, respetamos lo que venga del form.

        $config->update([
            'business_type' => $request->business_type,
            'modules_enabled' => $modules,
        ]);

        return redirect()->back()->with('success', 'Configuración de ' . ucfirst($request->business_type) . ' actualizada exitosamente');
    }
}
