# Stripe Elements Frontend Testing Guide

This guide explains how to test the Stripe Elements payment integration in the POS system.

## Overview

The payment flow has been fully integrated into the venta (sale) detail view. When a sale has a `PENDIENTE` (pending) payment status, users can click "Pagar con Stripe" to process the payment.

## Prerequisites

- Laravel development server running (`php artisan serve`)
- Stripe account with test keys configured in the database
- Demo data seeded (Cinema Fénix empresa with test Stripe keys)
- Browser developer tools (optional, for debugging)

## System Architecture

### Frontend Flow
1. User views a pending venta (sale) in `/admin/ventas/{id}`
2. User clicks "Pagar con Stripe" button
3. JavaScript calls `initializePaymentFlow(ventaId)`
4. Frontend fetches configuration from `GET /admin/ventas/{venta}/pago/config`
5. Frontend fetches PaymentIntent from `POST /admin/ventas/{venta}/pago/iniciar`
6. Stripe modal opens with Card Element
7. User enters card details
8. On form submit, `stripe.confirmCardPayment()` is called
9. Webhook handler processes `payment_intent.succeeded` event
10. Venta status updates to PAGADA
11. Movimiento (transaction) is created automatically

### Backend Endpoints

| Method | Endpoint | Purpose |
|--------|----------|---------|
| GET | `/admin/ventas/{venta}/pago/config` | Get Stripe public key and payment config |
| POST | `/admin/ventas/{venta}/pago/iniciar` | Create PaymentIntent |
| GET | `/admin/ventas/{venta}/pago/estado` | Check payment status |
| POST | `/webhooks/stripe` | Webhook handler (no auth) |

### Key Files

**Frontend:**
- `resources/views/venta/show.blade.php` - Sale detail view with payment button
- `resources/views/components/stripe-payment-modal.blade.php` - Payment modal component
- `resources/js/stripe-payment.js` - Stripe.js handler (Card Element + payment logic)

**Backend:**
- `app/Http/Controllers/ventaController.php` - Payment endpoints
- `app/Services/StripePaymentService.php` - Payment logic
- `app/Http/Controllers/StripeWebhookController.php` - Webhook handler
- `app/Models/StripeConfig.php` - Stripe keys storage
- `app/Models/PaymentTransaction.php` - Payment audit trail

## Testing Steps

### 1. Setup Demo Data

```bash
php artisan migrate
php artisan db:seed --class=DemoSeeder
```

This creates:
- Cinema Fénix empresa
- Admin user: admin@cinefenix.local / password123
- 3 sample products
- 3 sample customers
- Stripe test keys (encrypted in database)

### 2. Create a Pending Sale

1. Login to the system: http://localhost:8000
   - Email: admin@cinefenix.local
   - Password: password123

2. Navigate to Ventas (Sales)

3. Create a new sale:
   - Click "Crear venta" (Create Sale)
   - Select Cliente (Customer)
   - Add at least 1 Producto (Product)
   - Select Método de Pago: Stripe
   - Click "Guardar" (Save)

4. You should see the sale with status PENDIENTE

### 3. Test Payment Modal

1. Go to the sale you just created (click "Ver" in the list)

2. You should see:
   - Payment status badge showing "PENDIENTE"
   - Blue alert box with "Pagar con Stripe" button

3. Click "Pagar con Stripe"

4. The payment modal should appear with:
   - Amount display (in USD)
   - Card Element (white input field)
   - Buttons: Cancel and "Pagar Ahora"
   - Test card information

### 4. Test Payment with Test Card

**Successful Payment:**
```
Card Number: 4242 4242 4242 4242
Expiry: Any future date (e.g., 12/25)
CVC: Any 3 digits (e.g., 123)
```

Steps:
1. Enter the test card details
2. Click "Pagar Ahora"
3. You should see:
   - Loading spinner (3 seconds)
   - Success message: "¡Pago completado exitosamente!"
   - Page refreshes automatically
