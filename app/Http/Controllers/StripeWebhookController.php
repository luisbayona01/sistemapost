<?php

namespace App\Http\Controllers;

use App\Models\StripeConfig;
use App\Services\StripePaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Throwable;

class StripeWebhookController extends Controller
{
    /**
     * Manejar webhook de Stripe
     *
     * Endpoint: POST /webhooks/stripe
     *
     * Este endpoint recibe eventos de Stripe sin autenticación (webhook de tercero).
     * La autenticación se hace mediante firma del webhook.
     */
    public function __invoke(Request $request): Response
    {
        try {
            // Obtener el JSON raw del request
            $eventJson = $request->getContent();
            $signature = $request->header('Stripe-Signature');

            if (!$signature) {
                Log::warning('Webhook sin firma de Stripe');
                return response('No signature provided', Response::HTTP_BAD_REQUEST);
            }

            // Obtener el ID de la empresa desde los metadatos del webhook
            // Por ahora, soportamos una sola empresa por instalación
            // En multi-tenant, extraería el empresa_id del metadata del evento
            $stripeConfig = StripeConfig::where('enabled', true)->first();

            if (!$stripeConfig) {
                Log::error('No se encontró configuración de Stripe habilitada');
                return response('Stripe not configured', Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            // Procesar el webhook
            $service = new StripePaymentService($stripeConfig);
            $service->handleWebhook($eventJson, $signature);

            return response('Webhook processed successfully', Response::HTTP_OK);
        } catch (Throwable $e) {
            Log::error('Error en webhook de Stripe', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response('Webhook error: ' . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
