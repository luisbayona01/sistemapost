<?php

namespace App\Services;

use App\Models\PaymentTransaction;
use App\Models\StripeConfig;
use App\Models\Venta;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Stripe\StripeClient;
use Throwable;

class StripePaymentService
{
    protected StripeClient $stripe;
    protected StripeConfig $config;

    /**
     * Inicializar servicio con configuración de Stripe
     */
    public function __construct(StripeConfig $config = null)
    {
        if (!$config) {
            // Obtener configuración de la empresa del usuario autenticado
            $empresa_id = auth()->user()->empresa_id ?? null;
            if (!$empresa_id) {
                throw new \Exception('No se puede determinar la empresa del usuario');
            }

            $config = StripeConfig::where('empresa_id', $empresa_id)
                ->where('enabled', true)
                ->firstOrFail();
        }

        $this->config = $config;
        Stripe::setApiKey($this->config->getSecretKey());
        $this->stripe = new StripeClient(['api_key' => $this->config->getSecretKey()]);
    }

    /**
     * Crear un PaymentIntent para una venta
     *
     * @param Venta $venta
     * @param array $metadata Datos adicionales (opcional)
     * @return PaymentTransaction
     * @throws Throwable
     */
    public function createPaymentIntent(Venta $venta, array $metadata = []): PaymentTransaction
    {
        try {
            // Validar que la venta existe y pertenece a la empresa
            if ($venta->empresa_id !== $this->config->empresa_id) {
                throw new \Exception('La venta no pertenece a esta empresa');
            }

            // Monto en centavos para Stripe
            $amountCents = intval($venta->total * 100);

            // Metadata a enviar a Stripe
            $stripeMetadata = array_merge([
                'venta_id' => $venta->id,
                'empresa_id' => $venta->empresa_id,
                'cliente_email' => $venta->cliente->persona->email ?? 'no-email@example.com',
            ], $metadata);

            // Crear PaymentIntent en Stripe
            $paymentIntent = $this->stripe->paymentIntents->create([
                'amount' => $amountCents,
                'currency' => strtolower($this->config->empresa->moneda->codigo ?? 'usd'),
                'description' => "Venta #{$venta->id} - {$venta->cliente->persona->razon_social}",
                'metadata' => $stripeMetadata,
            ]);

            // Guardar transacción en BD
            $transaction = PaymentTransaction::create([
                'empresa_id' => $venta->empresa_id,
                'venta_id' => $venta->id,
                'payment_method' => 'STRIPE',
                'stripe_payment_intent_id' => $paymentIntent->id,
                'amount_paid' => $venta->total,
                'currency' => strtoupper($this->config->empresa->moneda->codigo ?? 'USD'),
                'status' => 'PENDING',
                'metadata' => [
                    'stripe_client_secret' => $paymentIntent->client_secret,
                    'stripe_status' => $paymentIntent->status,
                ],
            ]);

            Log::info('PaymentIntent creado exitosamente', [
                'venta_id' => $venta->id,
                'payment_intent_id' => $paymentIntent->id,
                'amount' => $venta->total,
            ]);

            return $transaction;
        } catch (Throwable $e) {
            Log::error('Error al crear PaymentIntent', [
                'venta_id' => $venta->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Procesar webhook de Stripe
     *
     * @param string $eventJson JSON del webhook
     * @param string $signature Firma del webhook
     * @return void
     * @throws Throwable
     */
    public function handleWebhook(string $eventJson, string $signature): void
    {
        try {
            // Verificar firma del webhook
            $event = \Stripe\Webhook::constructEvent(
                $eventJson,
                $signature,
                $this->config->getWebhookSecret()
            );

            Log::info('Webhook recibido de Stripe', ['event_type' => $event->type]);

            match ($event->type) {
                'payment_intent.succeeded' => $this->handlePaymentSucceeded($event->data->object),
                'payment_intent.payment_failed' => $this->handlePaymentFailed($event->data->object),
                'payment_intent.canceled' => $this->handlePaymentCanceled($event->data->object),
                default => Log::info('Evento Stripe ignorado', ['event_type' => $event->type]),
            };
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error('Firma del webhook inválida', ['error' => $e->getMessage()]);
            throw $e;
        } catch (Throwable $e) {
            Log::error('Error procesando webhook', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Procesar pago exitoso
     *
     * @param PaymentIntent $paymentIntent
     * @return void
     */
    private function handlePaymentSucceeded(PaymentIntent $paymentIntent): void
    {
        DB::transaction(function () use ($paymentIntent) {
            // Buscar la transacción de pago
            $transaction = PaymentTransaction::where('stripe_payment_intent_id', $paymentIntent->id)
                ->firstOrFail();

            // Obtener la venta
            $venta = Venta::findOrFail($transaction->venta_id);

            // Marcar transacción como exitosa
            $transaction->markAsSuccess([
                'stripe_charge_id' => $paymentIntent->charges->data[0]->id ?? null,
                'stripe_status' => $paymentIntent->status,
            ]);

            // Marcar venta como pagada
            $venta->update(['estado_pago' => 'PAGADA']);

            // Disparar evento para descuento de inventario y envío de email
            event(new \App\Events\CreateVentaEvent($venta));

            // Crear movimiento de caja si no existe
            $movimiento = $venta->movimientos()
                ->where('tipo', 'INGRESO')
                ->whereNull('pago_completado_en')
                ->first();

            if ($movimiento) {
                $movimiento->update(['pago_completado_en' => now()]);
            }

            Log::info('Pago procesado exitosamente y evento de inventario disparado', [
                'venta_id' => $venta->id,
                'payment_intent_id' => $paymentIntent->id,
                'amount' => $paymentIntent->amount / 100,
            ]);
        });
    }

    /**
     * Procesar pago fallido
     *
     * @param PaymentIntent $paymentIntent
     * @return void
     */
    private function handlePaymentFailed(PaymentIntent $paymentIntent): void
    {
        DB::transaction(function () use ($paymentIntent) {
            // Buscar la transacción de pago
            $transaction = PaymentTransaction::where('stripe_payment_intent_id', $paymentIntent->id)
                ->first();

            if (!$transaction) {
                Log::warning('PaymentIntent no encontrado en BD', ['intent_id' => $paymentIntent->id]);
                return;
            }

            // Obtener el mensaje de error
            $errorMessage = $paymentIntent->last_payment_error?->message ?? 'Error de pago desconocido';

            // Marcar transacción como fallida
            $transaction->markAsFailed($errorMessage, [
                'stripe_status' => $paymentIntent->status,
            ]);

            // Obtener la venta
            $venta = Venta::findOrFail($transaction->venta_id);

            // NO crear movimiento (el pago falló)
            Log::warning('Pago rechazado por Stripe', [
                'venta_id' => $venta->id,
                'payment_intent_id' => $paymentIntent->id,
                'error' => $errorMessage,
            ]);
        });
    }

    /**
     * Procesar pago cancelado
     *
     * @param PaymentIntent $paymentIntent
     * @return void
     */
    private function handlePaymentCanceled(PaymentIntent $paymentIntent): void
    {
        DB::transaction(function () use ($paymentIntent) {
            $transaction = PaymentTransaction::where('stripe_payment_intent_id', $paymentIntent->id)
                ->first();

            if ($transaction) {
                $transaction->update([
                    'status' => 'CANCELLED',
                    'error_message' => 'Pago cancelado por el cliente o por el sistema',
                ]);

                Log::info('Pago cancelado', [
                    'payment_intent_id' => $paymentIntent->id,
                ]);
            }
        });
    }

    /**
     * Obtener el secret del cliente para el frontend
     *
     * @param PaymentTransaction $transaction
     * @return string|null
     */
    public function getClientSecret(PaymentTransaction $transaction): ?string
    {
        return $transaction->metadata['stripe_client_secret'] ?? null;
    }

    /**
     * Confirmar el PaymentIntent en el servidor
     *
     * @param PaymentTransaction $transaction
     * @return PaymentIntent
     */
    public function confirmPaymentIntent(PaymentTransaction $transaction): PaymentIntent
    {
        $paymentIntent = $this->stripe->paymentIntents->retrieve($transaction->stripe_payment_intent_id);

        Log::info('PaymentIntent consultado', [
            'intent_id' => $paymentIntent->id,
            'status' => $paymentIntent->status,
        ]);

        return $paymentIntent;
    }
}
