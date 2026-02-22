<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Funcion;
use App\Models\Producto;
use App\Models\Distribuidor;
use App\Models\Sala;
use App\Models\PrecioEntrada;
use App\Models\FuncionAsiento;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/**
 * API Administrativa para gestión del cine
 * Requiere autenticación
 */
class CinemaAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * GET /api/admin/peliculas
     * Lista todas las películas (con filtros opcionales)
     */
    public function getPeliculas(Request $request): JsonResponse
    {
        $query = Producto::with(['distribuidor', 'marca', 'categoria']);

        // Filtros opcionales
        if ($request->has('estado')) {
            $query->where('estado_pelicula', $request->estado);
        }

        if ($request->has('search')) {
            $query->where('nombre', 'like', '%' . $request->search . '%');
        }

        $peliculas = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $peliculas
        ]);
    }

    /**
     * POST /api/admin/peliculas
     * Crea una nueva película
     */
    public function storePelicula(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'sinopsis' => 'nullable|string',
            'duracion' => 'nullable|string|max:50',
            'clasificacion' => 'nullable|string|max:10',
            'genero' => 'nullable|string',
            'trailer_url' => 'nullable|url',
            'distribuidor_id' => 'nullable|exists:distribuidores,id',
            'estado_pelicula' => 'nullable|in:cartelera,proximamente,archivada',
            'fecha_estreno' => 'nullable|date',
            'fecha_fin_exhibicion' => 'nullable|date|after_or_equal:fecha_estreno',
        ]);

        $validated['empresa_id'] = auth()->user()->empresa_id;
        $pelicula = Producto::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Película creada exitosamente',
            'data' => $pelicula
        ], 201);
    }

    /**
     * PUT /api/admin/peliculas/{id}
     * Actualiza una película
     */
    public function updatePelicula(Request $request, $id): JsonResponse
    {
        $pelicula = Producto::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'sometimes|string|max:255',
            'sinopsis' => 'nullable|string',
            'duracion' => 'nullable|string|max:50',
            'clasificacion' => 'nullable|string|max:10',
            'genero' => 'nullable|string',
            'trailer_url' => 'nullable|url',
            'distribuidor_id' => 'nullable|exists:distribuidores,id',
            'estado_pelicula' => 'nullable|in:cartelera,proximamente,archivada',
            'fecha_estreno' => 'nullable|date',
            'fecha_fin_exhibicion' => 'nullable|date|after_or_equal:fecha_estreno',
        ]);

        $pelicula->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Película actualizada exitosamente',
            'data' => $pelicula
        ]);
    }

    /**
     * GET /api/admin/funciones
     * Lista todas las funciones
     */
    public function getFunciones(Request $request): JsonResponse
    {
        $query = Funcion::with(['producto', 'sala', 'precios']);

        if ($request->has('fecha_desde')) {
            $query->where('fecha_hora', '>=', $request->fecha_desde);
        }

        if ($request->has('fecha_hasta')) {
            $query->where('fecha_hora', '<=', $request->fecha_hasta);
        }

        $funciones = $query->orderBy('fecha_hora', 'desc')->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $funciones
        ]);
    }

    /**
     * POST /api/admin/funciones
     * Crea una nueva función
     */
    public function storeFuncion(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'sala_id' => 'required|exists:salas,id',
            'producto_id' => 'required|exists:productos,id',
            'fecha_hora' => 'required|date|after:now',
            'precio' => 'required|numeric|min:0',
            'precios_entrada' => 'nullable|array',
        ]);

        $validated['empresa_id'] = auth()->user()->empresa_id;
        $funcion = Funcion::create($validated);

        // Attach precios
        if (!empty($validated['precios_entrada'])) {
            $funcion->precios()->attach($validated['precios_entrada']);
        }

        // Generate seats
        $sala = Sala::find($validated['sala_id']);
        $mapa = $sala->configuracion_json;

        foreach ($mapa as $seat) {
            if ($seat['tipo'] === 'asiento') {
                FuncionAsiento::create([
                    'funcion_id' => $funcion->id,
                    'codigo_asiento' => $seat['fila'] . $seat['num'],
                    'estado' => FuncionAsiento::ESTADO_DISPONIBLE
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Función creada exitosamente',
            'data' => $funcion->load(['producto', 'sala', 'precios'])
        ], 201);
    }

    /**
     * PUT /api/admin/funciones/{id}
     * Actualiza una función (con validación de ventas)
     */
    public function updateFuncion(Request $request, $id): JsonResponse
    {
        $funcion = Funcion::findOrFail($id);

        // Check for sales
        $ventasCount = FuncionAsiento::where('funcion_id', $id)
            ->where('estado', FuncionAsiento::ESTADO_VENDIDO)
            ->count();

        if ($ventasCount > 0 && !$request->boolean('force_update')) {
            return response()->json([
                'success' => false,
                'message' => "Esta función tiene {$ventasCount} asientos vendidos",
                'requires_confirmation' => true,
                'ventas_count' => $ventasCount
            ], 400);
        }

        $validated = $request->validate([
            'sala_id' => 'sometimes|exists:salas,id',
            'producto_id' => 'sometimes|exists:productos,id',
            'fecha_hora' => 'sometimes|date',
            'precio' => 'sometimes|numeric|min:0',
        ]);

        $funcion->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Función actualizada exitosamente',
            'data' => $funcion
        ]);
    }

    /**
     * DELETE /api/admin/funciones/{id}
     * Elimina una función (solo si no tiene ventas)
     */
    public function deleteFuncion($id): JsonResponse
    {
        $funcion = Funcion::findOrFail($id);

        $ventasCount = FuncionAsiento::where('funcion_id', $id)
            ->where('estado', FuncionAsiento::ESTADO_VENDIDO)
            ->count();

        if ($ventasCount > 0) {
            return response()->json([
                'success' => false,
                'message' => "No se puede eliminar: tiene {$ventasCount} asientos vendidos"
            ], 400);
        }

        $funcion->delete();

        return response()->json([
            'success' => true,
            'message' => 'Función eliminada exitosamente'
        ]);
    }

    /**
     * GET /api/admin/reportes/ventas
     * Reporte de ventas por período
     */
    public function getReporteVentas(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'fecha_desde' => 'required|date',
            'fecha_hasta' => 'required|date|after_or_equal:fecha_desde',
            'pelicula_id' => 'nullable|exists:productos,id',
            'sala_id' => 'nullable|exists:salas,id',
        ]);

        // Aquí iría la lógica de reportes
        // Por ahora retornamos estructura de ejemplo

        return response()->json([
            'success' => true,
            'data' => [
                'periodo' => [
                    'desde' => $validated['fecha_desde'],
                    'hasta' => $validated['fecha_hasta'],
                ],
                'total_ventas' => 0,
                'total_ingresos' => 0,
                'ocupacion_promedio' => 0,
                'peliculas_mas_vendidas' => [],
            ]
        ]);
    }

    /**
     * GET /api/admin/distribuidores
     * Lista distribuidores
     */
    public function getDistribuidores(): JsonResponse
    {
        $distribuidores = Distribuidor::where('activo', true)->get();

        return response()->json([
            'success' => true,
            'data' => $distribuidores
        ]);
    }

    /**
     * GET /api/admin/precios
     * Gestión de precios
     */
    public function getPrecios(): JsonResponse
    {
        $precios = PrecioEntrada::all();

        return response()->json([
            'success' => true,
            'data' => $precios
        ]);
    }

    /**
     * PUT /api/admin/precios/{id}
     * Actualiza un precio
     */
    public function updatePrecio(Request $request, $id): JsonResponse
    {
        $precio = PrecioEntrada::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'sometimes|string|max:255',
            'precio' => 'sometimes|numeric|min:0',
            'activo' => 'sometimes|boolean',
        ]);

        $precio->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Precio actualizado exitosamente',
            'data' => $precio
        ]);
    }
}
