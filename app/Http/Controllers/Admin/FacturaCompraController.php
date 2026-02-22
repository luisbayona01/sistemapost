<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{FacturaCompra, InventarioMovimiento, Producto, Proveedore, Documento, Kardex};
use App\Enums\TipoTransaccionEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FacturaCompraController extends Controller
{
    public function index()
    {
        $facturas = FacturaCompra::with(['user', 'proveedor.persona', 'movimientos.producto'])
            ->where('empresa_id', auth()->user()->empresa_id)
            ->orderBy('fecha_compra', 'desc')
            ->paginate(20);

        $mesActual = Carbon::now()->startOfMonth();
        $totalMes = FacturaCompra::where('empresa_id', auth()->user()->empresa_id)
            ->where('fecha_compra', '>=', $mesActual)
            ->sum('total_pagado');

        return view('admin.facturas.index', compact('facturas', 'totalMes'));
    }

    public function crear()
    {
        $productos = Producto::where('empresa_id', auth()->user()->empresa_id)
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'stock_actual', 'precio']);

        $proveedores = Proveedore::with('persona')
            ->where('empresa_id', auth()->user()->empresa_id)
            ->get();

        $documentos = Documento::all();

        return view('admin.facturas.crear', compact('productos', 'proveedores', 'documentos'));
    }

    public function guardar(Request $request)
    {
        $request->validate([
            'proveedor_id' => 'required|exists:proveedores,id',
            'fecha_compra' => 'required|date',
            'total_pagado' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.producto_id' => 'required|exists:productos,id',
            'items.*.cantidad' => 'required|numeric|min:0.01',
            'items.*.costo_unitario' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $factura = FacturaCompra::create([
                'empresa_id' => auth()->user()->empresa_id,
                'user_id' => auth()->id(),
                'proveedor_id' => $request->proveedor_id,
                'numero_factura' => $request->numero_factura,
                'fecha_compra' => $request->fecha_compra,
                'total_pagado' => $request->total_pagado,
                'impuesto_tipo' => $request->impuesto_tipo,
                'impuesto_porcentaje' => $request->impuesto_porcentaje,
                'impuesto_valor' => $request->impuesto_valor ?? 0,
                'subtotal_calculado' => $request->subtotal_calculado ?? $request->total_pagado,
                'notas' => $request->notas,
            ]);

            foreach ($request->items as $item) {
                // 1. Crear Movimiento de Inventario
                InventarioMovimiento::create([
                    'empresa_id' => auth()->user()->empresa_id,
                    'producto_id' => $item['producto_id'],
                    'factura_id' => $factura->id,
                    'cantidad' => $item['cantidad'],
                    'costo_unitario' => $item['costo_unitario'],
                    'origen' => 'FACTURA_COMPRA',
                ]);

                // 2. Actualizar Producto
                $producto = Producto::find($item['producto_id']);
                $producto->increment('stock_actual', $item['cantidad']);
                if ($item['costo_unitario'] > 0) {
                    $producto->update(['costo_unitario' => $item['costo_unitario']]);
                }

                // 3. Agregar entrada en Kardex
                (new Kardex())->crearRegistro([
                    'producto_id' => $item['producto_id'],
                    'cantidad' => $item['cantidad'],
                    'costo_unitario' => $item['costo_unitario'],
                    'compra_id' => $factura->id,
                    'descripcion' => "Compra Factura #{$factura->numero_factura} - Proveedor: " . $factura->proveedor->persona->razon_social,
                    'empresa_id' => auth()->user()->empresa_id,
                    'fecha' => $factura->fecha_compra
                ], TipoTransaccionEnum::Compra);
            }
        });

        return redirect()->route('admin.facturas.index')
            ->with('success', '✅ Factura y múltiples productos cargados correctamente.');
    }

    public function reporteMensual(Request $request)
    {
        $mes = $request->get('mes', date('m'));
        $anio = $request->get('anio', date('Y'));

        $facturas = FacturaCompra::with(['proveedor.persona'])
            ->where('empresa_id', auth()->user()->empresa_id)
            ->whereMonth('fecha_compra', $mes)
            ->whereYear('fecha_compra', $anio)
            ->orderBy('fecha_compra', 'asc')
            ->get();

        $totales = [
            'conteo' => $facturas->count(),
            'neto' => $facturas->sum('subtotal_calculado'),
            'iva' => $facturas->sum('impuesto_valor'),
            'total' => $facturas->sum('total_pagado'),
        ];

        return view('admin.facturas.reporte_mensual', compact('facturas', 'totales', 'mes', 'anio'));
    }

    public function editar($id)
    {
        $factura = FacturaCompra::with('movimientos')
            ->where('empresa_id', auth()->user()->empresa_id)
            ->findOrFail($id);

        $productos = Producto::where('empresa_id', auth()->user()->empresa_id)
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'stock_actual', 'precio']);

        $proveedores = Proveedore::with('persona')
            ->where('empresa_id', auth()->user()->empresa_id)
            ->get();

        return view('admin.facturas.editar', compact('factura', 'productos', 'proveedores'));
    }

    public function actualizar(Request $request, $id)
    {
        // En un sistema real, la edición de factura con múltiples productos implica revertir todo y volver a crear.
        // Por simplicidad y UX, a menudo se prefiere anular y crear nueva, pero aquí implementaremos una lógica de actualización.

        $request->validate([
            'proveedor_id' => 'required|exists:proveedores,id',
            'fecha_compra' => 'required|date',
            'total_pagado' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
        ]);

        $factura = FacturaCompra::where('empresa_id', auth()->user()->empresa_id)
            ->findOrFail($id);

        DB::transaction(function () use ($request, $factura) {
            // REVERTIR stock anterior
            foreach ($factura->movimientos as $mov) {
                Producto::where('id', $mov->producto_id)->decrement('stock_actual', $mov->cantidad);
            }
            $factura->movimientos()->delete();

            // Actualizar cabecera
            $factura->update([
                'proveedor_id' => $request->proveedor_id,
                'numero_factura' => $request->numero_factura,
                'fecha_compra' => $request->fecha_compra,
                'total_pagado' => $request->total_pagado,
                'impuesto_tipo' => $request->impuesto_tipo,
                'impuesto_porcentaje' => $request->impuesto_porcentaje,
                'impuesto_valor' => $request->impuesto_valor ?? 0,
                'subtotal_calculado' => $request->subtotal_calculado ?? $request->total_pagado,
                'notas' => $request->notas,
            ]);

            // Crear nuevos movimientos
            foreach ($request->items as $item) {
                InventarioMovimiento::create([
                    'empresa_id' => auth()->user()->empresa_id,
                    'producto_id' => $item['producto_id'],
                    'factura_id' => $factura->id,
                    'cantidad' => $item['cantidad'],
                    'costo_unitario' => $item['costo_unitario'],
                    'origen' => 'FACTURA_COMPRA',
                ]);

                $producto = Producto::find($item['producto_id']);
                $producto->increment('stock_actual', $item['cantidad']);
            }
        });

        return redirect()->route('admin.facturas.index')
            ->with('success', '✅ Factura actualizada y stock ajustado.');
    }
}
