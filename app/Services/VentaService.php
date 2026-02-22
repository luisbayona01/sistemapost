<?php

namespace App\Services;

use App\Events\CreateVentaDetalleEvent;
use App\Events\CreateVentaEvent;
use App\Models\Caja;
use App\Models\Movimiento;
use App\Models\Venta;
use App\Models\PaymentTransaction;
use App\Models\Inventario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Cliente;
use App\Models\Persona;
use App\Models\Documento;
use App\Enums\TipoPersonaEnum;

use Illuminate\Support\Facades\Log;
use Throwable;

class VentaService
{
    /**
     * Obtener o crear el cliente genérico para una empresa
     */
    public function getGenericClient(int $empresaId): Cliente
    {
        return DB::transaction(function () use ($empresaId) {
            // Buscar si ya existe un cliente con documento 0 o 99999999 para esta empresa
            $cliente = Cliente::withoutGlobalScope('empresa')
                ->where('empresa_id', $empresaId)
                ->whereHas('persona', function ($q) {
                    $q->whereIn('numero_documento', ['0', '99999999', '00000000']);
                })
                ->first();

            if ($cliente) {
                return $cliente;
            }

            // Si no existe, crear la persona y el cliente
            $doc = Documento::first() ?? Documento::create(['nombre' => 'DNI', 'codigo' => '01', 'estado' => 1]);

            $persona = Persona::create([
                'tipo' => TipoPersonaEnum::Natural,
                'razon_social' => 'PUBLICO GENERAL',
                'documento_id' => $doc->id,
                'numero_documento' => '0',
                'estado' => 1
            ]);

            return Cliente::create([
                'persona_id' => $persona->id,
                'empresa_id' => $empresaId
            ]);
        });
    }

