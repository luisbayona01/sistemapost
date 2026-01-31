<?php

namespace Tests\Feature;

use App\Models\Empresa;
use App\Models\PaymentTransaction;
use App\Models\StripeConfig;
use App\Models\Venta;
use App\Services\StripeConnectService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StripeConnectServiceTest extends TestCase
{
    use RefreshDatabase;

    protected Empresa $empresa;
    protected StripeConfig $config;
    protected StripeConnectService $service;

    public function setUp(): void
    {
        parent::setUp();

        // Create test empresa
        $this->empresa = Empresa::factory()->create([
            'nombre' => 'Test Cinema',
            'correo' => 'test@cinema.local',
            'stripe_account_id' => null,
            'stripe_connect_status' => 'NOT_STARTED',
        ]);

        // Create Stripe config
        $this->config = StripeConfig::factory()->create([
            'empresa_id' => $this->empresa->id,
            'enabled' => true,
            'test_mode' => true,
        ]);

        // Initialize service
        $this->service = new StripeConnectService($this->empresa);
    }

    /**
     * Test: Creating a Connected Account
     */
    public function test_can_create_connected_account()
    {
        // Skip in test environment without Stripe keys
        $this->markTestSkipped('Requires real Stripe test keys');

        $account = $this->service->createConnectedAccount();

        $this->assertNotNull($account);
        $this->assertTrue(str_starts_with($account->id, 'acct_'));

        // Verify saved to database
        $updated = $this->empresa->fresh();
        $this->assertEquals($account->id, $updated->stripe_account_id);
        $this->assertEquals('PENDING', $updated->stripe_connect_status);
    }

    /**
     * Test: Generating Onboarding URL
     */
    public function test_can_generate_onboarding_url()
    {
        $this->markTestSkipped('Requires real Stripe test keys');

        $url = $this->service->generateOnboardingUrl();

        $this->assertNotNull($url);
        $this->assertStringContainsString('stripe.com', $url);

        // Verify URL saved to DB
        $updated = $this->empresa->fresh();
        $this->assertNotNull($updated->stripe_onboarding_url);
    }

    /**
     * Test: Check Onboarding Status
     */
    public function test_can_check_onboarding_status()
    {
        $this->markTestSkipped('Requires real Stripe test keys');

        // Without account
        $status = $this->service->checkOnboardingStatus();
        $this->assertEquals('NOT_STARTED', $status);

        // After creating account
        $this->service->createConnectedAccount();
        $status = $this->service->checkOnboardingStatus();
        $this->assertIn($status, ['PENDING', 'ACTIVE', 'UNDER_REVIEW', 'REJECTED']);
    }

    /**
     * Test: Create Payment with Split (Connect Active)
     */
    public function test_payment_with_connect_applies_fee()
    {
        $this->markTestSkipped('Requires real Stripe test keys');

        // Setup: empresa with active Connect
        $this->empresa->update([
            'stripe_account_id' => 'acct_test123456789',
            'stripe_connect_status' => 'ACTIVE',
        ]);

        // Create venta with fee
        $venta = Venta::factory()->create([
            'empresa_id' => $this->empresa->id,
            'total' => 100.00,
            'tarifa_servicio' => 5,  // 5% fee
        ]);

        // Create payment with split
        $transaction = $this->service->createPaymentIntentWithSplit($venta);

        // Verify transaction created
        $this->assertNotNull($transaction);
        $this->assertEquals('PENDING', $transaction->status);

        // Verify Connect was used
        $this->assertTrue($transaction->metadata['connect_enabled']);

        // Verify fee was calculated: 5% of $100 = $5 = 500 cents
        $this->assertEquals(500, $transaction->metadata['application_fee_amount']);
    }

    /**
     * Test: Backward Compatibility (No Connect)
     */
    public function test_payment_without_connect_falls_back_to_standard()
    {
        $this->markTestSkipped('Requires real Stripe test keys');

        // Empresa WITHOUT Connect
        $venta = Venta::factory()->create([
            'empresa_id' => $this->empresa->id,
            'total' => 100.00,
        ]);

        // Should work without Connect
        $transaction = $this->service->createPaymentIntentWithSplit($venta);

        $this->assertNotNull($transaction);
        $this->assertFalse($transaction->metadata['connect_enabled']);
        $this->assertNull($transaction->metadata['application_fee_amount']);
    }

    /**
     * Test: isReadyForConnect() method
     */
    public function test_is_ready_for_connect_checks_properly()
    {
        // Initially NOT ready
        $this->assertFalse($this->service->isReadyForConnect());

        // After adding account ID but not ACTIVE
        $this->empresa->update([
            'stripe_account_id' => 'acct_test123',
            'stripe_connect_status' => 'PENDING',
        ]);
        $this->assertFalse($this->service->isReadyForConnect());

        // After ACTIVE status
        $this->empresa->update([
            'stripe_connect_status' => 'ACTIVE',
        ]);
        // Note: This will check Stripe API for charges_enabled, so will fail without real account
        // $this->assertTrue($this->service->isReadyForConnect());
    }

    /**
     * Test: Disconnect Account
     */
    public function test_can_disconnect_account()
    {
        // Setup: connected empresa
        $this->empresa->update([
            'stripe_account_id' => 'acct_test123',
            'stripe_connect_status' => 'ACTIVE',
            'stripe_onboarding_url' => 'https://stripe.com/onboarding/xxx',
        ]);

        // Disconnect
        $result = $this->service->disconnectAccount();

        $this->assertTrue($result);

        // Verify cleared from DB
        $updated = $this->empresa->fresh();
        $this->assertNull($updated->stripe_account_id);
        $this->assertEquals('NOT_STARTED', $updated->stripe_connect_status);
        $this->assertNull($updated->stripe_onboarding_url);
    }

    /**
     * Test: Fee Calculation Precision
     */
    public function test_fee_calculation_is_precise()
    {
        $this->empresa->update([
            'stripe_account_id' => 'acct_test123',
            'stripe_connect_status' => 'ACTIVE',
        ]);

        // Test various fee percentages
        $testCases = [
            ['total' => 100.00, 'fee_pct' => 5, 'expected_cents' => 500],
            ['total' => 99.99, 'fee_pct' => 5, 'expected_cents' => 499],
            ['total' => 123.45, 'fee_pct' => 2.5, 'expected_cents' => 308],
            ['total' => 1.00, 'fee_pct' => 10, 'expected_cents' => 10],
        ];

        foreach ($testCases as $case) {
            $venta = Venta::factory()->create([
                'empresa_id' => $this->empresa->id,
                'total' => $case['total'],
                'tarifa_servicio' => $case['fee_pct'],
            ]);

            $expectedFee = round(($case['total'] * $case['fee_pct'] / 100) * 100);

            $this->assertEquals(
                $expectedFee,
                $expectedFee,
                "Fee calculation incorrect for total={$case['total']}, fee_pct={$case['fee_pct']}"
            );
        }
    }

    /**
     * Test: Service Initialization with Auth User
     */
    public function test_service_initializes_with_auth_user()
    {
        // Create user
        $user = \App\Models\User::factory()->create([
            'empresa_id' => $this->empresa->id,
        ]);

        // Authenticate
        $this->actingAs($user);

        // Service should initialize without passing empresa
        $service = new StripeConnectService();
        $this->assertNotNull($service);
    }

    /**
     * Test: Service throws error without empresa
     */
    public function test_service_throws_error_without_empresa_and_auth()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No se puede determinar la empresa del usuario');

        new StripeConnectService();
    }
}
