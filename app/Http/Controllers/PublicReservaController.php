<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PublicReservaController extends Controller
{
    protected $cinemaService;

    public function __construct(\App\Services\CinemaService $cinemaService)
    {
        $this->cinemaService = $cinemaService;
    }

    /**
     * Procesar el bloqueo temporal de asientos.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'funcion_id' => 'required|exists:funciones,id',
            'asientos' => 'required|array|min:1',
        ]);

        $sessionId = session()->getId();
        $successCount = 0;
        $failedSeats = [];

        foreach ($data['asientos'] as $codigo) {
            $res = $this->cinemaService->reservarAsiento(
                $data['funcion_id'],
                $codigo,
                $sessionId
            );

            if ($res) {
                $successCount++;
            } else {
                $failedSeats[] = $codigo;
            }
        }

        if ($successCount === 0) {
            return response()->json([
                'success' => false,
                'message' => 'No se pudo reservar ningún asiento. Por favor, intenta con otros.',
                'failed' => $failedSeats
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Asientos bloqueados exitosamente. Tienes 5 minutos para completar el proceso.',
            'count' => $successCount,
            'failed' => $failedSeats
        ]);
    }

    /**
     * Mostrar la pantalla de pago/checkout.
     */
    public function checkout()
    {
        $tenant = app('currentTenant');
        $sessionId = session()->getId();

        $asientos = \App\Models\FuncionAsiento::where('session_id', $sessionId)
            ->where('estado', \App\Models\FuncionAsiento::ESTADO_RESERVADO)
            ->where('reservado_hasta', '>=', now())
            ->with(['funcion.pelicula', 'funcion.sala'])
            ->get();

        if ($asientos->isEmpty()) {
            return redirect()->route('public.cartelera')->with('error', 'Tu reserva ha expirado o no tienes asientos seleccionados.');
        }

        $funcion = $asientos->first()->funcion;
        $total = $asientos->count() * $funcion->precio;

        return view('public.checkout', compact('asientos', 'funcion', 'tenant', 'total'));
    }

    /**
     * Procesar el pago (próximamente con Stripe).
     */
    public function pagar(Request $request)
    {
        // Placeholder para el flujo de pago
        return response()->json(['message' => 'Pago en desarrollo']);
    }
}
