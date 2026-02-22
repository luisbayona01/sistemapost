<?php

namespace App\Services;

use App\Models\Empresa;
use App\Models\SaaSPlan;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Stripe\Customer;
use Stripe\Subscription;

class SubscriptionService
{
    /**
     * Crear una nueva suscripción para una empresa
     *
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function createSubscription(array $data): array
    {
        try {
            // Validar datos requeridos
            if (empty($data['plan_id']) || empty($data['name']) || empty($data['email'])) {
                throw new \Exception('Faltan datos requeridos para crear la suscripción.');
            }

            // Obtener el plan
            $plan = SaaSPlan::findOrFail($data['plan_id']);

            // Inicializar Stripe
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

            // Crear cliente en Stripe
            $stripeCustomer = Customer::create([
                'email' => $data['email'],
                'name' => $data['name'],
                'metadata' => [
                    'empresa_name' => $data['name'],
                ],
            ]);

            // Crear suscripción en Stripe
            $stripeSubscription = Subscription::create([
                'customer' => $stripeCustomer->id,
                'items' => [
                    [
                        'price' => $plan->stripe_price_id,
                    ],
                ],
                'trial_period_days' => $plan->dias_trial,
                'expand' => ['latest_invoice.payment_intent'],
            ]);

            return [
                'success' => true,
                'stripe_customer_id' => $stripeCustomer->id,
                'stripe_subscription_id' => $stripeSubscription->id,
                'estado_suscripcion' => $this->mapStripeStatus($stripeSubscription->status),
            ];
        } catch (\Exception $e) {
            // FALLBACK PARA DESARROLLO LOCAL
            // Si falla Stripe (ej. keys expiradas) y estamos en local, simulamos éxito
            if (config('app.env') === 'local') {
                return [
                    'success' => true,
                    'stripe_customer_id' => 'cus_test_' . \Illuminate\Support\Str::random(10),
                    'stripe_subscription_id' => 'sub_test_' . \Illuminate\Support\Str::random(10),
                    'estado_suscripcion' => 'trial',
                ];
            }

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Crear una empresa con suscripción y usuario admin
     *
     * @param array $empresaData
     * @param array $userData
     * @param int $planId
     * @return array
     */
    public function createEmpresaWithSubscription(
        array $empresaData,
        array $userData,
        int $planId
    ): array {
        try {
            return DB::transaction(function () use ($empresaData, $userData, $planId) {
                // Crear la empresa
                $empresa = Empresa::create(array_merge($empresaData, [
                    'plan_id' => $planId,
                    'tarifa_servicio_porcentaje' => 2.50, // Default 2.5%
                    'estado' => 'activa',
                    'estado_suscripcion' => 'trial',
                ]));

                // Crear suscripción en Stripe
                $subscriptionResult = $this->createSubscription([
                    'plan_id' => $planId,
                    'name' => $empresa->nombre,
                    'email' => $userData['email'],
                ]);

                if (!$subscriptionResult['success']) {
                    throw new \Exception($subscriptionResult['error']);
                }

                // Actualizar empresa con datos de Stripe
                $empresa->update([
                    'stripe_customer_id' => $subscriptionResult['stripe_customer_id'],
                    'stripe_subscription_id' => $subscriptionResult['stripe_subscription_id'],
                    'estado_suscripcion' => $subscriptionResult['estado_suscripcion'],
                    'fecha_onboarding_completado' => now(),
                ]);

                // Crear usuario admin para la empresa
                $usuario = User::create(array_merge($userData, [
                    'empresa_id' => $empresa->id,
                    'password' => bcrypt($userData['password']),
                    'estado' => 1,
                ]));

                // Asignar rol administrador
                $usuario->assignRole('administrador');

                return [
                    'success' => true,
                    'empresa' => $empresa,
                    'usuario' => $usuario,
                    'mensaje' => 'Empresa y suscripción creadas exitosamente.',
                ];
            });
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Actualizar el estado de una suscripción desde Stripe
     *
     * @param string $stripeSubscriptionId
     * @return bool
     */
    public function updateSubscriptionStatus(string $stripeSubscriptionId): bool
    {
        try {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

            $stripeSubscription = Subscription::retrieve($stripeSubscriptionId);
            $empresa = Empresa::where('stripe_subscription_id', $stripeSubscriptionId)->first();

            if (!$empresa) {
                return false;
            }

            $empresa->update([
                'estado_suscripcion' => $this->mapStripeStatus($stripeSubscription->status),
                'fecha_proximo_pago' => isset($stripeSubscription->current_period_end)
                    ? \Carbon\Carbon::createFromTimestamp($stripeSubscription->current_period_end)
                    : null,
            ]);

            return true;
        } catch (\Exception $e) {
            \Log::error('Error actualizando estado de suscripción: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Cancelar una suscripción
     *
     * @param string $stripeSubscriptionId
     * @return bool
     */
    public function cancelSubscription(string $stripeSubscriptionId): bool
    {
        try {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

            Subscription::retrieve($stripeSubscriptionId)->cancel();

            $empresa = Empresa::where('stripe_subscription_id', $stripeSubscriptionId)->first();
            if ($empresa) {
                $empresa->update([
                    'estado_suscripcion' => 'cancelled',
                ]);
            }

            return true;
        } catch (\Exception $e) {
            \Log::error('Error cancelando suscripción: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Cambiar el plan de una suscripción
     *
     * @param string $stripeSubscriptionId
     * @param string $newPriceId
     * @return bool
     */
    public function changePlan(string $stripeSubscriptionId, string $newPriceId): bool
    {
        try {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

            $subscription = Subscription::retrieve($stripeSubscriptionId);

            $subscription->items->data[0]->delete();
            $subscription->items->create([
                'price' => $newPriceId,
            ]);

            return true;
        } catch (\Exception $e) {
            \Log::error('Error cambiando plan: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Mapear el estado de Stripe al estado interno
     *
     * @param string $stripeStatus
     * @return string
     */
    private function mapStripeStatus(string $stripeStatus): string
    {
        return match ($stripeStatus) {
            'trialing' => 'trial',
            'active' => 'active',
            'past_due' => 'past_due',
            'canceled', 'unpaid' => 'cancelled',
            default => 'active',
        };
    }

    /**
     * Calcular tarifa por transacción
     *
     * @param Empresa $empresa
     * @param float $monto
     * @return float
     */
    public function calcularTarifa(Empresa $empresa, float $monto): float
    {
        return ($monto * $empresa->tarifa_servicio_porcentaje) / 100;
    }

    /**
     * Registrar tarifa en la empresa
     *
     * @param Empresa $empresa
     * @param float $montoTarifa
     * @return void
     */
    public function registrarTarifa(Empresa $empresa, float $montoTarifa): void
    {
        $empresa->increment('tarifa_servicio_monto', $montoTarifa);
    }
}
