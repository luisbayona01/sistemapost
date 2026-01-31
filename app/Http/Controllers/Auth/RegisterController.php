<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterEmpresaRequest;
use App\Models\Moneda;
use App\Models\SaaSPlan;
use App\Services\SubscriptionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function __construct(
        private SubscriptionService $subscriptionService
    ) {}

    /**
     * Mostrar formulario de registro de empresa
     */
    public function create(): View|RedirectResponse
    {
        // Si ya está autenticado, redirigir al panel
        if (Auth::check()) {
            return redirect()->route('panel');
        }

        $planes = SaaSPlan::activos()->get();
        $monedas = Moneda::all();
        $planSelected = request('plan', null);

        return view('auth.register', compact('planes', 'monedas', 'planSelected'));
    }

    /**
     * Procesar registro de empresa
     */
    public function store(RegisterEmpresaRequest $request): RedirectResponse
    {
        try {
            // Obtener el plan
            $plan = SaaSPlan::findOrFail($request->plan_id);

            // Datos de la empresa
            $empresaData = [
                'razon_social' => $request->empresa_nombre,
                'nombre_comercial' => $request->empresa_nombre,
                'nit' => $request->nit,
                'email' => $request->empresa_email ?? $request->email,
                'telefono' => $request->telefono,
                'moneda_id' => $request->moneda_id,
                'porcentaje_impuesto' => 19.00, // Default para Colombia
                'abreviatura_impuesto' => 'IVA',
            ];

            // Datos del usuario admin
            $userData = [
                'name' => $request->nombre_contacto,
                'email' => $request->email,
                'password' => $request->password,
            ];

            // Crear empresa, usuario y suscripción
            $resultado = $this->subscriptionService->createEmpresaWithSubscription(
                $empresaData,
                $userData,
                $plan->id
            );

            if (!$resultado['success']) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', $resultado['error'] ?? 'Error al registrar la empresa.');
            }

            // Autenticar al usuario automáticamente
            Auth::login($resultado['usuario']);

            return redirect()->route('panel')
                ->with('success', '¡Bienvenido! Tu empresa ha sido registrada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
