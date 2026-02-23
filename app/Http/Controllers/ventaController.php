<?php

namespace App\Http\Controllers;

use App\Enums\MetodoPagoEnum;
use App\Events\CreateVentaDetalleEvent;
use App\Events\CreateVentaEvent;
use App\Http\Requests\StoreVentaRequest;
use App\Models\Caja;
use App\Models\Cliente;
use App\Models\Movimiento;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\StripeConfig;
use App\Services\ActivityLogService;
use App\Services\ComprobanteService;
use App\Services\EmpresaService;
use App\Services\StripePaymentService;
use App\Services\CinemaService;
use App\Services\VentaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class VentaController extends Controller
{
    protected EmpresaService $empresaService;
    protected VentaService $ventaService;
    protected CinemaService $cinemaService;

    function __construct(EmpresaService $empresaService, VentaService $ventaService, CinemaService $cinemaService)
    {
        $this->middleware('permission:ver-venta|crear-venta|mostrar-venta|eliminar-venta', ['only' => ['index']]);
        $this->middleware('permission:crear-venta', ['only' => ['create', 'store']]);
        $this->middleware('permission:mostrar-venta', ['only' => ['show']]);
        $this->middleware('role:administrador|Gerente|Root', ['only' => ['destroy']]);
        $this->middleware('check-caja-aperturada-user', ['only' => ['create', 'store']]);
        $this->middleware('check-show-venta-user', ['only' => ['show']]);
        $this->empresaService = $empresaService;
        $this->ventaService = $ventaService;
        $this->cinemaService = $cinemaService;
    }
    /**
     * Display a listing of the resource.
     * Global Scope filtra automáticamente por empresa_id
     */
    /**
     * Display a listing of the resource.
     * Global Scope filtra automáticamente por empresa_id
     */
    public function index(): View
    {
        // OPTIMIZATION: Pagination added to prevent memory exhaustion
        $ventas = Venta::with(['comprobante', 'cliente.persona', 'user'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(15);

        return view('venta.index', compact('ventas'));
    }

    /**
     * Valida que exista caja abierta (middleware check-caja-aperturada-user)
     */
    public function create(ComprobanteService $comprobanteService): View
    {
        // Obtener empresa del usuario autenticado
        $empresa = auth()->user()->empresa;

        // Obtener caja abierta del usuario en esta empresa
        $cajaAbierta = Caja::where('user_id', Auth::id())
            ->where('empresa_id', $empresa->id)
            ->abierta()
            ->first();

        // Productos de la empresa con stock disponible
        // OPTIMIZATION: Limited to 500 to prevent crashing. Added proper Eager Loading.
        // TODO: Implement AJAX search for production scaling.
        $productos = Producto::join('inventario as i', 'i.producto_id', '=', 'productos.id')
            ->join('presentaciones as p', 'p.id', '=', 'productos.presentacione_id')
            ->where('productos.empresa_id', $empresa->id)
            ->where('productos.estado', 1)
            ->where('i.cantidad', '>', 0)
            ->select(
                'p.sigla',
                'productos.nombre',
                'productos.codigo',
                'productos.id',
                'i.cantidad',
                'productos.precio'
            )
            ->limit(500)
            ->get();

        // Clientes de la empresa
        // OPTIMIZATION: Limit added. Should also be AJAX.
        $clientes = Cliente::whereHas('persona', function ($query) {
            $query->where('estado', 1);
        })
            ->where('empresa_id', $empresa->id)
            ->limit(500)
            ->get();

        $comprobantes = $comprobanteService->obtenerComprobantes();

        return view('venta.create', compact('productos', 'clientes', 'comprobantes'));
    }

    /**
     * Crea venta con empresa_id, calcula tarifa y registra movimiento de caja
     */
    public function store(StoreVentaRequest $request): RedirectResponse
    {
        try {
            // Preparar datos para el servicio
            $arrayProducto_id = $request->get('arrayidproducto');
            $arrayCantidad = $request->get('arraycantidad');
            $arrayPrecioVenta = $request->get('arrayprecioventa');

            $productos = [];
            if ($arrayProducto_id) {
                foreach ($arrayProducto_id as $i => $id) {
                    $productos[] = [
                        'producto_id' => $id,
                        'cantidad' => $arrayCantidad[$i],
                        'precio_venta' => $arrayPrecioVenta[$i],
                    ];
                }
            }

            $venta = $this->ventaService->procesarVenta(array_merge($request->validated(), [
                'productos' => $productos,
            ]));

            return redirect()->route('movimientos.index', ['caja_id' => $venta->caja_id])
                ->with('success', 'Venta registrada correctamente');

        } catch (Throwable $e) {
            Log::error('Error al crear la venta', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al registrar la venta: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Venta $venta): View
    {
        $empresa = $this->empresaService->obtenerEmpresa();
        return view('venta.show', compact('venta', 'empresa'));
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
     * REQUIRES AUTH KEY (Gerente/Root)
     */
    public function destroy(string $id, Request $request)
    {
        // 1. Validar que se envió un PIN de autorización
        $request->validate([
            'auth_pin' => 'required|string'
        ]);

        // 2. Verificar credenciales del supervisor (Gerente o Root)
        $supervisor = \App\Models\User::role(['Gerente', 'Root'])
            ->where('pin_code', $request->auth_pin) // Asumiendo campo pin_code en users
            ->first();

        if (!$supervisor) {
            if ($request->ajax()) {
                return response()->json(['message' => 'PIN de autorización inválido o sin privilegios.'], 403);
            }
            return back()->with('error', 'Autorización fallida: PIN Incorrecto.');
        }

        // 3. Proceder con la anulación
        try {
            DB::beginTransaction();

            $venta = Venta::with('productos.inventario')->findOrFail($id);

            // LOGIC FIX: Revertir Stock
            foreach ($venta->productos as $producto) {
                if ($producto->inventario) {
                    $producto->inventario->increment('cantidad', $producto->pivot->cantidad);
                }
            }

            // Liberar asientos de cinema asociados
            $this->cinemaService->liberarAsientosPorVenta($venta->id);

            // LOGIC FIX: Update payment status so it doesn't count in cash reports
            $venta->update([
                'estado' => 0, // Inactivo
                'estado_pago' => 'ANULADA',
                'notas_anulacion' => $request->get('reason', 'Sin motivo')
            ]);

            // 4. Log de Auditoría Inmutable
            ActivityLogService::log(
                'Anulación de Venta',
                'Ventas',
                ['venta_id' => $id, 'authorized_by' => $supervisor->name, 'reason' => $request->get('reason', 'Sin motivo')]
            );

            DB::commit();

            if ($request->ajax()) {
                return response()->json(['message' => 'Venta anulada correctamente por: ' . $supervisor->name]);
            }
            return redirect()->route('ventas.index')->with('success', 'Venta anulada bajo supervisión de: ' . $supervisor->name);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error crítico al anular: ' . $e->getMessage());
        }
    }

    /**
     * Iniciar pago con Stripe para una venta
     * POST /admin/ventas/{venta}/pago/iniciar
     */
    public function iniciarPago(Venta $venta): JsonResponse
    {
        try {
            // Validar que la venta pertenece a la empresa del usuario
            if ($venta->empresa_id !== Auth::user()->empresa_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para procesar este pago'
                ], 403);
            }

            // Validar que la venta no está ya pagada
            if ($venta->estado_pago === 'PAGADA') {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta venta ya ha sido pagada'
                ], 400);
            }

            // Crear servicio de pago Stripe
            $stripeService = new StripePaymentService();

            // Crear PaymentIntent
            $transaction = $stripeService->createPaymentIntent(
                $venta,
                [
                    'empresa_id' => $venta->empresa_id,
                    'venta_id' => $venta->id,
                    'user_id' => Auth::id(),
                ]
            );

            // Registrar en activity log
            \App\Services\ActivityLogService::log(
                'Pago Stripe iniciado',
                'Pagos',
                ['venta_id' => $venta->id, 'payment_intent_id' => $transaction->stripe_payment_intent_id]
            );

            return response()->json([
                'success' => true,
                'client_secret' => $transaction->stripe_payment_intent_id,
                'amount' => $venta->total * 100, // En centavos para Stripe
                'currency' => 'usd',
                'venta_id' => $venta->id
            ]);

        } catch (Throwable $e) {
            Log::error('Error iniciando pago Stripe', [
                'venta_id' => $venta->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al iniciar el pago: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener configuración de Stripe para el frontend
     * GET /admin/ventas/{venta}/pago/config
     */
    public function configPago(Venta $venta): JsonResponse
    {
        try {
            // Validar que la venta pertenece a la empresa del usuario
            if ($venta->empresa_id !== Auth::user()->empresa_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para acceder a este recurso'
                ], 403);
            }

            // Obtener configuración Stripe de la empresa
            $stripeConfig = StripeConfig::where('empresa_id', $venta->empresa_id)
                ->where('enabled', true)
                ->first();

            if (!$stripeConfig) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stripe no está configurado para tu empresa'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'publicKey' => $stripeConfig->public_key,
                'ventaId' => $venta->id,
                'amount' => (int) ($venta->total * 100),
                'currency' => 'usd',
                'estatoPago' => $venta->estado_pago,
            ]);

        } catch (Throwable $e) {
            Log::error('Error obteniendo config de Stripe', [
                'venta_id' => $venta->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener configuración de pago'
            ], 500);
        }
    }

    /**
     * Obtener estado del pago de una venta
     * GET /admin/ventas/{venta}/pago/estado
     */
    public function estadoPago(Venta $venta): JsonResponse
    {
        try {
            // Validar que la venta pertenece a la empresa del usuario
            if ($venta->empresa_id !== Auth::user()->empresa_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para consultar este pago'
                ], 403);
            }

            // Obtener última transacción de pago
            $transaction = $venta->paymentTransactions()
                ->latest()
                ->first();

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay transacciones de pago para esta venta'
                ], 404);
            }

            // Si el pago está pendiente, confirmar estado en Stripe
            if ($transaction->isPending()) {
                $stripeService = new StripePaymentService();
                $transaction->refresh();
            }

            return response()->json([
                'success' => true,
                'status' => $transaction->status,
                'amount' => $transaction->amount_paid,
                'currency' => $transaction->currency,
                'created_at' => $transaction->created_at,
                'paid_at' => $transaction->updated_at
            ]);

        } catch (Throwable $e) {
            Log::error('Error consultando estado de pago', [
                'venta_id' => $venta->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al consultar el pago'
            ], 500);
        }
    }

    /**
     * Buscar venta por número de comprobante para devoluciones (POS)
     */
    public function buscarPorComprobante(Request $request): JsonResponse
    {
        $numero = $request->get('numero');

        $venta = Venta::with(['productos', 'asientosCinema', 'cliente.persona'])
            ->where('numero_comprobante', 'LIKE', "%{$numero}%")
            ->where('empresa_id', auth()->user()->empresa_id)
            ->latest()
            ->first();

        if (!$venta) {
            return response()->json(['success' => false, 'message' => 'Factura no encontrada'], 404);
        }

        return response()->json([
            'success' => true,
            'venta' => $venta,
            'print_url' => route('export.pdf-comprobante-venta', ['id' => \Illuminate\Support\Facades\Crypt::encrypt($venta->id)]),
            'detalle' => $venta->productos->map(fn($p) => "{$p->pivot->cantidad}x {$p->nombre}")->implode(', ')
        ]);
    }
}

