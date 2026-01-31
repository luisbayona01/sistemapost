# Stripe Connect - Quick Integration Guide

**For:** Developers implementing Phase 7  
**Time:** 15 minutes  
**Difficulty:** ⭐⭐ Intermediate  

---

## TL;DR

Add payment splitting in 3 lines:

```php
use App\Services\StripeConnectService;

$connectService = new StripeConnectService($empresa);
$transaction = $connectService->createPaymentIntentWithSplit($venta);
// Done! Automatically handles Connect or falls back to standard
```

---

## Quick Setup

### 1. Run Migration

```bash
php artisan migrate
```

This adds Connect fields to `empresa` table.

### 2. In Your Controller

```php
use App\Services\StripeConnectService;

class PaymentController extends Controller
{
    public function processPayment(Venta $venta)
    {
        // Initialize service
        $connectService = new StripeConnectService($venta->empresa);
        
        // Create payment (uses Connect if available)
        $transaction = $connectService->createPaymentIntentWithSplit($venta);
        
        // Get client secret for frontend
        return response()->json([
            'client_secret' => $transaction->metadata['stripe_client_secret'],
            'amount' => $venta->total,
        ]);
    }
}
```

### 3. Enable Connect (Optional)

```php
public function enableStripeConnect()
{
    $empresa = auth()->user()->empresa;
    $connectService = new StripeConnectService($empresa);
    
    // Generate onboarding URL
    $url = $connectService->generateOnboardingUrl();
    
    // Redirect user to Stripe
    return redirect($url);
}
```

---

## How to Know if Connect is Active

```php
// Quick check
if ($empresa->hasStripeConnect()) {
    echo "Connect is active!";
}

// Detailed status
$status = $empresa->getStripeConnectStatus();
// Returns: NOT_STARTED|PENDING|ACTIVE|REJECTED|UNDER_REVIEW

// Check readiness
$connectService = new StripeConnectService($empresa);
if ($connectService->isReadyForConnect()) {
    echo "Ready for Connect payments!";
}
```

---

## Payment Behavior

### Automatic Logic

Your code:
```php
$transaction = $connectService->createPaymentIntentWithSplit($venta);
```

What happens:

```
IF empresa.stripe_account_id EXISTS AND status = ACTIVE
├─ Calculate fee from venta.tarifa_servicio
├─ Create PaymentIntent with:
│  ├─ amount: $100
│  ├─ application_fee_amount: $5
│  └─ transfer_data.destination: merchant's account
└─ Result: Platform +$5, Merchant +$95

ELSE
├─ Create standard PaymentIntent
└─ Result: Platform +$100 (backward compatible)
```

No code changes needed!

---

## Database Fields

After migration, `empresa` has:

```php
$empresa->stripe_account_id           // 'acct_123...' or null
$empresa->stripe_connect_status       // 'ACTIVE'|'PENDING'|etc
$empresa->stripe_onboarding_url       // 'https://...' or null
$empresa->stripe_connect_updated_at   // Carbon timestamp
```

Use these helpers:

```php
$empresa->hasStripeConnect()       // true/false
$empresa->getStripeConnectStatus() // Current status
$empresa->isStripeConnectPending() // Waiting for merchant?
$empresa->isStripeConnectRejected() // Needs review?
```

---

## Fee Calculation

Fees are calculated from `Venta.tarifa_servicio`:

```php
$venta = Venta::create([
    'total' => 100.00,
    'tarifa_servicio' => 5,  // 5% fee
]);

// When payment is processed:
// Platform fee = 100.00 * 5% = $5.00 = 500 cents
// Merchant gets = 100.00 - 5.00 = $95.00 = 9500 cents
```

The service automatically calculates using `$venta->getMontaTarifa()`.

---

## Error Handling

```php
use App\Services\StripeConnectService;

try {
    $connectService = new StripeConnectService($empresa);
    $transaction = $connectService->createPaymentIntentWithSplit($venta);
} catch (Exception $e) {
    // Payment creation failed
    // Handled gracefully - should fall back to standard
    
    Log::error('Payment failed: ' . $e->getMessage());
    
    // Check if Connect was the issue
    if (!$connectService->isReadyForConnect()) {
        // Status is not ACTIVE - already falls back automatically
    }
}
```

---

## Testing

### Manual Test with Demo Seeder

