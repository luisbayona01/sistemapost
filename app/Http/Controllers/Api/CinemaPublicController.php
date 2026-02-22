<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Funcion;
use App\Models\Producto;
use App\Models\FuncionAsiento;
use App\Models\PrecioEntrada;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/**
 * API Pública para integración con sitios web externos
 * No requiere autenticación
 */
class CinemaPublicController extends Controller
{
    /**
     * GET /api/cinema/cartelera
     * Obtiene todas las películas en cartelera con sus funciones
     */
    public function getCartelera(): JsonResponse
    {
        $peliculas = Producto::where('estado_pelicula', 'cartelera')
            ->with(['distribuidor'])
            ->get()
            ->map(function ($pelicula) {
                return [
                    'id' => $pelicula->id,
                    'titulo' => $pelicula->nombre,
                    'slug' => $pelicula->slug,
                    'sinopsis' => $pelicula->sinopsis,
                    'duracion' => $pelicula->duracion,
                    'clasificacion' => $pelicula->clasificacion,
                    'genero' => $pelicula->genero,
                    'es_preventa' => (bool) $pelicula->es_preventa,
                    'afiche' => $pelicula->img_path ? asset($pelicula->img_path) : null,
                    'trailer_url' => $pelicula->trailer_url,
                    'distribuidor' => $pelicula->distribuidor?->nombre,
                    'fecha_estreno' => $pelicula->fecha_estreno?->format('Y-m-d'),
                    'fecha_fin' => $pelicula->fecha_fin_exhibicion?->format('Y-m-d'),
                ];

            });

        return response()->json([
            'success' => true,
            'data' => $peliculas
        ]);
    }

    /**
     * GET /api/cinema/peliculas/{id}/funciones
     * Obtiene todas las funciones disponibles de una película
     */
    public function getFuncionesPelicula($peliculaId): JsonResponse
    {
        $funciones = Funcion::where('producto_id', $peliculaId)
            ->where('fecha_hora', '>=', Carbon::now())
            ->with(['sala', 'precios'])
            ->orderBy('fecha_hora')
            ->get()
            ->map(function ($funcion) {
                $asientosDisponibles = FuncionAsiento::where('funcion_id', $funcion->id)
                    ->disponibles()
                    ->count();

                return [
                    'id' => $funcion->id,
                    'fecha_hora' => $funcion->fecha_hora->format('Y-m-d H:i'),
                    'sala' => [
                        'id' => $funcion->sala->id,
                        'nombre' => $funcion->sala->nombre,
                        'capacidad' => $funcion->sala->capacidad,
                    ],
                    'asientos_disponibles' => $asientosDisponibles,
                    'precios' => $funcion->precios->map(fn($p) => [
                        'id' => $p->id,
                        'tipo' => $p->nombre,
                        'precio' => (float) $p->precio,
                    ]),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $funciones
        ]);
    }

    /**
     * GET /api/cinema/funciones/{id}/asientos
     * Obtiene el mapa de asientos de una función específica
     */
    public function getAsientosFuncion($funcionId): JsonResponse
    {
        $funcion = Funcion::with(['sala'])->findOrFail($funcionId);

        // Mapa de la sala
        $mapa = $funcion->sala->configuracion_json;

        // Asientos vendidos/reservados
        $asientosOcupados = FuncionAsiento::where('funcion_id', $funcionId)
            ->whereIn('estado', [FuncionAsiento::ESTADO_VENDIDO, FuncionAsiento::ESTADO_RESERVADO])
            ->pluck('codigo_asiento')
            ->toArray();

        // Marcar asientos ocupados en el mapa
        $mapaConEstado = collect($mapa)->map(function ($seat) use ($asientosOcupados) {
            if ($seat['tipo'] === 'asiento') {
                $codigo = $seat['fila'] . $seat['num'];
                $seat['disponible'] = !in_array($codigo, $asientosOcupados);
            }
            return $seat;
        });

        return response()->json([
            'success' => true,
            'data' => [
                'funcion_id' => $funcion->id,
                'sala' => $funcion->sala->nombre,
                'mapa' => $mapaConEstado,
                'capacidad_total' => $funcion->sala->capacidad,
                'asientos_disponibles' => $funcion->sala->capacidad - count($asientosOcupados),
            ]
        ]);
    }

    /**
     * GET /api/cinema/precios
     * Obtiene la lista de precios activos
     */
    public function getPrecios(): JsonResponse
    {
        $precios = PrecioEntrada::where('activo', true)
            ->get()
            ->map(fn($p) => [
                'id' => $p->id,
                'nombre' => $p->nombre,
                'precio' => (float) $p->precio,
                'descripcion' => $p->descripcion ?? null,
            ]);

        return response()->json([
            'success' => true,
            'data' => $precios
        ]);
    }

    /**
     * POST /api/cinema/reservar
     * Reserva temporal de asientos (15 minutos)
     * IMPLEMENTACIÓN ATÓMICA: Previene venta doble
     */
    public function reservarAsientos(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'funcion_id' => 'required|exists:funciones,id',
            'asientos' => 'required|array|min:1|max:10',
            'asientos.*' => 'required|string',
        ]);

