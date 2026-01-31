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
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ventaController extends Controller
{
    protected EmpresaService $empresaService;

    function __construct(EmpresaService $empresaService)
    {
        $this->middleware('permission:ver-venta|crear-venta|mostrar-venta|eliminar-venta', ['only' => ['index']]);
        $this->middleware('permission:crear-venta', ['only' => ['create', 'store']]);
        $this->middleware('permission:mostrar-venta', ['only' => ['show']]);
        //$this->middleware('permission:eliminar-venta', ['only' => ['destroy']]);
        $this->middleware('check-caja-aperturada-user', ['only' => ['create', 'store']]);
        $this->middleware('check-show-venta-user', ['only' => ['show']]);
        $this->empresaService = $empresaService;
    }
    /**
     * Display a listing of the resource.
     * Global Scope filtra automáticamente por empresa_id
     */
    public function index(): View
    {
        $ventas = Venta::with(['comprobante', 'cliente.persona', 'user'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

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
        $productos = Producto::join('inventario as i', function ($join) {
            $join->on('i.producto_id', '=', 'productos.id');
        })
            ->join('presentaciones as p', function ($join) {
                $join->on('p.id', '=', 'productos.presentacione_id');
            })
            ->where('productos.empresa_id', $empresa->id)
            ->select(
                'p.sigla',
                'productos.nombre',
                'productos.codigo',
                'productos.id',
                'i.cantidad',
                'productos.precio'
            )
            ->where('productos.estado', 1)
            ->where('i.cantidad', '>', 0)
            ->get();

        // Clientes de la empresa
        $clientes = Cliente::whereHas('persona', function ($query) {
            $query->where('estado', 1);
        })
            ->where('empresa_id', $empresa->id)
            ->get();

       Crea venta con empresa_id, calcula tarifa y registra movimiento de caja
     */
    public function store(StoreVentaRequest $request): RedirectResponse
    {
        DB::beginTransaction();
        try {
            // Obtener empresa y caja
            $empresa = auth()->user()->empresa;
            $cajaAbierta = Caja::where('user_id', Auth::id())
                ->where('empresa_id', $empresa->id)
                ->abierta()
                ->first();

            if (!$cajaAbierta) {
                return redirect()->route('cajas.create')
                    ->with('error', 'Debes abrir una caja para registrar ventas');
            }

            // Crear venta con empresa_id y user_id
            $ventaData = array_merge($request->validated(), [
                'empresa_id' => $empresa->id,
                'user_id' => Auth::id(),
                'caja_id' => $cajaAbierta->id,
            ]);

            $venta = Venta::create($ventaData);

            // Llenar tabla venta_producto
            $arrayProducto_id = $request->get('arrayidproducto');
            $arrayCantidad = $request->get('arraycantidad');
            $arrayPrecioVenta = $request->get('arrayprecioventa');

            $siseArray = count($arrayProducto_id);

            for ($i = 0; $i < $siseArray; $i++) {
                $venta->productos()->syncWithoutDetaching([
                    $arrayProducto_id[$i] => [
                        'cantidad' => $arrayCantidad[$i],
                        'precio_venta' => $arrayPrecioVenta[$i],
                        'tarifa_unitaria' => $venta->calcularTarifaUnitaria(
                            $arrayProducto_id[$i],
                            $arrayPrecioVenta[$i]
                        ),
                    ]
                ]);

                // Despachar evento por cada detalle
                CreateVentaDetalleEvent::dispatch(
                    $venta,
                    $arrayProducto_id[$i],
                    $arrayCantidad[$i],
                    $arrayPrecioVenta[$i]
                );
            }

            // Registrar movimiento de caja (ingreso por venta)
            Movimiento::create([
                'empresa_id' => $empresa->id,
                'caja_id' => $cajaAbierta->id,
                'venta_id' => $venta->id,
                'user_id' => Auth::id(),
                'tipo' => 'INGRESO',
                'monto' => $venta->total,
                'metodo_pago' => $venta->metodo_pago,
                'descripcion' => "Venta #{$venta->id} - {$venta->comprobante->nombre}",
            ]);

            // Despachar evento de venta completa
            CreateVentaEvent::dispatch($venta);

            DB::commit();
            ActivityLogService::log('Creación de una venta', 'Ventas', $ventaData);
            return redirect()->route('movimientos.index', ['caja_id' => $cajaAbierta->id])
                ->with('success', 'Venta registrada correctamente');
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error al crear la venta', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->route('ventas.index')->with('error', 'Error al registrar la venta: ' . $e->getMessage()
                );

                $cont++;
            }

            //Despachar evento
            CreateVentaEvent::dispatch($venta);

            DB::commit();
            ActivityLogService::log('Creación de una venta', 'Ventas', $request->validated());
            return redirect()->route('movimientos.index', ['caja_id' => $venta->caja_id])
                ->with('success', 'Venta registrada');
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error al crear la venta', ['error' => $e->getMessage()]);
            return redirect()->route('ventas.index')->with('error', 'Ups, algo falló');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Venta $venta): View
    {
        $empresa =  $this->empresaService->obtenerEmpresa();
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
     */
    public function destroy(string $id)
    {
        /* Venta::where('id', $id)
            ->update([
                'estado' => 0
            ]);

        return redirect()->route('ventas.index')->with('success', 'Venta eliminada');*/
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
            activity()
                ->withProperties(['venta_id' => $venta->id, 'payment_intent_id' => $transaction->stripe_payment_intent_id])
                ->log('Pago Stripe iniciado');

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
                'amount' => (int)($venta->total * 100),
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
}