```bash
# Seed demo data
php artisan migrate:fresh
php artisan db:seed --class=DemoSeeder

# Test in tinker
php artisan tinker

>>> $empresa = \App\Models\Empresa::first();
>>> $connectService = new \App\Services\StripeConnectService($empresa);
>>> $connectService->isReadyForConnect()  // Should be false (no Connect set up yet)

# Simulate Connect being set up
>>> $empresa->update([
    'stripe_account_id' => 'acct_test1234567890',
    'stripe_connect_status' => 'ACTIVE'
]);

# Now test payment creation
>>> $venta = \App\Models\Venta::factory()->create(['empresa_id' => $empresa->id]);
>>> $txn = $connectService->createPaymentIntentWithSplit($venta);
>>> $txn->metadata // Check if Connect is enabled
```

### Run Unit Tests

```bash
# Run Connect tests
php artisan test tests/Feature/StripeConnectServiceTest.php

# Tests are marked @skip - they require Stripe test keys
# To run: Set STRIPE_SECRET_KEY env var with test key
# STRIPE_SECRET_KEY=sk_test_xxxx php artisan test tests/Feature/StripeConnectServiceTest.php
```

---

## Common Questions

### Q: Will existing payments break?

**A:** No. Connect is opt-in. Payments work with or without it.

### Q: Do I need to change controller code?

**A:** No. Just replace `StripePaymentService` with `StripeConnectService` and use `createPaymentIntentWithSplit()`. Rest is automatic.

### Q: How does merchant get paid?

**A:** Stripe automatically transfers the balance to their connected bank account (based on payout schedule).

### Q: What if merchant hasn't completed onboarding?

**A:** Payment falls back to standard mode. Platform gets 100%. No split.

### Q: Can I check if merchant is ready?

**A:** Yes:
```php
if ($connectService->isReadyForConnect()) {
    // Use Connect
}
```

### Q: Where's the documentation?

**A:** See `STRIPE_CONNECT.md` (600+ lines with complete reference)

### Q: What about testing?

**A:** See `tests/Feature/StripeConnectServiceTest.php` for examples and patterns.

---

## Before Going Live

Checklist:

- [ ] Run migration
- [ ] Update payment controller to use `createPaymentIntentWithSplit()`
- [ ] Test with Stripe test keys
- [ ] Verify payment splits in Stripe dashboard
- [ ] Set up webhook for `account.updated` events
- [ ] Configure webhook signing secret
- [ ] Test onboarding flow end-to-end
- [ ] Test fallback (payment without Connect)
- [ ] Verify fee calculations
- [ ] Set up error monitoring

---

## Production Deployment

### Steps

1. **Backup database**
   ```bash
   mysqldump -u user -p database > backup_2026_01_31.sql
   ```

2. **Deploy code**
   ```bash
   git pull origin main
   ```

3. **Run migration**
   ```bash
   php artisan migrate --force
   ```

4. **Clear cache**
   ```bash
   php artisan cache:clear
   php artisan config:clear
   ```

5. **Verify**
   ```bash
   php artisan tinker
   >>> \App\Models\Empresa::first()->stripe_account_id  // Should be null
   ```

6. **Monitor**
   - Watch error logs
   - Monitor payment success rate
   - Check Stripe dashboard for disputes

---

## Help & Support

**Documentation:**
- `STRIPE_CONNECT.md` - Full 600+ line reference
- `PHASE_7_COMPLETE.md` - Implementation summary
- `StripeConnectService.php` - Inline code comments

**Questions:**
- Check [Stripe Connect Docs](https://stripe.com/docs/connect)
- Review test file: `tests/Feature/StripeConnectServiceTest.php`
- Check example implementation in `STRIPE_CONNECT.md`

**Issues:**
- Check logs: `storage/logs/laravel.log`
- Verify Stripe keys in `.env`
- Confirm migration ran: `php artisan migrate:status`

---

## Next Steps

1. ✅ Read this Quick Guide (you're here!)
2. ⏭️ Run migration
3. ⏭️ Update payment controller
4. ⏭️ Test with Stripe test account
5. ⏭️ Read full `STRIPE_CONNECT.md` for details
6. ⏭️ Deploy to production

---

**Ready?** Let's split some payments! 💳✂️

Time to implementation: ~15 minutes  
Time to production: ~1 hour (with testing)

---

Last Updated: 2026-01-31  
Phase: 7 (Production Ready)
