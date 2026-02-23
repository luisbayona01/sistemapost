<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\Funcion;
use App\Models\FuncionAsiento;
use App\Models\Producto;
use App\Models\Sala;
use App\Models\Categoria;
use App\Models\Presentacione;
use App\Models\Marca;
use App\Models\Venta;
use App\Models\PaymentTransaction;
use App\Models\Caja;
use App\Models\Movimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Crypt;

class CashierController extends Controller
{
    protected $ventaService;
    protected $cinemaService;
    protected $accountingService;

    public function __construct(
        \App\Services\VentaService $ventaService,
        \App\Services\CinemaService $cinemaService,
        \App\Services\AccountingService $accountingService
    ) {
        $this->ventaService = $ventaService;
        $this->cinemaService = $cinemaService;
        $this->accountingService = $accountingService;
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $empresaId = auth()->user()->empresa_id;
        $hasCinema = \App\Helpers\ModuleHelper::isEnabled('cinema');
        $hasPos = \App\Helpers\ModuleHelper::isEnabled('pos');

        // Obtener o crear caja del cajero actual (CRÍTICO PARA BOTÓN DE CIERRE)
        $miCaja = Caja::where('empresa_id', $empresaId)
            ->where('user_id', auth()->id())
            ->where('estado', 'ABIERTA')
            ->first();

        if (!$miCaja) {
            // Auto-apertura si no existe, garantizando caja activa
            $miCaja = Caja::create([
                'empresa_id' => $empresaId,
                'user_id' => auth()->id(),
                'fecha_apertura' => now(),
                'monto_inicial' => 0,
                'estado' => 'ABIERTA',
                'nombre' => 'Caja ' . auth()->user()->name . ' - ' . now()->format('d/m'),
            ]);
        }

        // Productos cargados para el POS (Optimizado: select fields)
        $productos = Producto::where('empresa_id', $empresaId)
            ->where('es_venta_retail', true)
            ->where('disponible_venta', true) // Asegurar disponible
            ->where('nombre', '!=', 'Ticket Cine Genérico')
            ->where('nombre', 'not like', 'TICKET_CINEMA%')
            ->select('id', 'nombre', 'precio', 'stock_actual', 'categoria_id') // Campos necesarios
            ->with(['categoria.caracteristica', 'inventario'])
            ->orderBy('nombre')
            ->get();

        // Categorías
        $categorias = Categoria::with(['caracteristica'])
            ->whereHas('productos', function ($query) {
                $query->where('es_venta_retail', true)
                    ->where('disponible_venta', true);
            })
            ->get();

        // Filtro de fecha para funciones (Preventa)
        $fechaSeleccionada = $request->get('fecha', now()->format('Y-m-d'));
        $maxDias = config('cine.max_dias_preventa', 30);
        $fechaLimite = now()->addDays($maxDias)->format('Y-m-d');

        $errorPreventa = null;
        if ($fechaSeleccionada > $fechaLimite) {
            $errorPreventa = "La preventa solo está disponible hasta $maxDias días adelante ($fechaLimite).";
            $fechaSeleccionada = now()->format('Y-m-d');
        }

        // Obtener funciones activas (Optimizado: Eager Loading + Filtro fecha)
        $funciones = collect();
        if ($hasCinema) {
            $queryFunciones = Funcion::with(['pelicula', 'sala'])
                ->where('empresa_id', $empresaId)
                ->where('activo', true)
                ->whereDate('fecha_hora', $fechaSeleccionada);

            // Si es hoy, permitir ver funciones que empezaron hace 1h
            if ($fechaSeleccionada == now()->format('Y-m-d')) {
                $queryFunciones->where('fecha_hora', '>=', now()->subHours(1));
            }

            $funciones = $queryFunciones->orderBy('fecha_hora')->get();
        }

        // Obtener carrito actual de la sesión
        $carrito = session('carrito_pos', [
            'boletos' => [],
            'productos' => [],
        ]);

        $fechaOperativa = $this->accountingService->getActiveDay($empresaId);

        return view('pos.cashier', compact(
            'funciones',
            'productos',
            'categorias',
            'carrito',
            'hasCinema',
            'hasPos',
            'miCaja',
            'fechaSeleccionada',
            'errorPreventa',
            'fechaOperativa'
        ));
    }

