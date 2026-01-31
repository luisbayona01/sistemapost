<?php

namespace App\Services;

use App\Models\Empresa;
use App\Models\PaymentTransaction;
use App\Models\StripeConfig;
use App\Models\Venta;
use Illuminate\Support\Facades\Log;
use Stripe\Account;
use Stripe\AccountLink;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Stripe\StripeClient;
use Throwable;

/**
 * StripeConnectService
 *
 * Servicio para gestionar Stripe Connect con split automático de pagos.
 *
 * Flujo:
 * 1. Crear cuenta Connected Account en Stripe
 * 2. Generar URL de onboarding para que usuario complete información bancaria
 * 3. Al procesar pagos, usar application_fee_amount para retener tarifa de plataforma
 * 4. Transferir resto a cuenta conectada del merchant (empresa)
 *
 * Compatibilidad:
 * - Si empresa NO tiene stripe_account_id: usa modo directo (sin Connect)
 * - Si empresa TIENE stripe_account_id: usa Connect con split de pagos
 */
class StripeConnectService
{
    protected StripeClient $stripe;
    protected StripeConfig $config;
    protected Empresa $empresa;

    const STATUS_NOT_STARTED = 'NOT_STARTED';
    const STATUS_PENDING = 'PENDING';
    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_REJECTED = 'REJECTED';
    const STATUS_UNDER_REVIEW = 'UNDER_REVIEW';

    /**
     * Inicializar servicio
     */
    public function __construct(Empresa $empresa = null)
    {
        if (!$empresa) {
            $empresa_id = auth()->user()->empresa_id ?? null;
            if (!$empresa_id) {
                throw new \Exception('No se puede determinar la empresa del usuario');
            }
            $empresa = Empresa::findOrFail($empresa_id);
        }

        $this->empresa = $empresa;

        // Obtener configuración Stripe de la empresa
        $this->config = StripeConfig::where('empresa_id', $empresa->id)
            ->where('enabled', true)
            ->firstOrFail();

        Stripe::setApiKey($this->config->getSecretKey());
        $this->stripe = new StripeClient(['api_key' => $this->config->getSecretKey()]);
    }

