<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Insumo;
use App\Models\Producto;
use App\Models\InventarioAjuste;
use App\Services\Inventory\InventoryService;
use App\Enums\TipoTransaccionEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AjusteController extends Controller
{
    protected $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function create()
    {
        $insumos = Insumo::all();
        $productos = Producto::all();
        $motivos = ['merma', 'daño', 'error conteo', 'vencimiento', 'otro'];

        return view('admin.inventario-avanzado.ajuste.create', compact('insumos', 'productos', 'motivos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'insumo_id' => 'nullable|exists:insumos,id',
            'producto_id' => 'nullable|exists:productos,id',
            'cantidad' => 'required|numeric|min:0.001',
            'tipo' => 'required|in:INCREMENTO,DECREMENTO',
            'motivo' => 'required|in:merma,daño,error conteo,vencimiento,otro',
            'observaciones' => 'nullable|string|max:500',
        ]);

        if (!$validated['insumo_id'] && !$validated['producto_id']) {
            return back()->with('error', 'Debe seleccionar un insumo o un producto.');
        }

        try {
            DB::transaction(function () use ($validated) {
                $ajuste = InventarioAjuste::create([
                    'empresa_id' => auth()->user()->empresa_id,
                    'user_id' => auth()->id(),
                    'insumo_id' => $validated['insumo_id'],
                    'producto_id' => $validated['producto_id'],
                    'cantidad' => $validated['cantidad'],
                    'tipo' => $validated['tipo'],
                    'motivo' => $validated['motivo'],
                    'observaciones' => $validated['observaciones'],
                ]);

                if ($validated['insumo_id']) {
                    $this->procesarAjusteInsumo($validated);
                } else {
                    $this->procesarAjusteProducto($validated);
                }
            });

            return redirect()->route('inventario-avanzado.index')->with('success', 'Ajuste de inventario registrado correctamente.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al procesar ajuste: ' . $e->getMessage());
        }
    }

    private function procesarAjusteInsumo($data)
    {
        if ($data['tipo'] === 'INCREMENTO') {
            $this->inventoryService->registrarEntrada(
                $data['insumo_id'],
                $data['cantidad'],
                0, // Costo 0 para ajustes positivos manuales o promediado? Asumimos 0 si es recupero
                'AJUSTE_MANUAL',
                null,
                [
                    'tipo_transaccion' => TipoTransaccionEnum::Ajuste,
                    'motivo' => $data['motivo'],
                    'descripcion' => "Ajuste manual (+): " . $data['motivo']
                ]
            );
        } else {
            $this->inventoryService->reducirStockFIFO($data['insumo_id'], $data['cantidad'], [
                'tipo_transaccion' => TipoTransaccionEnum::Ajuste,
                'descripcion' => "Ajuste manual (-): " . $data['motivo']
            ]);
        }
    }

    private function procesarAjusteProducto($data)
    {
        $producto = Producto::findOrFail($data['producto_id']);
        $inventario = $producto->inventario;

        if (!$inventario) {
            throw new \Exception('El producto no tiene un registro de inventario inicial.');
        }

        $cantidad = $data['cantidad'];
        if ($data['tipo'] === 'DECREMENTO') {
            $inventario->disminuirStock($cantidad);
        } else {
            $inventario->aumentarStock($cantidad);
        }

        // Kardex para Producto
        $kardex = new \App\Models\Kardex();
        $kardex->crearRegistro([
            'producto_id' => $data['producto_id'],
            'cantidad' => $cantidad,
            'costo_unitario' => $producto->costo_unitario ?? 0,
            'motivo' => $data['motivo'],
            'descripcion' => "Ajuste manual (" . $data['tipo'] . "): " . $data['motivo']
        ], TipoTransaccionEnum::Ajuste);
    }
}