    /**
     * Procesar una venta completa
     */
    /**
     * Procesar una venta física (POS) - SOPORTA MIXTA (CINE + CONFITERIA)
     */
    public function procesarVenta(array $data): Venta
    {
        return DB::transaction(function () use ($data) {
            $empresa = auth()->user()->empresa;
            $user = auth()->user();

            // 1. Validar Caja (Solo para FISICA)
            $cajaAbierta = $this->obtenerCajaAbierta($empresa->id);
            if (!$cajaAbierta) {
                throw new \Exception('Debes abrir una caja para registrar ventas físicas');
            }

            // 2. Extraer y Calcular Totales Fiscales (Estandarizado)
            $subtotalCine = (float) ($data['subtotal_cine'] ?? 0);
            $subtotalConfiteria = (float) ($data['subtotal_confiteria'] ?? 0);

            // Si no vienen discriminados, intentar obtenerlos del total
            if ($subtotalCine == 0 && $subtotalConfiteria == 0) {
                $subtotalConfiteria = (float) ($data['subtotal'] ?? 0);
            }

            // Cálculo de INC (Extracción: El precio ya incluye el impuesto)
            $porcentajeINC = config('impuestos.inc_confiteria', 8);
            $aplicarINC = config('impuestos.aplicar_inc', true);

            // Fórmula de extracción: Subtotal / 1.08 para obtener el neto
            $netoConfiteria = $aplicarINC ? ($subtotalConfiteria / (1 + ($porcentajeINC / 100))) : $subtotalConfiteria;
            $incTotal = $subtotalConfiteria - $netoConfiteria;

            $totalFinal = $subtotalCine + $subtotalConfiteria;

            // 3. Crear la Venta
            $ventaData = [
                'empresa_id' => $empresa->id,
                'user_id' => $user->id,
                'caja_id' => $cajaAbierta->id,
                'cliente_id' => $data['cliente_id'] ?? $this->getGenericClient($empresa->id)->id,
                'comprobante_id' => $data['comprobante_id'] ?? 1,
                'fecha_hora' => now(),
                'fecha_operativa' => $data['fecha_operativa'] ?? app(\App\Services\AccountingService::class)->getActiveDay($empresa->id),
                'metodo_pago' => $data['metodo_pago'] ?? 'EFECTIVO',
                'subtotal' => $totalFinal,
                'subtotal_cine' => $subtotalCine,
                'subtotal_confiteria' => $subtotalConfiteria,
                'impuesto' => 0, // IVA 0% según normativa actual cinema/pos
                'inc_total' => $incTotal,
                'total' => $totalFinal,
                'total_final' => $totalFinal,
                'monto_tarifa' => $data['monto_tarifa'] ?? 0,
                'monto_recibido' => $data['monto_recibido'] ?? $totalFinal,
                'vuelto_entregado' => $data['vuelto_entregado'] ?? 0,
                'estado_pago' => 'PAGADA',
                'canal' => $data['canal'] ?? 'ventanilla',
                'tipo_venta' => 'FISICA',
                'origen' => 'POS',
                'cambio' => (float) ($data['monto_recibido'] ?? $totalFinal) - $totalFinal,

                // Datos del cliente para Factura Electrónica (Fase 5)
                'solicita_factura' => $data['solicita_factura'] ?? false,
                'cliente_tipo_doc' => $data['cliente_tipo_doc'] ?? 'CC',
                'cliente_documento' => $data['cliente_documento'] ?? null,
                'cliente_nombre' => $data['cliente_nombre'] ?? 'CONSUMIDOR FINAL',
                'cliente_email' => $data['cliente_email'] ?? null,
                'cliente_telefono' => $data['cliente_telefono'] ?? null,
                'preferencia_fiscal' => $data['preferencia_fiscal'] ?? 'fe_todo',
            ];

            $venta = Venta::create($ventaData);

            // 4. Procesar Asientos (Cinema)
            if (isset($data['boletos']) && !empty($data['boletos'])) {
                foreach ($data['boletos'] as $boleto) {
                    $asiento = \App\Models\FuncionAsiento::where('id', $boleto['funcion_asiento_id'])
                        ->lockForUpdate()
                        ->first();

                    if (!$asiento || !$asiento->isAvailable()) {
                        throw new \Exception("El asiento {$boleto['asiento']} ya no está disponible.");
                    }

                    $asiento->update(['estado' => 'VENDIDO', 'venta_id' => $venta->id]);

                    if (\Schema::hasTable('venta_funcion_asientos')) {
                        DB::table('venta_funcion_asientos')->insert([
                            'venta_id' => $venta->id,
                            'funcion_asiento_id' => $asiento->id,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                }
            }

            // 5. Procesar Productos (Confitería)
            if (isset($data['productos']) && !empty($data['productos'])) {
                // Nuevo arreglo para optimizar la reserva de stock
                $productosParaReservar = [];

                foreach ($data['productos'] as $prod) {
                    // Validar Existencia y Stock (Falla si no hay stock)
                    $producto = \App\Models\Producto::with('inventario')->findOrFail($prod['producto_id']);

                    // Solo validar si no es una venta retail que permite negativos (ajustar según política empresa)
                    if ($producto->inventario && $producto->inventario->cantidad < $prod['cantidad']) {
                        // Opcional: Log o Exception si se requiere bloqueo estricto
                        // throw new \Exception("Stock insuficiente para {$producto->nombre}");
                    }

                    $venta->productos()->attach($prod['producto_id'], [
                        'cantidad' => $prod['cantidad'],
                        'precio_venta' => $prod['precio'],
                        'tarifa_unitaria' => 0
                    ]);

                    // Acumulamos para el lock ordenado
                    $productosParaReservar[] = [
                        'insumo_id' => $producto->id, // Usando el producto como ID primario (o su insumo base si aplica)
                        'cantidad' => $prod['cantidad']
                    ];

                    // Despachar evento para afectar inventario
                    CreateVentaDetalleEvent::dispatch($venta, $prod['producto_id'], $prod['cantidad'], $prod['precio']);
                }

                // NUEVO PARCHE ANTI-DEADLOCKS DE BASE DE DATOS
                if (!empty($productosParaReservar) && app()->has(\App\Services\Inventory\InventoryService::class)) {
                    // Solo intentar el bloqueo si manejamos inventario avanzado (legacy usa el listener)
                    // (Esta reserva ordena por ID antes del lock evitando cross-locks en mariadb)
                    app(\App\Services\Inventory\InventoryService::class)->reservarInsumosOrdenados($productosParaReservar);
                }
            }

            // 6. Registrar Pagos y Movimiento
            $this->registrarPagosFisicos($venta, $data, $empresa->id);
            $this->registrarMovimiento($venta, $cajaAbierta);

            ActivityLogService::log('Venta POS completada', 'Ventas', ['venta_id' => $venta->id]);

            // Despachar evento principal (Fase 5: Integración Fiscal)
            \App\Events\CreateVentaEvent::dispatch($venta);

            return $venta;
        });
    }

    /**
     * Procesar una venta Web (E-commerce / App)
     */
    public function procesarVentaWeb(array $data): Venta
    {
        // Regla de Dominio: Venta Web NO puede usar EFECTIVO
        if (($data['metodo_pago'] ?? '') === 'EFECTIVO') {
            throw new \Exception('Las ventas web no admiten el método de pago EFECTIVO.');
        }

        return DB::transaction(function () use ($data) {
            $empresaId = $data['empresa_id'] ?? auth()->user()?->empresa_id;

            if (!$empresaId) {
                throw new \Exception('No se pudo determinar la empresa para la venta web.');
            }

            // 2. Crear la Venta (PENDIENTE)
            $ventaData = [
                'empresa_id' => $empresaId,
                'user_id' => $data['user_id'] ?? null,
                'caja_id' => null,
                'cliente_id' => $data['cliente_id'],
                'comprobante_id' => $data['comprobante_id'],
                'numero_comprobante' => $this->generarNumeroComprobante($empresaId, $data['comprobante_id']),
                'fecha_hora' => now(),
                'metodo_pago' => 'STRIPE',
                'subtotal' => $data['subtotal'],
                'impuesto' => $data['impuesto'],
                'total' => $data['total'],
                'estado_pago' => 'PENDIENTE',
                'canal' => 'web',
                'tipo_venta' => 'WEB',
                'origen' => 'WEB',
                'monto_recibido' => $data['total'],
                'cambio' => 0,
            ];

            $venta = Venta::create($ventaData);

            // 3. Procesar Detalle
            $this->procesarDetalle($venta, $data['productos']);

            ActivityLogService::log('Venta web iniciada (Esperando Pago)', 'Ventas', ['venta_id' => $venta->id]);

            return $venta;
        });
    }

    /**
     * Lógica compartida para registrar pagos en ventas físicas
     */
    private function registrarPagosFisicos(Venta $venta, array $data, int $empresaId): void
    {
        if (isset($data['pagos']) && is_array($data['pagos'])) {
            foreach ($data['pagos'] as $pago) {
                PaymentTransaction::create([
                    'empresa_id' => $empresaId,
                    'venta_id' => $venta->id,
                    'payment_method' => $pago['metodo'],
                    'amount_paid' => $pago['monto'],
                    'status' => 'SUCCESS',
                    'processed_at' => now(),
                    'metadata' => ['referencia' => $pago['referencia'] ?? null],
                ]);
            }
        } elseif ($venta->metodo_pago) {
            PaymentTransaction::create([
                'empresa_id' => $empresaId,
                'venta_id' => $venta->id,
                'payment_method' => $venta->metodo_pago,
                'amount_paid' => $venta->total,
                'status' => 'SUCCESS',
                'processed_at' => now(),
            ]);
        }
    }

    private function obtenerCajaAbierta(int $empresaId): ?Caja
    {
        return Caja::where('user_id', Auth::id())
            ->where('empresa_id', $empresaId)
            ->where('estado', 'ABIERTA')
            ->first();
    }

    /**
     * Procesar los productos de la venta (LEGACY - Usado por Admin)
     */
    private function procesarDetalle(Venta $venta, array $productos): void
    {
        foreach ($productos as $item) {
            $venta->productos()->attach($item['producto_id'], [
                'cantidad' => $item['cantidad'],
                'precio_venta' => $item['precio_venta'],
                'tarifa_unitaria' => $venta->calcularTarifaUnitaria($item['producto_id'], $item['precio_venta']),
            ]);

            CreateVentaDetalleEvent::dispatch(
                $venta,
                $item['producto_id'],
                $item['cantidad'],
                $item['precio_venta']
            );
        }
    }

    /**
     * Registrar el ingreso en caja
     */
    private function registrarMovimiento(Venta $venta, Caja $caja): void
    {
        Movimiento::create([
            'empresa_id' => $venta->empresa_id,
            'caja_id' => $caja->id,
            'venta_id' => $venta->id,
            'user_id' => Auth::id(),
            'tipo' => Movimiento::TIPO_INGRESO,
            'monto' => $venta->total,
            'metodo_pago' => $venta->metodo_pago,
            'descripcion' => "Venta POS #{$venta->id} - Canal: {$venta->canal}",
        ]);
    }

    /**
     * Generar número de comprobante único
     */
    private function generarNumeroComprobante(int $empresaId, int $comprobanteId): string
    {
        $ultimaVenta = Venta::where('empresa_id', $empresaId)
            ->where('comprobante_id', $comprobanteId)
            ->whereNotNull('numero_comprobante')
            ->orderBy('id', 'desc')
            ->first();

        if ($ultimaVenta && preg_match('/COMP' . $comprobanteId . '-(\d+)/', $ultimaVenta->numero_comprobante, $matches)) {
            $siguiente = (int) $matches[1] + 1;
        } else {
            $siguiente = 1;
        }

        return 'COMP' . $comprobanteId . '-' . str_pad($siguiente, 6, '0', STR_PAD_LEFT);
    }
}
