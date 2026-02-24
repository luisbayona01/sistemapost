<?php
/**
 * SuperAdmin Empreses Controller
 * 
 * Part of Phase 7.14: Modo Dios
 * Handles SaaS-wide tenant management, impersonation, and module control.
 */

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\User;
use App\Models\SaaSPlan;
use App\Models\Moneda;
use App\Models\BusinessConfiguration;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EmpresasController extends Controller
{
    /**
     * Listar todas las empresas con métricas básicas
     */
    public function index(): View
    {
        $empresas = Empresa::with(['plan', 'moneda'])
            ->withCount(['users', 'ventas'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('super-admin.empresas.index', compact('empresas'));
    }

    /**
     * Formulario para crear una nueva empresa
     */
    public function create(): View
    {
        $planes = SaaSPlan::where('activo', true)->get();
        $monedas = Moneda::all();

        return view('super-admin.empresas.create', compact('planes', 'monedas'));
    }

    /**
     * Almacenar nueva empresa y su admin inicial
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'slug' => 'required|string|unique:empresa,slug|max:255',
            'ruc' => 'required|string|max:20',
            'correo_empresa' => 'required|email|max:255',
            'plan_id' => 'required|exists:saas_plans,id',
            'moneda_id' => 'required|exists:monedas,id',
            'admin_name' => 'required|string|max:255',
            'admin_username' => 'required|string|unique:users,username|max:255',
            'admin_email' => 'required|email|unique:users,email|max:255',
            'admin_password' => 'required|string|min:8',
        ]);

        try {
            DB::beginTransaction();

            // 1. Crear Empresa
            $empresa = Empresa::create([
                'nombre' => $request->nombre,
                'slug' => Str::slug($request->slug),
                'propietario' => $request->admin_name,
                'ruc' => $request->ruc,
                'correo' => $request->correo_empresa,
                'moneda_id' => $request->moneda_id,
                'plan_id' => $request->plan_id,
                'estado' => 'activa',
                'estado_suscripcion' => 'active',
                'porcentaje_impuesto' => 19,
                'abreviatura_impuesto' => 'IVA'
            ]);

            // 2. Crear Usuario Admin
            $user = User::create([
                'name' => $request->admin_name,
                'username' => $request->admin_username,
                'email' => $request->admin_email,
                'password' => Hash::make($request->admin_password),
                'empresa_id' => $empresa->id,
                'estado' => 1
            ]);

            // 3. Asignar Rol Administrador con contexto de Team
            app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($empresa->id);
            $user->assignRole('administrador');

            // 4. Inicializar Configuración de Módulos
            BusinessConfiguration::create([
                'empresa_id' => $empresa->id,
                'business_type' => 'generic',
                'modules_enabled' => [
                    'pos' => true,
                    'inventory' => true,
                    'reports' => true,
                    'cinema' => false,
                ]
            ]);

            app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

            DB::commit();

            return redirect()->route('root.empresas.index')
                ->with('success', "Empresa '{$empresa->nombre}' creada correctamente.");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear empresa: ' . $e->getMessage());
        }
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

        $config = BusinessConfiguration::firstOrCreate(
            ['empresa_id' => $empresa->id],
            ['modules_enabled' => ['pos' => true, 'inventory' => true, 'reports' => true]]
        );

        $estadisticas = [
            'total_ventas' => $empresa->ventas()->sum('total'),
            'numero_ventas' => $empresa->ventas()->count(),
            'usuarios' => $empresa->users()->count(),
            'tarifa_acumulada' => $empresa->tarifa_servicio_monto,
        ];

        return view('super-admin.empresas.show', compact('empresa', 'estadisticas', 'config'));
    }

    /**
     * Actualizar módulos habilitados
     */
    public function updateModules(Request $request, Empresa $empresa): RedirectResponse
    {
        $config = BusinessConfiguration::where('empresa_id', $empresa->id)->first();

        $modules = [
            'pos' => $request->has('module_pos'),
            'cinema' => $request->has('module_cinema'),
            'inventory' => $request->has('module_inventory'),
            'reports' => $request->has('module_reports'),
            'api' => $request->has('module_api'),
        ];

        $config->update(['modules_enabled' => $modules]);

        return redirect()->back()->with('success', 'Configuración de módulos actualizada.');
    }

    /**
     * Suspender una empresa
     */
    public function suspend(Empresa $empresa): RedirectResponse
    {
        $empresa->update(['estado' => 'suspendida']);
        return redirect()->back()->with('success', 'Empresa suspendida exitosamente.');
    }

    /**
     * Activar una empresa
     */
    public function activate(Empresa $empresa): RedirectResponse
    {
        $empresa->update(['estado' => 'activa']);
        return redirect()->back()->with('success', 'Empresa activada exitosamente.');
    }

    /**
     * Impersonar a una empresa (Busca al primer administrador)
     */
    public function impersonateEmpresa(Empresa $empresa): RedirectResponse
    {
        $admin = User::where('empresa_id', $empresa->id)
            ->role(['administrador', 'Gerente', 'Root'])
            ->first();

        if (!$admin) {
            return redirect()->back()->with('error', 'No se encontró un administrador para esta empresa.');
        }

        return $this->impersonate($admin);
    }

    /**
     * Impersonar a un usuario (Login como...)
     */
    public function impersonate(User $user): RedirectResponse
    {
        $originalId = Auth::id();
        $originalUser = Auth::user();

        if ($user->id === $originalId) {
            return redirect()->back()->with('error', 'Ya eres este usuario.');
        }

        // Registrar en la Bóveda de Auditoría
        \App\Models\AuditForge::create([
            'empresa_id' => $user->empresa_id,
            'user_id' => $originalId,
            'event' => 'IMPERSONATION_START',
            'model_type' => 'App\Models\User',
            'model_id' => $user->id,
            'ip_address' => request()->ip(),
            'new_values' => [
                'impersonated_user' => $user->email,
                'impersonated_name' => $user->name,
                'root_user' => $originalUser->email
            ],
            'occurred_at' => now()
        ]);

        session(['original_user' => $originalId]);
        Auth::login($user);

        return redirect()->route('admin.dashboard.index')
            ->with('success', "Ahora operando como: {$user->name} ({$user->empresa->nombre})");
    }
}