        try {
            return \Illuminate\Support\Facades\DB::transaction(function () use ($validated) {
                $reservados = [];

                // Bloquear las filas para evitar lectura simultánea por otro proceso
                $asientosBD = FuncionAsiento::where('funcion_id', $validated['funcion_id'])
                    ->whereIn('codigo_asiento', $validated['asientos'])
                    ->lockForUpdate() // BLOQUEO CRÍTICO
                    ->get();

                // Verificar disponibilidad de TODOS
                foreach ($validated['asientos'] as $codigo) {
                    $asiento = $asientosBD->where('codigo_asiento', $codigo)->first();

                    if (!$asiento || !$asiento->isAvailable()) {
                        return response()->json([
                            'success' => false,
                            'message' => "El asiento {$codigo} ya no está disponible. Alguien más lo acaba de reservar/comprar."
                        ], 409); // Conflict
                    }
                }

                // Si todos están disponibles, procedemos a reservar
                $tokenReserva = bin2hex(random_bytes(16));

                foreach ($asientosBD as $asiento) {
                    $asiento->update([
                        'estado' => FuncionAsiento::ESTADO_RESERVADO,
                        'session_id' => $tokenReserva,
                        'reservado_hasta' => now()->addMinutes(FuncionAsiento::RESERVATION_TIMEOUT_MINUTES),
                    ]);
                    $reservados[] = $asiento->codigo_asiento;
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Asientos reservados exitosamente',
                    'data' => [
                        'asientos_reservados' => $reservados,
                        'token_reserva' => $tokenReserva,
                        'expira_en_minutos' => 15,
                    ]
                ]);
            });

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al reservar asientos: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * POST /api/cinema/confirmar-compra
     * Confirma la compra y genera el ticket
     * (Se debe llamar después de procesar el pago)
     */
    public function confirmarCompra(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'funcion_id' => 'required|exists:funciones,id',
            'asientos' => 'required|array',
            'precio_entrada_id' => 'required|exists:precio_entradas,id',
            'metodo_pago' => 'required|string',
            'referencia_pago' => 'nullable|string', // ID de transacción del gateway
        ]);

        try {
            // Aquí se integraría con el sistema de ventas existente
            // Por ahora retornamos la estructura esperada

            return response()->json([
                'success' => true,
                'message' => 'Compra confirmada exitosamente',
                'data' => [
                    'venta_id' => null, // Se generará con la integración real
                    'ticket_url' => null, // URL del PDF del ticket
                    'qr_code' => null, // Código QR para validación
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al confirmar compra: ' . $e->getMessage()
            ], 500);
        }
    }
}
