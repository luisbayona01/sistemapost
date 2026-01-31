<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Rules\CajaCerradaRule;
use App\Services\ActivityLogService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class CajaController extends Controller
{
    /**
     * Display a listing of the resource.
     * Global Scope filtra automáticamente por empresa_id
     */
    public function index(): View
    {
        $cajas = Caja::where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('caja.index', compact('cajas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('caja.create');
    }

    /**
     * Store a newly created resource in storage.
     * Crea caja con empresa_id y validar que no exista caja abierta
     */
    public function store(Request $request): RedirectResponse
    {
        $empresa = auth()->user()->empresa;

        // Validaciones
        $request->validate([
            'saldo_inicial' => ['required', 'numeric', 'min:0'],
        ]);

        // Validar que usuario no tenga caja abierta en esta empresa
        $cajaAbierta = Caja::where('user_id', Auth::id())
            ->where('empresa_id', $empresa->id)
            ->abierta()
            ->first();

        if ($cajaAbierta) {
            return redirect()->route('cajas.index')
                ->with('error', "Ya tienes una caja abierta desde las {$cajaAbierta->hora_apertura}");
        }

        try {
            $caja = Caja::create([
                'empresa_id' => $empresa->id,
                'user_id' => Auth::id(),
                'saldo_inicial' => $request->get('saldo_inicial'),
                'fecha_apertura' => now()->format('Y-m-d'),
                'hora_apertura' => now()->format('H:i:s'),
            ]);

            ActivityLogService::log(
                'Apertura de caja',
                'Cajas',
                [
                    'caja_id' => $caja->id,
                    'saldo_inicial' => $caja->saldo_inicial,
                    'empresa_id' => $empresa->id,
                ]
            );

            return redirect()->route('movimientos.index', ['caja_id' => $caja->id])
                ->with('success', 'Caja aperturada correctamente');
        } catch (Throwable $e) {
            Log::error('Error al crear la caja', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->route('cajas.index')
                ->with('error', 'Error al abrir la caja: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Caja $caja): View
    {
        // Verificar que la caja pertenece al usuario y empresa
        if ($caja->user_id !== Auth::id() || $caja->empresa_id !== auth()->user()->empresa_id) {
            abort(403, 'No tienes permiso para ver esta caja');
        }

        $movimientos = $caja->movimientos()->latest()->get();
        $saldo = $caja->calcularSaldo();
        $estado = $caja->estaAbierta() ? 'ABIERTA' : 'CERRADA';

        return view('caja.show', compact('caja', 'movimientos', 'saldo', 'estado'));
    }

    /**
     * Show the form for closing the cash register.
     */
    public function showCloseForm(Caja $caja): View
    {
        // Verificar que la caja pertenece al usuario y empresa
        if ($caja->user_id !== Auth::id() || $caja->empresa_id !== auth()->user()->empresa_id) {
            abort(403, 'No tienes permiso para cerrar esta caja');
        }

        if ($caja->estaCerrada()) {
            return redirect()->route('cajas.index')
                ->with('warning', 'Esta caja ya está cerrada');
        }

        $saldoCalculado = $caja->calcularSaldo();
        $movimientos = $caja->movimientos()->latest()->get();

        return view('caja.close', compact('caja', 'saldoCalculado', 'movimientos'));
    }

    /**
     * Close the cash register.
     */
    public function close(Caja $caja, Request $request): RedirectResponse
    {
        // Verificar que la caja pertenece al usuario y empresa
        if ($caja->user_id !== Auth::id() || $caja->empresa_id !== auth()->user()->empresa_id) {
            abort(403, 'No tienes permiso para cerrar esta caja');
        }

        if ($caja->estaCerrada()) {
            return redirect()->route('cajas.index')
                ->with('warning', 'Esta caja ya está cerrada');
        }

        $request->validate([
            'saldo_final' => ['required', 'numeric', 'min:0'],
        ]);

        DB::beginTransaction();
        try {
            $saldoCalculado = $caja->calcularSaldo();
            $saldoFinal = $request->get('saldo_final');
            $diferencia = $saldoFinal - $saldoCalculado;

            // Usar método cerrar() del modelo
            $caja->cerrar([
                'saldo_final' => $saldoFinal,
                'fecha_cierre' => now()->format('Y-m-d'),
                'hora_cierre' => now()->format('H:i:s'),
                'diferencia' => $diferencia,
            ]);

            ActivityLogService::log(
                'Cierre de caja',
                'Cajas',
                [
                    'caja_id' => $caja->id,
                    'saldo_inicial' => $caja->saldo_inicial,
                    'saldo_final' => $saldoFinal,
                    'diferencia' => $diferencia,
                ]
            );

            DB::commit();

            $mensaje = $diferencia === 0
                ? 'Caja cerrada correctamente sin diferencias'
                : "Caja cerrada con diferencia de \$" . number_format($diferencia, 2);

            return redirect()->route('cajas.index')
                ->with('success', $mensaje);
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error al cerrar la caja', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()
                ->with('error', 'Error al cerrar la caja: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource (soft delete).
     */
    public function destroy(Caja $caja): RedirectResponse
    {
        // Verificar que la caja pertenece al usuario y empresa
        if ($caja->user_id !== Auth::id() || $caja->empresa_id !== auth()->user()->empresa_id) {
            abort(403, 'No tienes permiso para eliminar esta caja');
        }

        try {
            // No permitir eliminar cajas cerradas
            if ($caja->estaCerrada()) {
                return redirect()->route('cajas.index')
                    ->with('error', 'No puedes eliminar una caja cerrada');
            }

            $caja->update(['estado' => 0]);
            ActivityLogService::log('Eliminación de caja (no cierre formal)', 'Cajas', ['estado' => $caja->estado]);
            return redirect()->route('cajas.index')
                ->with('success', 'Caja eliminada');
        } catch (Throwable $e) {
            Log::error('Error al eliminar la caja', ['error' => $e->getMessage()]);
            return redirect()->route('cajas.index')
                ->with('error', 'Error al eliminar la caja: ' . $e->getMessage());
        }
    }
}
