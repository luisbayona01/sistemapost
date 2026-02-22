<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Services\DevolucionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DevolucionController extends Controller
{
    protected $devolucionService;

    public function __construct(DevolucionService $devolucionService)
    {
        $this->devolucionService = $devolucionService;
    }

    /**
     * Procesar devolución de una venta completa
     */
    public function store(Request $request, Venta $venta)
    {
        $request->validate([
            'motivo' => 'required|string|min:10',
            'reintegrar_stock' => 'required|boolean',
            'auth_pin' => 'required|string|size:4'
        ]);

        // Verificar PIN Administrativo
        $supervisor = \App\Models\User::role(['Gerente', 'Root'])
            ->where('pin_code', $request->auth_pin)
            ->first();

        if (!$supervisor) {
            return response()->json([
                'success' => false,
                'message' => 'PIN de autorización inválido o sin privilegios de supervisor.'
            ], 403);
        }

        try {
            $devolucion = $this->devolucionService->procesarDevolucion(
                $venta,
                $request->motivo,
                $request->reintegrar_stock
            );

            return response()->json([
                'success' => true,
                'message' => 'Devolución procesada exitosamente. El inventario ha sido actualizado.',
                'devolucion' => $devolucion
            ]);

        } catch (\Exception $e) {
            Log::error("Error en devolución Venta #{$venta->id}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Listado de devoluciones (para reportes)
     */
    public function index()
    {
        $devoluciones = \App\Models\Devolucion::with(['venta', 'user'])
            ->latest()
            ->paginate(20);

        return view('admin.reportes.devoluciones', compact('devoluciones'));
    }
}