4. Sale status should now be PAGADA (green badge)

**Declined Payment (Test):**
```
Card Number: 4000 0000 0000 0002
Expiry: Any future date
CVC: Any 3 digits
```

Steps:
1. Open modal again
2. Enter declined card details
3. Click "Pagar Ahora"
4. You should see error message: "Your card was declined"
5. Modal stays open for retry

**Requires 3D Secure (Test):**
```
Card Number: 4000 0025 0000 3155
Expiry: Any future date
CVC: Any 3 digits
```

Steps:
1. Open modal again
2. Enter this card number
3. Click "Pagar Ahora"
4. You will be redirected to Stripe's authentication screen
5. Complete the authentication
6. Payment should succeed

### 5. Test Webhook (Optional - requires stripe-cli)

To test webhook handling locally:

```bash
# Terminal 1: Run Laravel server
php artisan serve

# Terminal 2: Listen for Stripe webhooks
stripe listen --forward-to http://localhost:8000/webhooks/stripe

# Terminal 3: Trigger test events
stripe trigger payment_intent.succeeded
```

Expected behavior:
- Webhook received at http://localhost:8000/webhooks/stripe
- Payment status updated in database
- Venta estado_pago changes to PAGADA
- Movimiento (transaction) created

### 6. Verify in Database

After successful payment:

```bash
# Check payment transaction
sqlite3 storage/database.sqlite
SELECT * FROM payment_transactions WHERE venta_id = 1;

# Check venta status
SELECT id, estado_pago, total FROM ventas WHERE id = 1;

# Check movimiento created
SELECT * FROM movimientos WHERE venta_id = 1;
```

## Troubleshooting

### Modal doesn't appear

**Problem:** Button click doesn't open payment modal

**Debug:**
1. Check browser console (F12) for JavaScript errors
2. Check Network tab - ensure `/admin/ventas/{venta}/pago/config` request succeeds
3. Verify Stripe.js library is loaded (check <script src="https://js.stripe.com/v3/"></script>)

**Solution:**
- Ensure Stripe keys are configured in database (StripeConfig)
- Run `php artisan db:seed --class=DemoSeeder` again
- Clear browser cache and refresh

### Card Element not rendering

**Problem:** Black box appears instead of card input

**Debug:**
1. Check browser console for Stripe.js errors
2. Verify public key is valid
3. Check that stripe-payment.js loaded correctly

**Solution:**
- Ensure Vite is running: `npm run dev`
- Rebuild assets: `npm run build`
- Hard refresh browser (Ctrl+Shift+R)

### Payment fails with "Configuration incomplete" error

**Problem:** Payment modal shows this error

**Debug:**
1. Check that ventaId, clientSecret, and amount are set in modal
2. Verify /pago/iniciar endpoint returns success: true

**Solution:**
- Ensure sale exists and belongs to current user's empresa
- Check sales amount is > 0
- Verify user is logged in and has permission

### Card payment succeeds but status doesn't update

**Problem:** Payment completed but venta still shows PENDIENTE

**Debug:**
1. Check payment_transactions table for the transaction
2. Check webhook is being triggered
3. Look for errors in Laravel logs

**Solution:**
- Run webhook manually: `stripe trigger payment_intent.succeeded`
- Check StripeWebhookController logs
- Verify webhook signature verification

### CSRF Token Error

**Problem:** "419 Page Expired" when submitting payment

**Debug:**
1. Check meta[name="csrf-token"] exists in layout
2. Verify token is included in request headers

**Solution:**
- Ensure @csrf is in layout blade
- Refresh page to get new token
- Check that X-CSRF-TOKEN header is sent

## Performance Notes

- Stripe.js library loads from CDN (async, non-blocking)
- Card Element initializes on modal open (lazy loading)
- Confirm payment takes 2-3 seconds average
- Webhook processing is handled asynchronously

## Security Checklist