    /**
     * Agregar boletos al carrito con reserva temporal
     */
    public function agregarBoletos(Request $request)
    {
        $request->validate([
            'funcion_id' => 'required|exists:funciones,id',
            'asientos' => 'required|array|min:1',
        ]);

        $carrito = session('carrito_pos', ['boletos' => [], 'productos' => []]);
        $funcion = \App\Models\Funcion::with('pelicula', 'sala')->findOrFail($request->funcion_id);

        // Obtener precio oficial desde el backend
        $precioId = $request->input('precio_id');
        $tarifaFija = 0; // ANULADO POR ORDEN URGENTE (Antes 4000)
        $precioBase = 10000; // Fallback

        if ($precioId) {
            $precioEntrada = \App\Models\PrecioEntrada::find($precioId);
            if ($precioEntrada) {
                $precioBase = (float) $precioEntrada->precio;
            }
        } elseif ($funcion->precio > 0) {
            $precioBase = (float) $funcion->precio;
        } elseif ($funcion->precio_base > 0) { // Soporte para columna renombrada
            $precioBase = (float) $funcion->precio_base;
        }

        $precioTotalUnitario = $precioBase + $tarifaFija;

        foreach (is_array($request->asientos) ? $request->asientos : [] as $asientoNombre) {

            // Buscar asiento
            $asiento = \App\Models\FuncionAsiento::where('funcion_id', $request->funcion_id)
                ->where('codigo_asiento', $asientoNombre)
                ->first();

            if (!$asiento) {
                return response()->json([
                    'success' => false,
                    'message' => "Asiento {$asientoNombre} no existe",
                ], 400);
            }

            // Verificar que NO esté vendido
            if (strtoupper($asiento->estado) === 'VENDIDO') {
                return response()->json([
                    'success' => false,
                    'message' => "Asiento {$asientoNombre} ya fue vendido",
                ], 400);
            }

            // Verificar que no esté ya en el carrito
            $yaEnCarrito = collect($carrito['boletos'])->contains('asiento', $asiento->codigo_asiento);

            if ($yaEnCarrito) {
                continue; // Ya está en el carrito, saltar
            }

            // Agregar al carrito (SIN modificar BD aún)
            $carrito['boletos'][] = [
                'funcion_id' => $funcion->id,
                'funcion_asiento_id' => $asiento->id,
                'asiento' => $asiento->codigo_asiento,
                'precio' => $precioTotalUnitario, // Precio real con tarifa
                'pelicula' => $funcion->pelicula?->titulo ?? 'Sin título',
                'sala' => $funcion->sala?->nombre ?? 'Sala',
                'horario' => $funcion->fecha_hora->format('d/m H:i'),
            ];
        }

        session(['carrito_pos' => $carrito]);

        return response()->json([
            'success' => true,
            'message' => 'Asientos agregados al carrito',
        ]);
    }

    /**
     * Agregar boleto al carrito (Mantener por compatibilidad)
     */
    public function agregarBoleto(Request $request)
    {
        if (!$request->has('asientos') && $request->has('asiento_id')) {
            $request->merge(['asientos' => [$request->asiento_id]]);
        }
        return $this->agregarBoletos($request);
    }

