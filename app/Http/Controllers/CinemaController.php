<?php

namespace App\Http\Controllers;

use App\Models\Funcion;
use App\Models\Sala;
use App\Models\PrecioEntrada;
use App\Services\CinemaService;
use App\Services\TicketService;
use App\Actions\Cinema\ProcesarVentaCinemaAction;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Models\Venta;

class CinemaController extends Controller
{
    protected CinemaService $cinemaService;
    protected TicketService $ticketService;
    protected ProcesarVentaCinemaAction $procesarVentaAction;

    public function __construct(
        CinemaService $cinemaService,
        TicketService $ticketService,
        ProcesarVentaCinemaAction $procesarVentaAction
    ) {
        $this->cinemaService = $cinemaService;
        $this->ticketService = $ticketService;
        $this->procesarVentaAction = $procesarVentaAction;
        $this->middleware('auth');
    }

    /**
     * Exportar el ticket de cinema en PDF
     */
    public function exportarTicket(Venta $venta): Response
    {
        $pdf = $this->ticketService->generarTicketPDF($venta, 'pdf.ticket-cinema');
        return $pdf->stream('ticket-cinema-' . $venta->id . '.pdf');
    }

    /**
     * Listar funciones disponibles (REDIRECCIÓN A POS)
     */
    public function index()
    {
        return redirect()->route('pos.index');
    }

    /**
     * Mostrar el mapa de asientos de una función
     */
    public function showSeatMap(Funcion $funcion): View
    {
        $funcion->load(['sala', 'asientos', 'precios']);
        $sala = $funcion->sala;

        // Estructura del mapa - Manejar si ya es un array o necesita decodificarse
        $mapa = is_array($sala->configuracion_json)
            ? $sala->configuracion_json
            : json_decode($sala->configuracion_json, true);

        $precios = $funcion->precios->where('activo', true);

        return view('cinema.seat-map', compact('funcion', 'sala', 'mapa', 'precios'));
    }

    /**
     * Procesar la venta de múltiples asientos (Conversión Reserva -> Venta)
     */
    public function venderAsiento(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'funcion_id' => 'required',
                'asientos' => 'required|array',
                'precio_entrada_id' => 'required',
                'metodo_pago' => 'nullable|string'
            ]);

            $data['session_id'] = $request->session()->getId();

            $venta = $this->procesarVentaAction->execute($data);

            return response()->json([
                'success' => true,
                'venta_id' => $venta->id,
                'message' => 'Venta realizada con éxito'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Reservar/Bloquear múltiples asientos (AJAX)
     */
    public function reservarAsiento(Request $request): JsonResponse
    {
        $request->validate([
            'funcion_id' => 'required|exists:funciones,id',
            'asientos' => 'required|array',
        ]);

        $success = true;
        foreach (is_array($request->asientos) ? $request->asientos : [] as $codigo) {
            $res = $this->cinemaService->reservarAsiento(
                $request->funcion_id,
                $codigo,
                session()->getId()
            );
            if (!$res)
                $success = false;
        }

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Asientos bloqueados temporalmente' : 'Algunos asientos no pudieron ser bloqueados'
        ]);
    }
}
