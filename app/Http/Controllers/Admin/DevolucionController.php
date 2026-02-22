<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Devolucion, DevolucionItem, Venta, FuncionAsiento, Producto};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DevolucionController extends Controller
{
    /**
     * Buscar venta para devolver
     */
    public function index()
    {
        $devoluciones = Devolucion::with(['venta', 'user'])
            ->where('empresa_id', auth()->user()->empresa_id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.devoluciones.index', compact('devoluciones'));
    }

    /**
     * Buscar venta por ID
     */
    public function buscarVenta(Request $request)
    {
        $request->validate([
            'venta_id' => 'required|integer',
        ]);

        $venta = Venta::with([
            'productos',
            'funcionAsientos.funcion.pelicula',
            'funcionAsientos.funcion.sala',
        ])
            ->where('empresa_id', auth()->user()->empresa_id)
            ->where('estado_pago', 'PAGADA')
            ->find($request->venta_id);

        if (!$venta) {
            return back()->with('error', 'Venta #' . $request->venta_id . ' no encontrada');
        }

        // Verificar si ya fue devuelta
        $yaDevuelta = Devolucion::where('venta_id', $venta->id)->exists();
        if ($yaDevuelta) {
            return back()->with('error', 'Esta venta ya fue devuelta anteriormente');
        }

        // Verificar si es del mismo día
        $esDelMismoDia = $venta->created_at->isToday();
        $esRoot = auth()->user()->hasRole('Root');

        // Si no es del mismo día y no es Root, bloquear
        if (!$esDelMismoDia && !$esRoot) {
            return back()->with(
                'error',
                'Solo se pueden devolver ventas del día actual. ' .
                'Para devoluciones de días anteriores contacta al administrador del sistema.'
            );
        }

        return view('admin.devoluciones.procesar', compact(
            'venta',
            'esDelMismoDia',
            'esRoot'
        ));
    }

    /**
     * Procesar la devolución
     */
    public function procesar(Request $request)
    {
        $request->validate([
            'venta_id' => 'required|exists:ventas,id',
            'motivo' => 'required|string|min:5|max:500',
            'items_boletos' => 'nullable|array',
            'items_productos' => 'nullable|array',
        ]);

        // Validar que haya algo que devolver
        $hayBoletos = !empty($request->items_boletos);
        $hayProductos = !empty($request->items_productos);

        if (!$hayBoletos && !$hayProductos) {
            return back()->with('error', 'Debes seleccionar al menos un item para devolver');
        }

        $venta = Venta::where('empresa_id', auth()->user()->empresa_id)
            ->findOrFail($request->venta_id);

        // Verificar permisos para devolución excepcional
        $esDelMismoDia = $venta->created_at->isToday();
        $esRoot = auth()->user()->hasRole('Root');

        if (!$esDelMismoDia && !$esRoot) {
            return back()->with('error', 'No tienes permisos para hacer esta devolución');
        }

        DB::transaction(function () use ($request, $venta, $esDelMismoDia, $esRoot) {
            $montoTotal = 0;
            $tiposItems = [];

            // Crear registro de devolución
            $devolucion = Devolucion::create([
                'empresa_id' => auth()->user()->empresa_id,
                'venta_id' => $venta->id,
                'user_id' => auth()->id(),
                'tipo' => 'MIXTA', // se actualiza abajo
                'monto_devuelto' => 0, // se actualiza al final
                'motivo' => $request->motivo,
                'es_excepcional' => !$esDelMismoDia,
                'autorizacion_nota' => $request->autorizacion_nota,
            ]);

            // Procesar devolución de BOLETOS
            if (!empty($request->items_boletos)) {
                $tiposItems[] = 'BOLETERIA';
                foreach ($request->items_boletos as $asientoId) {
                    $asiento = FuncionAsiento::findOrFail($asientoId);

                    // Precio del boleto
                    $precioAsiento = $asiento->funcion->precio_base ?? 0;

                    DevolucionItem::create([
                        'devolucion_id' => $devolucion->id,
                        'tipo_item' => 'BOLETO',
                        'funcion_asiento_id' => $asiento->id,
                        'cantidad' => 1,
                        'monto' => $precioAsiento,
                    ]);

                    // Liberar asiento
                    $asiento->update(['estado' => 'DISPONIBLE']);

                    $montoTotal += $precioAsiento;
                }
            }

            // Procesar devolución de PRODUCTOS
            if (!empty($request->items_productos)) {
                $tiposItems[] = 'CONFITERIA';
                foreach ($request->items_productos as $productoId => $cantidad) {
                    if ($cantidad <= 0)
                        continue;

                    $producto = Producto::findOrFail($productoId);
                    $subtotal = $producto->precio * $cantidad;

                    DevolucionItem::create([
                        'devolucion_id' => $devolucion->id,
                        'tipo_item' => 'PRODUCTO',
                        'producto_id' => $producto->id,
                        'cantidad' => $cantidad,
                        'monto' => $subtotal,
                    ]);

                    // Devolver al inventario
                    $producto->increment('stock_actual', $cantidad);

                    $montoTotal += $subtotal;
                }
            }

            // Determinar tipo
            $tipo = 'MIXTA';
            if (count($tiposItems) === 1) {
                $tipo = $tiposItems[0];
            }

            // Actualizar devolución con montos finales
            $devolucion->update([
                'tipo' => $tipo,
                'monto_devuelto' => $montoTotal,
            ]);

            // Registrar movimiento de egreso en caja
            $caja = \App\Models\Caja::where('empresa_id', auth()->user()->empresa_id)
                ->where('estado', 'ABIERTA')
                ->first();

            if ($caja) {
                \App\Models\Movimiento::create([
                    'caja_id' => $caja->id,
                    'empresa_id' => auth()->user()->empresa_id,
                    'tipo' => \App\Enums\TipoMovimientoEnum::DEVOLUCION,
                    'descripcion' => "Devolución Venta #{$venta->id}",
                    'user_id' => auth()->id(),
                    'monto' => $montoTotal,
                ]);
            }
        });

        return redirect()->route('admin.devoluciones.index')
            ->with('success', '✅ Devolución procesada correctamente');
    }
}