    /**
     * Agregar producto de confitería al carrito
     */
    public function agregarProducto(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'required|integer|min:1',
        ]);

        $producto = Producto::with('insumos')->findOrFail($request->producto_id);

        // 1. Validar Stock del Producto Final (DESACTIVADO PARA PRIORIZAR VENTA)
        /*
        $stockActual = $producto->inventario->cantidad ?? 0;
        if ($producto->insumos->isEmpty() && $stockActual < $request->cantidad) {
            // ...
        }
        */

        // 2. Validar Stock de Insumos (Receta) - SOLO INFORMATIVO (Opcional: podrías quitarlo o dejarlo como warning)
        foreach ($producto->insumos as $insumo) {
            $cantidadRequerida = $insumo->pivot->cantidad * $request->cantidad;
            // ... (podemos dejar el cálculo pero no bloquear)
        }

        $carrito = session('carrito_pos', ['boletos' => [], 'productos' => []]);

        $productoExistente = false;
        foreach ($carrito['productos'] as &$item) {
            if ($item['producto_id'] == $request->producto_id) {
                $item['cantidad'] += $request->cantidad;
                $productoExistente = true;
                break;
            }
        }

        if (!$productoExistente) {
            $carrito['productos'][] = [
                'producto_id' => $request->producto_id,
                'nombre' => $producto->nombre,
                'precio' => (float) $producto->precio,
                'cantidad' => (int) $request->cantidad,
            ];
        }

        session(['carrito_pos' => $carrito]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Producto agregado',
            ]);
        }

        return back()->with('success', 'Producto agregado')->with('openModal', 'confiteria');
    }

    /**
     * Agregar múltiples productos desde el carrito temporal del modal
     */
    public function agregarCarritoCompleto(Request $request)
    {
        $items = json_decode($request->input('items'), true);

        if (empty($items)) {
            return back()->with('error', 'Carrito vacío');
        }

        $carrito = session('carrito_pos', ['boletos' => [], 'productos' => []]);

        // Obtener productos EN LOTE (más rápido)
        $productosIds = array_column($items, 'id');
        $productos = Producto::whereIn('id', $productosIds)
            ->select('id', 'nombre', 'precio')
            ->with('inventario')
            ->get()
            ->keyBy('id');

        foreach ($items as $item) {
            $producto = $productos->get($item['id']);

            if (!$producto) {
                return back()->with('error', "Producto ID {$item['id']} no encontrado");
            }

            // VALIDACIÓN DE STOCK DESACTIVADA - Priorizar ventas
            // $stockActual = $producto->inventario->cantidad ?? 0;
            // if ($stockActual < $item['cantidad']) {
            //     return back()->with('error', "Stock insuficiente para {$producto->nombre}");
            // }

            // Buscar si ya existe en el carrito
            $existe = false;
            foreach ($carrito['productos'] as $key => $productoCarrito) {
                if ($productoCarrito['producto_id'] == $item['id']) {
                    $carrito['productos'][$key]['cantidad'] += $item['cantidad'];
                    $existe = true;
                    break;
                }
            }

            if (!$existe) {
                $carrito['productos'][] = [
                    'producto_id' => $producto->id,
                    'nombre' => $producto->nombre,
                    'precio' => (float) $producto->precio, // Usar precio de la BD
                    'cantidad' => (int) $item['cantidad'],
                ];
            }
        }

        session(['carrito_pos' => $carrito]);

        return back()->with('success', count($items) . ' productos agregados');
    }

    /**
     * Vaciar carrito
     */
    public function vaciarCarrito(Request $request)
    {
        session()->forget('carrito_pos');
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }
        return back()->with('info', 'Carrito vaciado');
    }

    /**
     * Quitar boleto del carrito
     */
    public function quitarBoleto(Request $request, $index)
    {
        $carrito = session('carrito_pos', ['boletos' => [], 'productos' => []]);
        if (isset($carrito['boletos'][$index])) {
            unset($carrito['boletos'][$index]);
            $carrito['boletos'] = array_values($carrito['boletos']);
        }
        session(['carrito_pos' => $carrito]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }
        return back()->with('info', 'Boleto eliminado del carrito');
    }

    /**
     * Quitar producto del carrito
     */
    public function quitarProducto(Request $request, $index)
    {
        $carrito = session('carrito_pos', ['boletos' => [], 'productos' => []]);
        if (isset($carrito['productos'][$index])) {
            unset($carrito['productos'][$index]);
            $carrito['productos'] = array_values($carrito['productos']);
        }
        session(['carrito_pos' => $carrito]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }
        return back()->with('info', 'Producto eliminado del carrito');
    }

    /**
     * Finalizar venta mixta (Cinema + Confitería)
     */
    public function finalizarVenta(Request $request)
    {
        $carrito = session('carrito_pos', ['boletos' => [], 'productos' => []]);

        if (empty($carrito['boletos']) && empty($carrito['productos'])) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Carrito vacío'], 422);
            }
            return back()->with('error', 'Carrito vacío');
        }

        // CALCULAR TOTALES FISCALES (DIAN 2026 - Colombia)
        $subtotalCine = 0;
        foreach ($carrito['boletos'] as $boleto) {
            $subtotalCine += floatval($boleto['precio']);
        }

        $subtotalConfiteria = 0;
        foreach ($carrito['productos'] as $producto) {
            $subtotalConfiteria += floatval($producto['precio']) * intval($producto['cantidad']);
        }

        // CÁLCULO DE INC (Extracción: El precio ya incluye el impuesto)
        $porcentajeINC = config('impuestos.inc_confiteria', 8);
        $aplicarINC = config('impuestos.aplicar_inc', true);

        // Fórmula de extracción: Subtotal / 1.08 para obtener neto, luego la diferencia es el impuesto
        $netoConfiteria = $aplicarINC ? ($subtotalConfiteria / (1 + ($porcentajeINC / 100))) : $subtotalConfiteria;
        $incTotal = $subtotalConfiteria - $netoConfiteria;

        // TOTAL FINAL (Ya incluye todo porque los precios son finales)
        $totalFinal = $this->accountingService->applyRounding($subtotalCine + $subtotalConfiteria);

        $fechaOperativa = $this->accountingService->getActiveDay(auth()->user()->empresa_id);

        try {
            // Determinar canal para el reporte fiscal
            $canal = 'mixta';
            if (empty($carrito['boletos'])) {
                $canal = 'confiteria';
            } elseif (empty($carrito['productos'])) {
                $canal = 'ventanilla';
            }

            $venta = $this->ventaService->procesarVenta([
                'boletos' => $carrito['boletos'],
                'productos' => $carrito['productos'],
                'subtotal_cine' => $subtotalCine,
                'subtotal_confiteria' => $subtotalConfiteria,
                'metodo_pago' => $request->metodo_pago ?? 'EFECTIVO',
                'canal' => $canal,
                'monto_recibido' => $request->monto_recibido ?? $totalFinal,

                // Datos del cliente para Factura Electrónica (Fase 5)
                'solicita_factura' => $request->boolean('solicita_factura'),
                'cliente_tipo_doc' => $request->cliente_tipo_doc ?? 'CC',
                'cliente_documento' => $request->cliente_documento,
                'cliente_nombre' => $request->cliente_nombre ?? 'CONSUMIDOR FINAL',
                'cliente_email' => $request->cliente_email,
                'cliente_telefono' => $request->cliente_telefono,
                'preferencia_fiscal' => $request->preferencia_fiscal ?? 'fe_todo',
            ]);

            session()->forget('carrito_pos'); // ← ÚNICO punto de vaciado: solo llega aquí si todo fue exitoso

            // === EMISIÓN FISCAL ASÍNCRONA ===
            $service = app(\App\Services\Fiscal\EmisionFiscalService::class);
            $numeroTemp = 'REC-' . now()->format('Ymd') . '-' . str_pad($venta->id, 5, '0', STR_PAD_LEFT);

            $venta->documentoFiscal()->create([
                'empresa_id' => $venta->empresa_id,
                'tipo_documento' => $service->decidirTipoDocumento($venta),
                'numero_completo' => $numeroTemp,
                'numero' => str_pad($venta->id, 5, '0', STR_PAD_LEFT),
                'prefijo' => 'REC',
                'estado' => 'pendiente_emision',
                'subtotal' => round($subtotalCine + $subtotalConfiteria - $incTotal, 2),
                'impuesto_inc' => round($incTotal, 2),
                'total' => round($totalFinal, 2),
            ]);

            // Disparamos el job ASÍNCRONO → el cajero queda libre inmediatamente
            \App\Jobs\EmitirDocumentoFiscalJob::dispatch($venta->id)
                ->onQueue('fiscal')
                ->delay(now()->addSeconds(3));

            if ($request->ajax() || $request->wantsJson()) {
                // Devolvemos éxito AL INSTANTE con recibo de contingencia
                return response()->json([
                    'success' => true,
                    'venta_id' => $venta->id,
                    'numero_recibo' => $venta->documentoFiscal->numero_completo ?? $numeroTemp,
                    'mensaje' => 'Venta registrada. Factura electrónica se emitirá en segundos.',
                    'imprimir_recibo' => true,   // frontend abre impresión térmica YA
                    'print_url' => route('export.pdf-comprobante-venta', ['id' => Crypt::encrypt($venta->id)]),
                    'total_pagado' => $venta->total_final
                ], 201);
            }

            return redirect()->route('pos.index')
                ->with('venta_exitosa', [
                    'id' => $venta->id,
                    'total' => $venta->total_final,
                    'cambio' => $venta->cambio,
                    'print_url' => route('export.pdf-comprobante-venta', ['id' => Crypt::encrypt($venta->id)]),
                ]);

        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            return back()->with('error', 'Error al procesar venta: ' . $e->getMessage());
        }
    }

    public function getCarritoPartial()
    {
        $carrito = session('carrito_pos', ['boletos' => [], 'productos' => []]);
        return view('pos.partials.cart-sidebar', compact('carrito'));
    }

    public function lockSeatTemporal(Request $request)
    {
        $request->validate([
            'auditorio_id' => 'required|exists:salas,id',
            'fila' => 'required|string|max:5',
            'numero' => 'required|integer',
        ]);

        $empresaId = auth()->user()->empresa_id;
        $key = "seat_lock_{$empresaId}_{$request->auditorio_id}_{$request->fila}_{$request->numero}";

        // Si ya está locked por otra caja
        if (\Illuminate\Support\Facades\Cache::has($key) && \Illuminate\Support\Facades\Cache::get($key) !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Asiento ya en proceso por otro cajero'
            ], 409);
        }

        // Lock temporal 8 minutos (suficiente para todo el checkout)
        \Illuminate\Support\Facades\Cache::put($key, auth()->id(), now()->addMinutes(8));

        return response()->json(['success' => true, 'locked_until' => now()->addMinutes(8)]);
    }
}
