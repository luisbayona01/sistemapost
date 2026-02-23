<?php

namespace App\Actions\Cinema;

use App\Models\Venta;
use App\Models\Funcion;
use App\Models\PrecioEntrada;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Presentacione;
use App\Models\Marca;
use App\Services\VentaService;
use App\Services\CinemaService;
use Illuminate\Support\Facades\DB;

class ProcesarVentaCinemaAction
{
    protected VentaService $ventaService;
    protected CinemaService $cinemaService;

    public function __construct(VentaService $ventaService, CinemaService $cinemaService)
    {
        $this->ventaService = $ventaService;
        $this->cinemaService = $cinemaService;
    }

    /**
     * Coordina la creación de la venta y la confirmación del asiento.
     * Mantiene la lógica de Cinema separada de la Facturación.
     */
    public function execute(array $data): Venta
    {
        return DB::transaction(function () use ($data) {
            $funcion = Funcion::with('pelicula')->findOrFail($data['funcion_id']);
            $precioEntrada = PrecioEntrada::findOrFail($data['precio_entrada_id']);
            $codigosAsientos = $data['asientos'];
            $cantidad = count($codigosAsientos);

            // 1. Preparar datos para el Servicio de Venta
            $empresa = auth()->user()->empresa;
            $porcentajeImpuesto = $empresa->porcentaje_impuesto ?? 19;
            $factor = 1 + ($porcentajeImpuesto / 100);

            $precioBaseUnitario = $precioEntrada->precio; // 30000
            $tarifaFijaUnitaria = 4000; // 4000

            $subtotalTotal = ($precioBaseUnitario / $factor) * $cantidad;
            $impuestoTotal = ($precioBaseUnitario - ($precioBaseUnitario / $factor)) * $cantidad;
            $totalVenta = ($precioBaseUnitario + $tarifaFijaUnitaria) * $cantidad;
            $montoTarifaTotal = $tarifaFijaUnitaria * $cantidad;

            // Buscar o crear un producto servicio para la venta de tickets (asegurar IDs válidos)
            $catId = Categoria::first()->id ?? 1;
            $presId = Presentacione::first()->id ?? 1;
            $marcaId = Marca::first()->id ?? 1;

            $productoTicket = Producto::withoutGlobalScope('empresa')->where('codigo', 'TICKET_CINEMA_' . $empresa->id)->first();

            if (!$productoTicket) {
                $productoTicket = new Producto([
                    'codigo' => 'TICKET_CINEMA_' . $empresa->id,
                    'nombre' => 'Entrada de Cine General',
                    'empresa_id' => $empresa->id,
                    'precio' => 0,
                    'categoria_id' => $catId,
                    'presentacione_id' => $presId,
                    'marca_id' => $marcaId,
                    'stock_minimo' => 0,
                ]);
                $productoTicket->es_venta_retail = false;
                $productoTicket->save();
            }

            // Asegurar que tenga stock (es un producto virtual/servicio)
            \App\Models\Inventario::updateOrCreate(
                ['empresa_id' => $empresa->id, 'producto_id' => $productoTicket->id],
                ['cantidad' => 999999, 'cantidad_minima' => 0, 'ubicacione_id' => 1]
            );

            $clienteGenerico = $this->ventaService->getGenericClient($empresa->id);

            $ventaParams = [
                'cliente_id' => $data['cliente_id'] ?? $clienteGenerico->id,
                'comprobante_id' => $data['comprobante_id'] ?? 1,
                'metodo_pago' => $data['metodo_pago'] ?? 'EFECTIVO',
                'subtotal' => $subtotalTotal,
                'impuesto' => $impuestoTotal,
                'total' => $totalVenta,
                'monto_tarifa' => $montoTarifaTotal,
                'tarifa_servicio' => 0,
                'productos' => [
                    [
                        'producto_id' => $productoTicket->id, // Usar el ID del producto servicio
                        'cantidad' => $cantidad,
                        'precio_venta' => $precioBaseUnitario
                    ]
                ],
                'pagos' => $data['pagos'] ?? null,
                'canal' => 'ventanilla'
            ];

            // 2. Ejecutar Venta
            $venta = $this->ventaService->procesarVenta($ventaParams);

            // 3. Confirmar Asientos
            foreach ($codigosAsientos as $codigo) {
                $confirmado = $this->cinemaService->confirmarVenta(
                    $data['funcion_id'],
                    $codigo,
                    $data['session_id'],
                    $venta->id
                );

                if (!$confirmado) {
                    throw new \Exception("No se pudo confirmar el asiento {$codigo}. Es posible que el bloqueo haya expirado.");
                }
            }

            return $venta;
        });
    }
}
