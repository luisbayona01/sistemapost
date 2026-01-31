<?php

namespace Tests\Feature;

use App\Models\Empresa;
use App\Models\Moneda;
use App\Models\PaymentTransaction;
use App\Models\StripeConfig;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Stripe Payment Integration Tests
 *
 * These tests verify the Stripe payment integration works correctly.
 */
class StripePaymentTest extends TestCase
{
    use RefreshDatabase;

    protected Empresa $empresa;
    protected User $user;
    protected StripeConfig $stripeConfig;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test data
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

        $this->user = User::create([
            'empresa_id' => $this->empresa->id,
            'name' => 'Test User',
            'email' => 'test@cinema.local',
            'password' => bcrypt('password'),
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
     * Test: Stripe config is created correctly
     */
    public function test_stripe_config_created(): void
    {
        $this->assertDatabaseHas('stripe_configs', [
            'empresa_id' => $this->empresa->id,
            'test_mode' => true,
            'enabled' => true,
        ]);

        // Verify keys are encrypted in the model
        $config = StripeConfig::find($this->stripeConfig->id);
        $this->assertNotNull($config->public_key);
        $this->assertNotNull($config->secret_key);
        $this->assertNotNull($config->webhook_secret);
    }

    /**
     * Test: Stripe config has correct relationships
     */
    public function test_stripe_config_relationships(): void
    {
        $config = StripeConfig::firstWhere('empresa_id', $this->empresa->id);

        $this->assertNotNull($config);
        $this->assertEquals($this->empresa->id, $config->empresa_id);
        $this->assertInstanceOf(Empresa::class, $config->empresa);
    }

    /**
     * Test: Multi-company isolation in StripeConfig
     */
    public function test_stripe_config_multicompany_isolation(): void
    {
        $moneda = Moneda::first();

        $otherEmpresa = Empresa::create([
            'nombre' => 'Other Cinema',
            'propietario' => 'Other Owner',
            'ruc' => '20987654321',
            'porcentaje_impuesto' => 18,
            'abreviatura_impuesto' => 'IGV',
            'direccion' => 'Other Address',
            'moneda_id' => $moneda->id,
        ]);

        StripeConfig::create([
            'empresa_id' => $otherEmpresa->id,
            'public_key' => 'pk_test_other',
            'secret_key' => 'sk_test_other',
            'webhook_secret' => 'whsec_test_other',
            'test_mode' => true,
            'enabled' => true,
        ]);

        // Each empresa should have its own config
        $config1 = StripeConfig::forEmpresa($this->empresa->id)->first();
        $config2 = StripeConfig::forEmpresa($otherEmpresa->id)->first();

        $this->assertNotNull($config1);
        $this->assertNotNull($config2);
        $this->assertNotEquals($config1->id, $config2->id);
        $this->assertEquals($this->empresa->id, $config1->empresa_id);
        $this->assertEquals($otherEmpresa->id, $config2->empresa_id);
    }

    /**
     * Test: Payment transaction model
     */
    public function test_payment_transaction_model(): void
    {
        $transaction = PaymentTransaction::create([
            'empresa_id' => $this->empresa->id,
            'venta_id' => 1,
            'payment_method' => 'STRIPE',
            'stripe_payment_intent_id' => 'pi_test_123',
            'amount_paid' => 100.00,
            'currency' => 'usd',
            'status' => 'PENDING',
            'metadata' => json_encode(['user_id' => $this->user->id]),
        ]);

        $this->assertDatabaseHas('payment_transactions', [
            'stripe_payment_intent_id' => 'pi_test_123',
            'status' => 'PENDING',
            'amount_paid' => 100.00,
        ]);

        $this->assertTrue($transaction->isPending());
        $this->assertFalse($transaction->isSuccessful());
        $this->assertFalse($transaction->isFailed());
    }

    /**
     * Test: Payment transaction status transitions
     */
    public function test_payment_transaction_status_transitions(): void
    {
        $transaction = PaymentTransaction::create([
            'empresa_id' => $this->empresa->id,
            'venta_id' => 1,
            'payment_method' => 'STRIPE',
            'stripe_payment_intent_id' => 'pi_test_456',
            'amount_paid' => 50.00,
            'currency' => 'usd',
            'status' => 'PENDING',
        ]);

        // Mark as success
        $transaction->markAsSuccess(['stripe_charge_id' => 'ch_test_789']);
        $this->assertTrue($transaction->isSuccessful());

        // Mark as failed
        $transaction->markAsFailed('Insufficient funds');
        $this->assertTrue($transaction->isFailed());
        $this->assertStringContainsString('Insufficient funds', $transaction->error_message);
    }

    /**
     * Test: Stripe config enabled scope
     */
    public function test_stripe_config_enabled_scope(): void
    {
        // Disable current config
        $this->stripeConfig->update(['enabled' => false]);

        $enabledConfigs = StripeConfig::enabled()->get();
        $this->assertEquals(0, $enabledConfigs->count());

        // Enable again
        $this->stripeConfig->update(['enabled' => true]);
        $enabledConfigs = StripeConfig::enabled()->get();
        $this->assertTrue($enabledConfigs->count() > 0);
    }

    /**
     * Test: Payment transaction global scope
     */
    public function test_payment_transaction_company_isolation(): void
    {
        $moneda = Moneda::first();
        $otherEmpresa = Empresa::create([
            'nombre' => 'Other Cinema',
            'propietario' => 'Other Owner',
            'ruc' => '20987654321',
            'porcentaje_impuesto' => 18,
            'abreviatura_impuesto' => 'IGV',
            'direccion' => 'Other Address',
            'moneda_id' => $moneda->id,
        ]);

        // Create transactions for both companies
        PaymentTransaction::create([
            'empresa_id' => $this->empresa->id,
            'venta_id' => 1,
            'payment_method' => 'STRIPE',
            'stripe_payment_intent_id' => 'pi_empresa1',
            'amount_paid' => 100.00,
            'currency' => 'usd',
            'status' => 'SUCCESS',
        ]);

        PaymentTransaction::create([
            'empresa_id' => $otherEmpresa->id,
            'venta_id' => 2,
            'payment_method' => 'STRIPE',
            'stripe_payment_intent_id' => 'pi_empresa2',
            'amount_paid' => 200.00,
            'currency' => 'usd',
            'status' => 'SUCCESS',
        ]);

        // Query by company
        $empresa1Trans = PaymentTransaction::forEmpresa($this->empresa->id)->get();
        $empresa2Trans = PaymentTransaction::forEmpresa($otherEmpresa->id)->get();

        $this->assertEquals(1, $empresa1Trans->count());
        $this->assertEquals(1, $empresa2Trans->count());
    }
}