✅ Private keys never exposed to frontend
✅ Public key fetched from secure endpoint
✅ Client secret only used for payment confirmation
✅ CSRF protection enabled
✅ Authorization checks on all endpoints
✅ Company ownership validation
✅ Webhook signature verified
✅ Sensitive data encrypted in database

## Production Considerations

Before deploying to production:

1. **Switch to Live Keys**
   - Update StripeConfig with live public/private keys
   - Ensure keys are encrypted before storing

2. **Test All Card Types**
   - Visa, Mastercard, American Express
   - International cards with different billing addresses

3. **Enable Webhook Signing**
   - Verify webhook signatures in production
   - Already implemented in StripeWebhookController

4. **Monitor and Logging**
   - Check Laravel logs for payment errors
   - Monitor webhook delivery failures
   - Set up alerts for failed payments

5. **Error Handling**
   - Test network failures
   - Test invalid credentials
   - Test quota/rate limit errors

## API Reference

### GET /admin/ventas/{venta}/pago/config

**Response (200 OK):**
```json
{
  "success": true,
  "publicKey": "pk_test_...",
  "ventaId": 1,
  "amount": 12500,
  "currency": "usd",
  "estatoPago": "PENDIENTE"
}
```

**Error (403):**
```json
{
  "success": false,
  "message": "No tienes permiso para acceder a este recurso"
}
```

### POST /admin/ventas/{venta}/pago/iniciar

**Response (200 OK):**
```json
{
  "success": true,
  "client_secret": "pi_1234567890_secret_xxxxxxxxx",
  "amount": 12500,
  "currency": "usd",
  "venta_id": 1
}
```

**Error (400):**
```json
{
  "success": false,
  "message": "Esta venta ya ha sido pagada"
}
```

### GET /admin/ventas/{venta}/pago/estado

**Response (200 OK):**
```json
{
  "success": true,
  "status": "succeeded",
  "amount": 12500,
  "currency": "usd",
  "created_at": "2026-01-30T10:30:00Z",
  "paid_at": "2026-01-30T10:31:45Z"
}
```

## JavaScript API

### initializePaymentFlow(ventaId)

Initiates the payment flow for a sale.

```javascript
// Call when user clicks "Pagar con Stripe" button
initializePaymentFlow(ventaId);

// Automatically:
// 1. Fetches payment config
// 2. Creates PaymentIntent
// 3. Opens modal with Card Element
// 4. Waits for user input
```

### initializeStripe(publicKey)

Initializes Stripe.js and Card Element (called automatically).

```javascript
// Advanced: Manual initialization
initializeStripe('pk_test_...');

// Mounts Card Element to #card-element
// Sets up form submission handler
```

### openStripePaymentModal(ventaId, amount, clientSecret)

Opens payment modal with payment details (called automatically).

```javascript
openStripePaymentModal(1, 12500, 'pi_secret...');

// Shows modal with amount and payment form
```

### closeStripePaymentModal()

Closes payment modal (called automatically on success).

```javascript
// Manual close
closeStripePaymentModal();
```

## FAQ

**Q: Can I test with a real card?**
A: Not recommended. Use Stripe test cards. Never use real cards in development.

**Q: Does the system support 3D Secure?**
A: Yes, automatically via Stripe.confirmCardPayment(). Test with 4000 0025 0000 3155.

**Q: What happens if webhook fails?**
A: Sale remains PENDIENTE. User can try payment again. Webhook retries are handled by Stripe.

**Q: Can I save cards for future payments?**
A: Currently not implemented. Can be added by storing PaymentMethod IDs.

**Q: What payment methods are supported?**
A: Currently Card Element only. Can be extended to Payment Element for more methods.

**Q: Is the payment PCI compliant?**
A: Yes. Private keys never touch frontend. Card data handled by Stripe.js only.

## Support

For issues:
1. Check browser console (F12)
2. Check Laravel logs: `tail -f storage/logs/laravel.log`
3. Check Stripe dashboard for payment intent status
4. Review webhook event log in Stripe dashboard
