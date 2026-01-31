# Stripe Connect Implementation - PHASE 7

**Status:** Phase 7 Complete  
**Date:** 2026-01-31  
**Version:** 1.0  
**Last Updated:** 2026-01-31

---

## Table of Contents

1. [Overview](#overview)
2. [Architecture](#architecture)
3. [Payment Flow](#payment-flow)
4. [Onboarding Flow](#onboarding-flow)
5. [Implementation Guide](#implementation-guide)
6. [API Reference](#api-reference)
7. [Testing Guide](#testing-guide)
8. [Backward Compatibility](#backward-compatibility)
9. [Troubleshooting](#troubleshooting)
10. [Security Considerations](#security-considerations)

---

## Overview

### What is Stripe Connect?

Stripe Connect enables CinemaPOS to split payments automatically between:
- **Platform (CinemaPOS):** Retains service fee
- **Merchant (Cinema):** Receives remaining payment directly to their bank account

### Key Benefits

✅ **Automatic Payment Splitting** - No manual payouts needed  
✅ **Direct Deposits** - Merchants receive payments in their own accounts  
✅ **Compliance** - Cleaner accounting and tax reporting  
✅ **Scalability** - Support unlimited merchants with individual onboarding  
✅ **Backward Compatible** - Works with existing payment system

### When to Use Connect

Use Stripe Connect when:
- Each merchant needs direct access to their earnings
- Platform wants to deduct service fees automatically
- Merchants require real-time access to funds (Express account)
- Multi-tenant payment processing is needed

---

## Architecture

### Data Model

#### Empresa (Companies) - New Fields

```sql
-- Stripe Connect Configuration
stripe_account_id         VARCHAR(255) UNIQUE        -- Connected Account ID (acct_xxx)
stripe_connect_status     VARCHAR(50) DEFAULT 'NOT_STARTED'  -- Onboarding status
stripe_onboarding_url     TEXT NULLABLE              -- Link for merchant to complete setup
stripe_connect_updated_at TIMESTAMP NULLABLE         -- Last status check
```

#### Payment Transaction - New Metadata

```php
'metadata' => [
    'connect_enabled' => bool,              // Is Connect active for this payment?
    'application_fee_amount' => int,        // Fee retained by platform (cents)
    'stripe_account_id' => string|null,     // Merchant's Stripe account (for auditing)
]
```

### Service Architecture

```
┌─────────────────────────────────────────────────┐
│         CinemaPOS Application                   │
│  (PaymentController, VentaService, etc)         │
└──────────────────┬──────────────────────────────┘
                   │
                   ▼
        ┌──────────────────────┐
        │ StripeConnectService │
        ├──────────────────────┤
        │ • Onboarding         │
        │ • Split Payments     │
        │ • Account Management │
        └──────────────────────┘
                   │
                   ▼
        ┌──────────────────────┐
        │  StripePaymentService│
        ├──────────────────────┤
        │ • Basic Payments     │
        │ • Direct Mode        │
        └──────────────────────┘
                   │
                   ▼
        ┌──────────────────────┐
        │  Stripe API (SDK)    │
        │                      │
        │  PaymentIntents      │
        │  AccountLinks        │
        │  Webhooks            │
        └──────────────────────┘
```

---

## Payment Flow

### Standard Flow (No Connect)

**Scenario:** Empresa without Stripe Connect enabled

```
User Sale Creation
    │
    ▼
PaymentIntent Created
├─ amount: total
├─ currency: USD
└─ (NO application_fee_amount)
    │
    ▼
Platform Receives 100% of Payment
    │
    ▼
Manual Payout to Merchant (if applicable)
```

**Code Path:**
```php
// In StripePaymentService::createPaymentIntent()
// Simple PaymentIntent without Connect
```

### Split Payment Flow (With Connect)

**Scenario:** Empresa with Stripe Connect enabled and onboarded

```
User Sale Creation (Venta with tarifa_servicio = 5%)
    │
    ▼
Check: Is empresa.stripe_account_id present?
    │
    ├─ NO  → Use Standard Flow (above)
    │
    └─ YES → Check: Is stripe_connect_status = 'ACTIVE'?
             │
             ├─ NO  → Use Standard Flow
             │
             └─ YES → Calculate Split:
                      • Total: $100.00
                      • Fee (5%): $5.00
                      • Merchant: $95.00
                         │
                         ▼
                    PaymentIntent Created
                    ├─ amount: 10000 (cents)
                    ├─ application_fee_amount: 500 (cents)
                    └─ transfer_data.destination: acct_xxxx
                         │
                         ▼
                    Customer Pays $100
                         │
                         ▼
                    Stripe Automatically:
                    ├─ Retains $5 in Platform Account
                    └─ Transfers $95 to Merchant Account
                         │
                         ▼
                    payment_transaction.metadata tracks:
                    ├─ connect_enabled: true
                    └─ application_fee_amount: 500
```

**Code Path:**
```php
// In StripeConnectService::createPaymentIntentWithSplit()

if ($this->empresa->stripe_account_id &&
    $this->empresa->stripe_connect_status === 'ACTIVE') {
    
    $applicationFeeAmount = calculateFee($venta); // Cents
    
    $paymentIntent = $stripe->paymentIntents->create([
        'amount' => $totalCents,
        'application_fee_amount' => $applicationFeeAmount,
        'transfer_data' => [
            'destination' => $empresa->stripe_account_id
        ]
    ]);
}
```

---

## Onboarding Flow

### Express Account Onboarding

CinemaPOS uses Stripe Express Accounts for simplified onboarding.

```
1. Admin Initiates Connect Onboarding
   └─ Click "Enable Stripe Connect" in Settings
   
2. StripeConnectService::generateOnboardingUrl()
   ├─ Create Connected Account (Express)
   │  └─ stripe.accounts.create(type='express')
   │     └─ Returns: acct_xxxx...
   │
   ├─ Generate AccountLink
   │  └─ stripe.accountLinks.create(account=acct_xxxx)
   │     └─ Returns: onboarding URL
   │
   └─ Save to DB
      └─ Empresa.stripe_account_id = acct_xxxx
      └─ Empresa.stripe_onboarding_url = https://...
      └─ Empresa.stripe_connect_status = PENDING

3. User Redirected to Stripe Onboarding Page
   └─ Completes KYC:
      ├─ Legal Entity Name
      ├─ Owner Information
      ├─ Business Address
      └─ Bank Account Details

4. Webhook: account.updated
   └─ Check: charges_enabled && payouts_enabled?
      ├─ YES → stripe_connect_status = ACTIVE
      ├─ NO  → Check requirements
      │        ├─ currently_due → PENDING
      │        └─ eventually_due → UNDER_REVIEW

5. User Redirected to Success Page
   └─ CinemaPOS verifies onboarding status
```

### Status States

```
NOT_STARTED  ──────────┐
                       ├─→ PENDING ──────────┐
ACTIVE       ◄─────────┤                     ├─→ ACTIVE (Ready for Payments)
UNDER_REVIEW │         │                     │
             │         └─→ REJECTED (Manual Review Needed)
             │
             └─ User initiates onboarding
```

---

## Implementation Guide

### 1. Run Migration

```bash
php artisan migrate
```

This adds columns to `empresa` table:
- `stripe_account_id`
- `stripe_connect_status`
- `stripe_onboarding_url`
- `stripe_connect_updated_at`

### 2. Create Connected Account

```php
use App\Services\StripeConnectService;

// Initialize service
$connectService = new StripeConnectService($empresa);

// Create Connected Account
$account = $connectService->createConnectedAccount();
// Returns: Stripe\Account object
// Saves: empresa.stripe_account_id
```

**What Happens:**
- Creates Express Account in Stripe
- Stores `acct_xxxx...` in `empresa.stripe_account_id`
- Sets status to `PENDING`

### 3. Generate Onboarding URL

```php
// Generate URL for merchant to complete onboarding
$onboardingUrl = $connectService->generateOnboardingUrl(
    successUrl: route('stripe.onboarding.success'),
    refreshUrl: route('stripe.onboarding.refresh')
);

// Redirect user to Stripe
return redirect($onboardingUrl);
```

**Redirect User To:**
- Stripe completes KYC
- User enters bank account
- Returns to success URL

### 4. Check Onboarding Status

```php
// In webhook or periodic job
$status = $connectService->checkOnboardingStatus();

// Returns one of:
// - NOT_STARTED
// - PENDING (waiting for merchant action)
// - ACTIVE (ready for payments!)
// - UNDER_REVIEW (submitted, awaiting Stripe review)
// - REJECTED (needs manual review)

if ($status === 'ACTIVE') {
    // Safe to process payments with Connect
    $connectService->isReadyForConnect(); // Returns true
}
```

### 5. Process Payment with Split

```php
// When creating payment for a sale
$connectService = new StripeConnectService($empresa);

// This automatically handles:
// - Detecting if Connect is active
// - Calculating platform fee
// - Creating PaymentIntent with split
$transaction = $connectService->createPaymentIntentWithSplit($venta);

// If Connect is NOT active, works like standard payment
// If Connect IS active, automatically splits the payment
```

**Example with Fee Calculation:**

```php
// Sale: $100
// Tarifa_servicio: 5%

$venta = Venta::create([
    'total' => 100.00,
    'tarifa_servicio' => 5,  // Platform fee percentage
]);

$transaction = $connectService->createPaymentIntentWithSplit($venta);

// Automatically creates PaymentIntent with:
// - Total: 10000 cents ($100)
// - Application Fee: 500 cents ($5)
// - Transfer Destination: acct_xxxx (merchant's account)
// - Merchant Receives: 9500 cents ($95)
```

### 6. Handle Webhooks

Stripe will send webhooks for:

```php
// In WebhookController
public function handleStripeWebhook(Request $request)
{
    $payload = $request->getContent();
    $sig_header = $request->header('Stripe-Signature');
    
    // In StripePaymentService::handleWebhook()
    $paymentService->handleWebhook($payload, $sig_header);
    
    // Processes:
    // - payment_intent.succeeded
    // - payment_intent.payment_failed
    // - payment_intent.canceled
    // - account.updated (Connect status changes)
}
```

---

## API Reference

### StripeConnectService

#### Constructor

```php
public function __construct(Empresa $empresa = null)
```

Uses authenticated user's empresa if not provided.

#### createConnectedAccount()

```php
public function createConnectedAccount(string $type = 'standard'): Account
```

Creates Express Account. Returns Stripe Account object.

**Stores:**
- `empresa.stripe_account_id` = `acct_xxxx...`
- `empresa.stripe_connect_status` = `PENDING`

#### generateOnboardingUrl()

```php
public function generateOnboardingUrl(
    string $successUrl = null,
    string $refreshUrl = null
): string
```

Generates link for merchant to complete KYC onboarding.

**Parameters:**
- `$successUrl` - Redirect after completion
- `$refreshUrl` - Redirect if user exits early

**Returns:** Stripe AccountLink URL

#### checkOnboardingStatus()

```php
public function checkOnboardingStatus(): string
```

Queries Stripe for current account status.

**Returns:**
- `NOT_STARTED`
- `PENDING`
- `ACTIVE` (Ready for payments!)
- `UNDER_REVIEW`
- `REJECTED`

#### createPaymentIntentWithSplit()

```php
public function createPaymentIntentWithSplit(
    Venta $venta,
    array $metadata = []
): PaymentTransaction
```

Creates PaymentIntent with automatic split if Connect is active.

**Logic:**
```
IF empresa.stripe_account_id EXISTS
   AND empresa.stripe_connect_status === 'ACTIVE'
THEN
   Calculate application_fee_amount from venta.tarifa_servicio
   Add transfer_data with destination = stripe_account_id
ELSE
   Create standard PaymentIntent (backward compatible)
```

**Returns:** PaymentTransaction with metadata including:
- `connect_enabled` - Was Connect used?
- `application_fee_amount` - Platform fee in cents

#### getConnectedAccountInfo()

```php
public function getConnectedAccountInfo(): ?Account
```

Returns Stripe Account object with details:
- `charges_enabled` - Can accept charges?
- `payouts_enabled` - Can receive payouts?
- `requirements.currently_due` - Missing fields
- `requirements.eventually_due` - Under review fields

#### isReadyForConnect()

```php
public function isReadyForConnect(): bool
```

Quick check if company is fully onboarded and ready for Connect.

**Checks:**
- Has `stripe_account_id`
- Status is `ACTIVE`
- `charges_enabled === true`
- `payouts_enabled === true`

#### disconnectAccount()

```php
public function disconnectAccount(): bool
```

Removes Connect for this empresa (existing payments still valid).

---

## Testing Guide

### Unit Tests

```php
// tests/Feature/StripeConnectServiceTest.php

public function test_can_create_connected_account()
{
    $empresa = Empresa::factory()->create();
    $service = new StripeConnectService($empresa);
    
    $account = $service->createConnectedAccount();
    
    $this->assertNotNull($account->id);
    $this->assertTrue(str_starts_with($account->id, 'acct_'));
    $this->assertEquals($empresa->fresh()->stripe_account_id, $account->id);
}

public function test_can_generate_onboarding_url()
{
    $empresa = Empresa::factory()->create();
    $service = new StripeConnectService($empresa);
    
    $url = $service->generateOnboardingUrl();
    
    $this->assertStringContainsString('stripe.com', $url);
}

public function test_payment_with_connect_applies_fee()
{
    $empresa = Empresa::factory()->create([
        'stripe_account_id' => 'acct_test123',
        'stripe_connect_status' => 'ACTIVE'
    ]);
    
    $venta = Venta::factory()->create([
        'empresa_id' => $empresa->id,
        'total' => 100.00,
        'tarifa_servicio' => 5
    ]);
    
    $service = new StripeConnectService($empresa);
    $transaction = $service->createPaymentIntentWithSplit($venta);
    
    // Verify fee was calculated
    $this->assertEquals(500, $transaction->metadata['application_fee_amount']);
    $this->assertTrue($transaction->metadata['connect_enabled']);
}

public function test_backward_compatible_without_connect()
{
    // Empresa without Connect should use standard flow
    $empresa = Empresa::factory()->create();
    
    $venta = Venta::factory()->create([
        'empresa_id' => $empresa->id,
        'total' => 100.00
    ]);
    
    $service = new StripeConnectService($empresa);
    $transaction = $service->createPaymentIntentWithSplit($venta);
    
    // Verify standard payment created
    $this->assertFalse($transaction->metadata['connect_enabled']);
    $this->assertNull($transaction->metadata['application_fee_amount']);
}
```

### Integration Tests

```bash
# Test with Stripe Test Keys
php artisan test tests/Feature/StripeConnectServiceTest.php

# Test with Stripe Webhooks
php artisan test tests/Feature/StripeWebhookTest.php
```

### Manual Testing

```bash
# 1. Create test empresa
php artisan tinker
>>> $empresa = \App\Models\Empresa::factory()->create();
>>> $config = \App\Models\StripeConfig::factory()->create(['empresa_id' => $empresa->id]);

# 2. Create Connected Account
>>> $service = new \App\Services\StripeConnectService($empresa);
>>> $account = $service->createConnectedAccount();
>>> $empresa->fresh();  // View stripe_account_id

# 3. Generate Onboarding URL
>>> $url = $service->generateOnboardingUrl();
>>> echo $url;  // Visit this URL to onboard

# 4. Check Status (after completing onboarding)
>>> $status = $service->checkOnboardingStatus();
>>> echo $status;  // Should be 'ACTIVE'

# 5. Create Payment with Split
>>> $venta = \App\Models\Venta::factory()->create(['empresa_id' => $empresa->id]);
>>> $txn = $service->createPaymentIntentWithSplit($venta);
>>> $txn->metadata;  // Verify connect_enabled and fee
```

### Test Card Numbers

```
Successful Payment:  4242 4242 4242 4242
Declined:           4000 0000 0000 0002
3D Secure Required: 4000 0025 0000 3155
```

---

## Backward Compatibility

### Phase 5 & 6 Compatibility ✅

**No breaking changes:**
- Existing `StripePaymentService` unchanged
- All existing sales work as before
- No database schema conflicts
- New fields are nullable and optional

### Migration Path

```
Before (Phase 5-6):
├─ StripeConfig per empresa
├─ PaymentIntent without Connect
└─ 100% of payment to platform

After (Phase 7):
├─ StripeConfig per empresa (unchanged)
├─ Optional: stripe_account_id per empresa
├─ PaymentIntent WITH or WITHOUT Connect
└─ IF Connect active → automatic split
   ELSE → backward compatible (100% to platform)
```

### Code Compatibility

```php
// Phase 5-6: Still works
$paymentService = new StripePaymentService($config);
$transaction = $paymentService->createPaymentIntent($venta);

// Phase 7: New capability
$connectService = new StripeConnectService($empresa);
if ($connectService->isReadyForConnect()) {
    $transaction = $connectService->createPaymentIntentWithSplit($venta);
} else {
    // Falls back to standard flow
    $transaction = $connectService->createPaymentIntentWithSplit($venta);
}
```

---

## Troubleshooting

### Issue: "No stripe_account_id found"

**Cause:** Empresa hasn't been onboarded to Connect

**Solution:**
```php
if (!$empresa->stripe_account_id) {
    $connectService->createConnectedAccount();
    // Now user can visit onboarding URL
}
```

### Issue: Payment fails with "Invalid stripe_account_id"

**Cause:** Account ID format incorrect or account deleted in Stripe

**Solution:**
```php
// Verify account exists
$account = $connectService->getConnectedAccountInfo();
if (!$account) {
    // Recreate account
    $connectService->disconnectAccount();
    $connectService->createConnectedAccount();
}
```

### Issue: Status stays "PENDING" for hours

**Cause:** Merchant hasn't completed KYC onboarding

**Solution:**
```php
// Check what's missing
$account = $connectService->getConnectedAccountInfo();
echo $account->requirements->currently_due;  // Missing fields

// Send reminder to merchant
// Provide onboarding URL again
$url = $connectService->generateOnboardingUrl();
```

### Issue: "Transfer Destination required" error

**Cause:** Trying to use Connect but status isn't ACTIVE

**Solution:**
```php
$status = $connectService->checkOnboardingStatus();
// Only use Connect if status is 'ACTIVE'
if ($status !== 'ACTIVE') {
    // Fall back to standard payment
    // System automatically does this!
}
```

### Issue: Fee calculation incorrect

**Cause:** `tarifa_servicio` not set on Venta

**Solution:**
```php
// Verify tarifa_servicio is set
$venta = Venta::create([
    'total' => 100.00,
    'tarifa_servicio' => 5,  // This must be set!
]);

// Check calculation
$fee = $venta->getMontaTarifa();
echo "Fee: " . $fee;  // Should be 5.00
```

---

## Security Considerations

### 1. API Keys

```php
// ✅ Keys are encrypted in database
$config = StripeConfig::where('empresa_id', $id)->first();
$secretKey = $config->getSecretKey();  // Auto-decrypted

// ❌ Never log full keys
Log::info('Key: ' . substr($key, 0, 8) . '...');
```

### 2. Connected Account IDs

```php
// ✅ Verify ownership before operations
if ($venta->empresa_id !== auth()->user()->empresa_id) {
    throw new Exception('Unauthorized');
}

// ❌ Don't process other empresa's payments
```

### 3. Webhook Verification

```php
// ✅ Always verify webhook signature
$event = \Stripe\Webhook::constructEvent(
    $payload,
    $sig_header,
    $webhook_secret
);

// ❌ Never trust incoming data without verification
```

### 4. Fee Calculation

```php
// ✅ Calculate server-side from database
$fee = $venta->getMontaTarifa();  // Server-side calculation

// ❌ Never trust client-side fee amounts
```

### 5. Account Status Verification

```php
// ✅ Always verify before using Connect
if ($conectService->isReadyForConnect()) {
    // Safe to use Connect
}

// ❌ Don't assume status based on DB alone
// Always query Stripe for current status
```

---

## Best Practices

### 1. Always Check Status Before Using Connect

```php
$status = $connectService->checkOnboardingStatus();
if ($status !== 'ACTIVE') {
    // Use standard payment flow
    return $paymentService->createPaymentIntent($venta);
}
```

### 2. Handle Graceful Fallback

```php
try {
    $transaction = $connectService->createPaymentIntentWithSplit($venta);
} catch (Exception $e) {
    // Fall back to standard payment
    Log::warning('Connect failed, using standard: ' . $e->getMessage());
    $transaction = $paymentService->createPaymentIntent($venta);
}
```

### 3. Monitor Webhook Status

```php
// In account.updated webhook
'account.updated' => function ($event) {
    $account = $event->data->object;
    
    // Update our DB
    $empresa = Empresa::where('stripe_account_id', $account->id)->first();
    if ($empresa) {
        $connectService->checkOnboardingStatus();
        // Automatically updates status in DB
    }
}
```

### 4. Periodic Status Refresh

```php
// In a scheduled job
$schedule->call(function () {
    foreach (Empresa::whereNotNull('stripe_account_id')->get() as $empresa) {
        $connectService = new StripeConnectService($empresa);
        $connectService->checkOnboardingStatus();
    }
})->daily();
```

### 5. Clear Onboarding URL After Completion

```php
// After successful onboarding
if ($status === 'ACTIVE') {
    $empresa->update([
        'stripe_onboarding_url' => null,  // Clear old URL
    ]);
}
```

---

## Reference Implementation

### Complete Flow Example

```php
// In PaymentController

public function initiateStripeConnectOnboarding()
{
    $empresa = auth()->user()->empresa;
    $connectService = new StripeConnectService($empresa);
    
    try {
        // Step 1: Create Connected Account (if needed)
        if (!$empresa->stripe_account_id) {
            $connectService->createConnectedAccount();
        }
        
        // Step 2: Generate Onboarding URL
        $url = $connectService->generateOnboardingUrl();
        
        // Step 3: Redirect user
        return redirect($url);
    } catch (Exception $e) {
        return back()->with('error', 'Failed to initiate Connect: ' . $e->getMessage());
    }
}

public function handleOnboardingCallback()
{
    $empresa = auth()->user()->empresa;
    $connectService = new StripeConnectService($empresa);
    
    // Check current status
    $status = $connectService->checkOnboardingStatus();
    
    if ($status === 'ACTIVE') {
        return redirect('/settings/payments')
            ->with('success', 'Stripe Connect activated! Ready to receive payments.');
    }
    
    return redirect('/settings/payments')
        ->with('info', 'Onboarding in progress. Status: ' . $status);
}

public function processSalePayment(Venta $venta)
{
    $empresa = $venta->empresa;
    $connectService = new StripeConnectService($empresa);
    
    // Automatically uses Connect if active, otherwise standard
    $transaction = $connectService->createPaymentIntentWithSplit($venta);
    
    // Log what was used
    if ($transaction->metadata['connect_enabled']) {
        Log::info('Payment split with Connect', [
            'venta_id' => $venta->id,
            'fee' => $transaction->metadata['application_fee_amount'] / 100,
        ]);
    } else {
        Log::info('Standard payment (no Connect)', [
            'venta_id' => $venta->id,
        ]);
    }
    
    return $transaction;
}
```

---

## Phase 7 Checklist

- [x] Migration created (`add_stripe_connect_to_empresas_table`)
- [x] StripeConnectService implemented
- [x] Onboarding flow documented
- [x] Payment split logic implemented
- [x] Backward compatibility maintained
- [x] Error handling and fallbacks
- [x] Webhook support planned
- [x] Testing guide provided
- [x] Security guidelines documented
- [x] Reference implementation provided

---

## Future Enhancements

Possible additions for Phase 8+:

1. **Custom Account Type Support**
   - Standard accounts for larger merchants
   - Express accounts for quick onboarding

2. **Payout Schedule Customization**
   - Daily, weekly, monthly payouts
   - Threshold-based payouts

3. **Merchant Dashboard**
   - Real-time earnings view
   - Payout history
   - Connected account management

4. **Advanced Fee Models**
   - Tiered fees based on volume
   - Different fees for different payment methods
   - Promotional periods with reduced fees

5. **Reconciliation Automation**
   - Automatic matching of Stripe transfers to entries
   - Payout status tracking
   - Disputed transaction handling

---

## Support & Resources

**Stripe Documentation:**
- https://stripe.com/docs/connect
- https://stripe.com/docs/connect/express-accounts
- https://stripe.com/docs/api/payment_intents

**CinemaPOS Documentation:**
- [Phase 5: Stripe PaymentIntent](./STRIPE_IMPLEMENTATION.md)
- [Phase 6: Demo Ready](./PHASE_6_DEMO_READY.md)

**Questions?**
Contact: payments@cinepox.local

---

**Document Version:** 1.0  
**Last Updated:** 2026-01-31  
**Reviewed By:** Senior Payments Engineer  
**Status:** Ready for Production