    /**
     * Crear una cuenta Connected Account en Stripe
     *
     * @param string $type Tipo: 'standard' (recomendado) o 'custom'
     * @return Account
     * @throws Throwable
     */
    public function createConnectedAccount(string $type = 'standard'): Account
    {
        try {
            Log::info('Creando Connected Account en Stripe', [
                'empresa_id' => $this->empresa->id,
                'empresa_nombre' => $this->empresa->nombre,
            ]);

            // Crear cuenta conectada
            $account = $this->stripe->accounts->create([
                'type' => 'express', // Express account (simplifica onboarding)
                'country' => 'US', // Configurar según región
                'email' => $this->empresa->correo,
                'business_profile' => [
                    'name' => $this->empresa->nombre,
                    'product_description' => 'Cinema POS - Payment Processing',
                    'url' => config('app.url'), // URL de la plataforma
                ],
                'metadata' => [
                    'empresa_id' => $this->empresa->id,
                    'empresa_nombre' => $this->empresa->nombre,
                    'platform' => 'CinemaPOS',
                ],
            ]);

            // Guardar account_id en la empresa
            $this->empresa->update([
                'stripe_account_id' => $account->id,
                'stripe_connect_status' => self::STATUS_PENDING,
                'stripe_connect_updated_at' => now(),
            ]);

            Log::info('Connected Account creada exitosamente', [
                'empresa_id' => $this->empresa->id,
                'stripe_account_id' => $account->id,
            ]);

            return $account;
        } catch (Throwable $e) {
            Log::error('Error creando Connected Account', [
                'empresa_id' => $this->empresa->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Generar URL de onboarding para que el usuario complete información
     *
     * @param string $successUrl URL a redirigir después de completar onboarding
     * @param string $refreshUrl URL a redirigir si el usuario sale sin completar
     * @return string URL de onboarding
     * @throws Throwable
     */
    public function generateOnboardingUrl(
        string $successUrl = null,
        string $refreshUrl = null
    ): string {
        try {
            // Si no existe cuenta conectada, crearla primero
            if (!$this->empresa->stripe_account_id) {
                $this->createConnectedAccount();
            }

            $successUrl = $successUrl ?? route('stripe.onboarding.success');
            $refreshUrl = $refreshUrl ?? route('stripe.onboarding.refresh');

            Log::info('Generando URL de onboarding', [
                'empresa_id' => $this->empresa->id,
                'stripe_account_id' => $this->empresa->stripe_account_id,
            ]);

            // Crear AccountLink para onboarding
            $accountLink = $this->stripe->accountLinks->create([
                'account' => $this->empresa->stripe_account_id,
                'type' => 'account_onboarding',
                'refresh_url' => $refreshUrl,
                'return_url' => $successUrl,
            ]);

            // Guardar URL de onboarding
            $this->empresa->update([
                'stripe_onboarding_url' => $accountLink->url,
            ]);

            Log::info('URL de onboarding generada', [
                'empresa_id' => $this->empresa->id,
                'onboarding_url' => $accountLink->url,
            ]);

            return $accountLink->url;
        } catch (Throwable $e) {
            Log::error('Error generando URL de onboarding', [
                'empresa_id' => $this->empresa->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Verificar estado del onboarding
     *
     * @return string Estado: NOT_STARTED|PENDING|ACTIVE|REJECTED|UNDER_REVIEW
     * @throws Throwable
     */
    public function checkOnboardingStatus(): string
    {
        try {
            if (!$this->empresa->stripe_account_id) {
                return self::STATUS_NOT_STARTED;
            }

            // Obtener información de la cuenta desde Stripe
            $account = $this->stripe->accounts->retrieve(
                $this->empresa->stripe_account_id
            );

            $status = self::STATUS_NOT_STARTED;

            // Determinar estado basado en información de Stripe
            if ($account->charges_enabled && $account->payouts_enabled) {
                $status = self::STATUS_ACTIVE;
            } elseif ($account->requirements) {
                if ($account->requirements->currently_due &&
                    count($account->requirements->currently_due) > 0) {
                    $status = self::STATUS_PENDING;
                }

                if ($account->requirements->eventually_due &&
                    count($account->requirements->eventually_due) > 0) {
                    $status = self::STATUS_UNDER_REVIEW;
                }
            }

            // Si fue rechazada
            if (!$account->active) {
                $status = self::STATUS_REJECTED;
            }

            // Actualizar estado en BD
            $this->empresa->update([
                'stripe_connect_status' => $status,
                'stripe_connect_updated_at' => now(),
            ]);

            Log::info('Estado de onboarding verificado', [
                'empresa_id' => $this->empresa->id,
                'status' => $status,
            ]);

            return $status;
        } catch (Throwable $e) {
            Log::error('Error verificando onboarding', [
                'empresa_id' => $this->empresa->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Crear PaymentIntent con split automático de pagos
     *
     * Si la empresa está onboarded en Connect:
     * - Retiene application_fee_amount (tarifa de plataforma)
     * - Transfiere resto a la cuenta conectada
     *
     * Si NO está onboarded:
     * - Funciona como antes (sin Connect)
     *
     * @param Venta $venta
     * @param array $metadata Datos adicionales
     * @return PaymentTransaction
     * @throws Throwable
     */
    public function createPaymentIntentWithSplit(
        Venta $venta,
        array $metadata = []
    ): PaymentTransaction {
        try {
            if ($venta->empresa_id !== $this->empresa->id) {
                throw new \Exception('La venta no pertenece a esta empresa');
            }

            $amountCents = intval($venta->total * 100);

            // Metadata base
            $stripeMetadata = array_merge([
                'venta_id' => $venta->id,
                'empresa_id' => $venta->empresa_id,
                'cliente_email' => $venta->cliente->persona->email ?? 'no-email@example.com',
                'tarifa_servicio' => $venta->tarifa_servicio ?? 0,
            ], $metadata);

            // Calcular application_fee si está en Connect y onboarded
            $applicationFeeAmount = null;
            $transferData = null;

            if ($this->empresa->stripe_account_id &&
                $this->empresa->stripe_connect_status === self::STATUS_ACTIVE) {

                // Calcular monto de tarifa de plataforma basado en tarifa_servicio
                $feeAmount = $venta->getMontaTarifa();
                $applicationFeeAmount = intval($feeAmount * 100);

                // Configurar transferencia a cuenta conectada
                $transferData = [
                    'destination' => $this->empresa->stripe_account_id,
                ];

                Log::info('Split de pagos activado', [
                    'venta_id' => $venta->id,
                    'total_cents' => $amountCents,
                    'fee_cents' => $applicationFeeAmount,
                    'transfer_cents' => $amountCents - $applicationFeeAmount,
                ]);
            }

            // Crear PaymentIntent con o sin split
            $paymentIntentData = [
                'amount' => $amountCents,
                'currency' => strtolower($this->empresa->moneda->codigo ?? 'usd'),
                'description' => "Venta #{$venta->id} - {$venta->cliente->persona->razon_social}",
                'metadata' => $stripeMetadata,
            ];

            // Agregar datos de split si aplica
            if ($applicationFeeAmount) {
                $paymentIntentData['application_fee_amount'] = $applicationFeeAmount;
                $paymentIntentData['transfer_data'] = $transferData;
            }

            // Crear en Stripe
            $paymentIntent = $this->stripe->paymentIntents->create($paymentIntentData);

            // Guardar transacción
            $transaction = PaymentTransaction::create([
                'empresa_id' => $venta->empresa_id,
                'venta_id' => $venta->id,
                'payment_method' => 'STRIPE',
                'stripe_payment_intent_id' => $paymentIntent->id,
                'amount_paid' => $venta->total,
                'currency' => strtoupper($this->empresa->moneda->codigo ?? 'USD'),
                'status' => 'PENDING',
                'metadata' => [
                    'stripe_client_secret' => $paymentIntent->client_secret,
                    'stripe_status' => $paymentIntent->status,
                    'connect_enabled' => (bool)($this->empresa->stripe_account_id &&
                        $this->empresa->stripe_connect_status === self::STATUS_ACTIVE),
                    'application_fee_amount' => $applicationFeeAmount,
                ],
            ]);

            Log::info('PaymentIntent con split creado', [
                'venta_id' => $venta->id,
                'payment_intent_id' => $paymentIntent->id,
                'connect_enabled' => (bool)$applicationFeeAmount,
            ]);

            return $transaction;
        } catch (Throwable $e) {
            Log::error('Error creando PaymentIntent con split', [
                'venta_id' => $venta->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Obtener información de la cuenta conectada
     *
     * Retorna detalles como:
     * - charges_enabled, payouts_enabled
     * - required_fields (campos faltantes)
     * - earning (ingresos generados)
     *
     * @return Account|null
     */
    public function getConnectedAccountInfo(): ?Account
    {
        try {
            if (!$this->empresa->stripe_account_id) {
                return null;
            }

            $account = $this->stripe->accounts->retrieve(
                $this->empresa->stripe_account_id
            );

            return $account;
        } catch (Throwable $e) {
            Log::error('Error obteniendo información de cuenta', [
                'empresa_id' => $this->empresa->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Verificar si la empresa está lista para Stripe Connect
     *
     * Condiciones:
     * - Tiene stripe_account_id
     * - Estado es ACTIVE
     * - Tiene charges_enabled y payouts_enabled
     *
     * @return bool
     */
    public function isReadyForConnect(): bool
    {
        if (!$this->empresa->stripe_account_id ||
            $this->empresa->stripe_connect_status !== self::STATUS_ACTIVE) {
            return false;
        }

        try {
            $account = $this->getConnectedAccountInfo();
            return $account && $account->charges_enabled && $account->payouts_enabled;
        } catch (Throwable $e) {
            Log::warning('Error verificando readiness', [
                'empresa_id' => $this->empresa->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Desconectar cuenta de Stripe Connect
     *
     * Nota: Los pagos anteriores siguen siendo válidos
     * Solo se detiene el split de nuevos pagos
     *
     * @return bool
     * @throws Throwable
     */
    public function disconnectAccount(): bool
    {
        try {
            $this->empresa->update([
                'stripe_account_id' => null,
                'stripe_connect_status' => self::STATUS_NOT_STARTED,
                'stripe_onboarding_url' => null,
                'stripe_connect_updated_at' => now(),
            ]);

            Log::info('Cuenta Stripe Connect desconectada', [
                'empresa_id' => $this->empresa->id,
            ]);

            return true;
        } catch (Throwable $e) {
            Log::error('Error desconectando cuenta', [
                'empresa_id' => $this->empresa->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
