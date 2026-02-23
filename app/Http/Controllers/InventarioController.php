<?php

namespace App\Http\Controllers;

use App\Enums\TipoTransaccionEnum;
use App\Http\Requests\StoreInventarioRequest;
use App\Models\Inventario;
use App\Models\Kardex;
use App\Models\Producto;
use App\Models\Ubicacione;
use App\Services\ActivityLogService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class InventarioController extends Controller
{
    function __construct()
    {
        $this->middleware('check_producto_inicializado', ['only' => ['create', 'store']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $empresaId = auth()->user()->empresa_id;

        $inventario = Inventario::with(['producto.categoria', 'producto.presentacione'])
            ->whereHas('producto', function ($q) use ($empresaId) {
                $q->where('empresa_id', $empresaId)
                    ->where('estado', 1);
            })
            ->latest()
            ->get();

        $insumos = \App\Models\Insumo::where('empresa_id', $empresaId)->get();

        return view('inventario.index', compact('inventario', 'insumos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $producto = Producto::findOrfail($request->producto_id);
        $ubicaciones = Ubicacione::all();
        return view('inventario.create', compact('producto', 'ubicaciones'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInventarioRequest $request, Kardex $kardex): RedirectResponse
    {
        DB::beginTransaction();
        try {
            $kardex->crearRegistro($request->validated(), TipoTransaccionEnum::Apertura);
            Inventario::create($request->validated());
            DB::commit();
            ActivityLogService::log('Inicialiación de producto', 'Productos', $request->validated());
            return redirect()->route('productos.index')->with('success', 'Producto inicializado');
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error al inicializar el producto', ['error' => $e->getMessage()]);
            return redirect()->route('productos.index')->with('error', 'Ups, algo falló');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    /**
     * Ajuste rápido de inventario (Carga diaria/semanal)
     */
    public function ajusteRapido(Request $request, Kardex $kardex): RedirectResponse
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'required|numeric',
            'motivo' => 'required|string|max:255',
        ]);

        $empresaId = auth()->user()->empresa_id;
        $producto = Producto::findOrFail($request->producto_id);

        // El movimiento puede ser positivo (entrada) o negativo (salida)
        $cantidad = floatval($request->cantidad);

        DB::beginTransaction();
        try {
            // 1. Actualizar o Crear registro de inventario
            $inventario = Inventario::firstOrNew(['producto_id' => $producto->id]);
            $inventario->cantidad = ($inventario->cantidad ?? 0) + $cantidad;
            $inventario->empresa_id = $empresaId;
            $inventario->save();

            // 2. Registrar en Kardex
            // Si cantidad > 0 es una Compra/Ajuste Positivo, si < 0 es Ajuste Negativo
            $tipo = $cantidad >= 0 ? TipoTransaccionEnum::Compra : TipoTransaccionEnum::Ajuste;

            $kardex->crearRegistro([
                'producto_id' => $producto->id,
                'cantidad' => abs($cantidad), // enviamos absoluto porque Kardex deduce el tipo
                'costo_unitario' => $producto->precio ?? 0,
                'descripcion' => "Ajuste manual: " . $request->motivo,
            ], $tipo);

            DB::commit();
            return back()->with('success', 'Inventario actualizado correctamente para: ' . $producto->nombre);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error en ajuste de inventario', ['error' => $e->getMessage()]);
            return back()->with('error', 'Error al procesar el ajuste.');
        }
    }
}
