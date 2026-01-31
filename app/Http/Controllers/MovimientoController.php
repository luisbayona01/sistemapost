<?php

namespace App\Http\Controllers;

use App\Enums\MetodoPagoEnum;
use App\Http\Requests\StoreMovimientoRequest;
use App\Models\Caja;
use App\Models\Movimiento;
use App\Services\ActivityLogService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class MovimientoController extends Controller
{
    function __construct()
    {
        $this->middleware('check_movimiento_caja_user', ['only' => ['index', 'create', 'store']]);
    }

    /**
     * Display a listing of the resource.
     * Global Scope filtra automáticamente por empresa_id
     */
    public function index(Request $request): View
    {
        $caja = Caja::findOrfail($request->caja_id);

        // Verificar que la caja pertenece al usuario y empresa
        if ($caja->user_id !== Auth::id() || $caja->empresa_id !== auth()->user()->empresa_id) {
            abort(403, 'No tienes permiso para ver esta caja');
        }

        $movimientos = $caja->movimientos()->latest()->get();
        $saldoActual = $caja->calcularSaldo();

        return view('movimiento.index', compact('caja', 'movimientos', 'saldoActual'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $caja_id = $request->get('caja_id');
        $caja = Caja::findOrfail($caja_id);

        // Verificar que la caja pertenece al usuario y empresa
        if ($caja->user_id !== Auth::id() || $caja->empresa_id !== auth()->user()->empresa_id) {
            abort(403, 'No tienes permiso para esta caja');
        }

        // Validar que la caja esté abierta
        if (!$caja->estaAbierta()) {
            return redirect()->route('cajas.index')
                ->with('error', 'La caja no está abierta');
        }

        $optionsMetodoPago = MetodoPagoEnum::cases();

        return view('movimiento.create', compact('optionsMetodoPago', 'caja_id', 'caja'));
    }

    /**
     * Store a newly created resource in storage.
     * Crea movimiento con empresa_id y user_id automático
     */
    public function store(StoreMovimientoRequest $request): RedirectResponse
    {
        try {
            $caja = Caja::findOrfail($request->get('caja_id'));

            // Verificar que la caja pertenece al usuario y empresa
            if ($caja->user_id !== Auth::id() || $caja->empresa_id !== auth()->user()->empresa_id) {
                abort(403, 'No tienes permiso para esta caja');
            }

            // Validar que la caja esté abierta
            if (!$caja->estaAbierta()) {
                return redirect()->route('cajas.index')
                    ->with('error', 'La caja no está abierta');
            }

            // Crear movimiento con empresa_id y user_id
            $movimientoData = array_merge($request->validated(), [
                'empresa_id' => auth()->user()->empresa_id,
                'user_id' => Auth::id(),
            ]);

            $movimiento = Movimiento::create($movimientoData);

            ActivityLogService::log(
                'Creación de movimiento',
                'Movimientos',
                [
                    'movimiento_id' => $movimiento->id,
                    'tipo' => $movimiento->tipo,
                    'monto' => $movimiento->monto,
                    'caja_id' => $caja->id,
                ]
            );

            $mensaje = $movimiento->esIngreso()
                ? 'Ingreso registrado correctamente'
                : 'Egreso registrado correctamente';

            return redirect()->route('movimientos.index', ['caja_id' => $request->caja_id])
                ->with('success', $mensaje);
        } catch (Throwable $e) {
            Log::error('Error al crear el movimiento', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->route('movimientos.index', ['caja_id' => $request->caja_id])
                ->with('error', 'Error al registrar el movimiento: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Movimiento $movimiento): View
    {
        // Verificar que el movimiento pertenece a la empresa
        if ($movimiento->empresa_id !== auth()->user()->empresa_id) {
            abort(403, 'No tienes permiso para ver este movimiento');
        }

        return view('movimiento.show', compact('movimiento'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Movimiento $movimiento): RedirectResponse
    {
        // Verificar que el movimiento pertenece a la empresa
        if ($movimiento->empresa_id !== auth()->user()->empresa_id) {
            abort(403, 'No tienes permiso para eliminar este movimiento');
        }

        try {
            $caja_id = $movimiento->caja_id;
            $movimiento->delete();

            ActivityLogService::log('Eliminación de movimiento', 'Movimientos', ['movimiento_id' => $movimiento->id]);

            return redirect()->route('movimientos.index', ['caja_id' => $caja_id])
                ->with('success', 'Movimiento eliminado');
        } catch (Throwable $e) {
            Log::error('Error al eliminar el movimiento', ['error' => $e->getMessage()]);
            return redirect()->back()
                ->with('error', 'Error al eliminar el movimiento: ' . $e->getMessage());
        }
    }
}
