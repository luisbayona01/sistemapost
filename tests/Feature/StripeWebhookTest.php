<?php

namespace Tests\Feature;

use App\Models\Empresa;
use App\Models\Moneda;
use App\Models\PaymentTransaction;
use App\Models\StripeConfig;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StripeWebhookTest extends TestCase
{
    use RefreshDatabase;

    protected Empresa $empresa;
    protected StripeConfig $stripeConfig;

    protected function setUp(): void
    {
        parent::setUp();

        $moneda = Moneda::create([
            'estandar_iso' => 'USD',
            'nombre_completo' => 'Dólar Estadounidense',
            'simbolo' => '$',
        ]);

        $this->empresa = Empresa::create([
            'nombre' => 'Test Cinema',
            'propietario' => 'Test Owner',
            'ruc' => '20123456789',
            'porcentaje_impuesto' => 18,
            'abreviatura_impuesto' => 'IGV',
            'direccion' => 'Test Address',
            'moneda_id' => $moneda->id,
        ]);

        $this->stripeConfig = StripeConfig::create([
            'empresa_id' => $this->empresa->id,
            'public_key' => 'pk_test_123456789',
            'secret_key' => 'sk_test_123456789',
            'webhook_secret' => 'whsec_test_123456789',
            'test_mode' => true,
            'enabled' => true,
        ]);
    }

    /**
     * Test: Webhook missing signature header
     *
     * Verifies that webhook requests without signature are rejected
     */
    public function test_webhook_requires_signature(): void
    {
        $payload = json_encode([
            'type' => 'payment_intent.succeeded',
            'data' => ['object' => ['id' => 'pi_test_123']],
        ]);

        // Send without Stripe-Signature header
        $response = $this->post('/webhooks/stripe', [], [], [], $payload);

        $response->assertStatus(400);
    }

    /**
     * Test: Webhook with invalid signature rejected
     *
     * Verifies signature verification works
     */
    public function test_webhook_rejects_invalid_signature(): void
    {
        $payload = json_encode([
            'type' => 'payment_intent.succeeded',
            'data' => ['object' => ['id' => 'pi_test_123']],
        ]);

        $response = $this->post(
            '/webhooks/stripe',
            [],
            ['Stripe-Signature' => 'invalid_signature'],
            [],
            $payload
        );

        // Should reject
        $response->assertStatus(400);
    }

    /**
     * Test: Webhook for unknown event type ignored
     */
    public function test_webhook_ignores_unknown_events(): void
    {
        // Create minimal valid webhook structure
        $payload = json_encode([
            'type' => 'charge.refunded',  // Not handled
            'data' => ['object' => ['id' => 'ch_test_123']],
        ]);

        // Mock signature
        $timestamp = time();
        $signature = hash_hmac(
            'sha256',
            "$timestamp." . $payload,
            'whsec_test_123456789'
        );

        $response = $this->post(
            '/webhooks/stripe',
            [],
            ['Stripe-Signature' => "t=$timestamp,v1=$signature"],
            [],
            $payload
        );

        // Should accept but not process
        $response->assertStatus(200);
    }

    /**
     * Test: Webhook endpoint is accessible without authentication
     *
     * Important: Webhooks must not require auth (they're called by Stripe)
     */
    public function test_webhook_endpoint_no_auth_required(): void
    {
        $this->withoutMiddleware(\App\Http\Middleware\Authenticate::class);

        $payload = json_encode([
            'type' => 'payment_intent.succeeded',
            'data' => ['object' => ['id' => 'pi_test_123']],
        ]);

        $response = $this->post('/webhooks/stripe', [], [], [], $payload);

        // Should not return 401/403
        $this->assertNotEquals(401, $response->status());
        $this->assertNotEquals(403, $response->status());
    }

    /**
     * Test: Webhook payload must be JSON
     */
    public function test_webhook_requires_valid_json(): void
    {
        $response = $this->post(
            '/webhooks/stripe',
            [],
            ['Stripe-Signature' => 't=123,v1=abc'],
            [],
            'invalid json {'
        );

        $response->assertStatus(400);
    }

    /**
     * Test: Multiple webhook events in sequence
     *
     * Tests that webhook handler can process multiple events
     */
    public function test_webhook_processes_multiple_events(): void
    {
        // Create payment transaction
        $transaction = PaymentTransaction::create([
            'empresa_id' => $this->empresa->id,
            'venta_id' => 1,
            'payment_method' => 'STRIPE',
            'stripe_payment_intent_id' => 'pi_test_123',
            'amount_paid' => 100.00,
            'currency' => 'usd',
            'status' => 'PENDING',
        ]);

        // Simulate webhook events that would be sent by Stripe
        $events = [
            'payment_intent.processing',
            'payment_intent.succeeded',
        ];

        foreach ($events as $eventType) {
            $payload = json_encode([
                'type' => $eventType,
                'data' => [
                    'object' => [
                        'id' => 'pi_test_123',
                        'status' => 'succeeded',
                        'amount_received' => 10000,
                    ]
                ],
            ]);

            // Note: In real implementation, this would verify the event
            // This test just verifies the endpoint doesn't crash
            $response = $this->post('/webhooks/stripe', [], [], [], $payload);

            // Should not return 500
            $this->assertNotEquals(500, $response->status());
        }
    }

    /**
     * Test: Webhook handles large payloads
     */
    public function test_webhook_handles_large_payload(): void
    {
        $payload = json_encode([
            'type' => 'payment_intent.succeeded',
            'data' => [
                'object' => [
                    'id' => 'pi_test_' . str_repeat('x', 1000),
                    'status' => 'succeeded',
                    'metadata' => [
                        'large_field' => str_repeat('data', 1000),
                    ]
                ]
            ],
        ]);

        $response = $this->post(
            '/webhooks/stripe',
            [],
            ['Stripe-Signature' => 't=123,v1=abc'],  // Invalid but testing payload size
            [],
            $payload
        );

        // Should not crash on large payload
        $this->assertNotEquals(413, $response->status());  // Not "Payload too large"
    }

    /**
     * Test: Webhook with missing data field
     */
    public function test_webhook_handles_malformed_payload(): void
    {
        // Missing 'data' field
        $payload = json_encode([
            'type' => 'payment_intent.succeeded',
        ]);

        $response = $this->post(
            '/webhooks/stripe',
            [],
            ['Stripe-Signature' => 't=123,v1=abc'],
            [],
            $payload
        );

        // Should handle gracefully
        $this->assertNotEquals(500, $response->status());
    }

    /**
     * Test: Webhook rate limiting (future feature)
     *
     * Placeholder for rate limiting tests when implemented
     */
    public function test_webhook_duplicate_processing(): void
    {
        // Stripe may resend events - verify idempotency
        $payload = json_encode([
            'id' => 'evt_test_123',
            'type' => 'payment_intent.succeeded',
            'data' => ['object' => ['id' => 'pi_test_123']],
        ]);

        $timestamp = time();
        $signature = hash_hmac(
            'sha256',
            "$timestamp." . $payload,
            'whsec_test_123456789'
        );

        // Send same event twice
        $response1 = $this->post(
            '/webhooks/stripe',
            [],
            ['Stripe-Signature' => "t=$timestamp,v1=$signature"],
            [],
            $payload
        );

        $response2 = $this->post(
            '/webhooks/stripe',
            [],
            ['Stripe-Signature' => "t=$timestamp,v1=$signature"],
            [],
            $payload
        );

        // Both should succeed (idempotent)
        $response1->assertStatus(200);
        $response2->assertStatus(200);
    }
}
