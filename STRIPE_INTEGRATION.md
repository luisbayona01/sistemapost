# CinemaPOS - Stripe PaymentIntent Integration Guide

## Overview

This guide explains how to test the Stripe PaymentIntent payment integration in CinemaPOS locally and in production.

## Architecture

### Payment Flow

```
1. Create Sale (PENDING status)
   ↓
2. Frontend initiates payment
   ↓
3. POST /admin/ventas/{venta}/pago/iniciar
   ↓
4. Backend creates PaymentIntent in Stripe
   ↓
5. Frontend collects payment with Stripe Elements
   ↓
6. Stripe sends webhook: payment_intent.succeeded/failed
   ↓
7. Webhook handler marks venta as PAGADA + creates movimiento (success only)
```

### Key Components

- **StripePaymentService** (`app/Services/StripePaymentService.php`): Core payment logic
- **StripeWebhookController** (`app/Http/Controllers/StripeWebhookController.php`): Webhook handler
- **Payment routes**: 
  - `POST /webhooks/stripe` (no auth required)
  - `POST /admin/ventas/{venta}/pago/iniciar` (auth required)
  - `GET /admin/ventas/{venta}/pago/estado` (auth required)

## Local Testing Setup

### 1. Install Stripe CLI

Download and install [Stripe CLI](https://stripe.com/docs/stripe-cli):

```bash
# macOS
brew install stripe/stripe-cli/stripe

# Linux (Ubuntu/Debian)
curl https://files.stripe.com/stripe-cli/install.sh -o install.sh
sudo bash install.sh

# Windows (via Chocolatey)
choco install stripe-cli
```

### 2. Configure Test Keys

Get your test keys from [Stripe Dashboard](https://dashboard.stripe.com/test/keys):

1. Go to Dashboard → Developers → API Keys
2. Copy **Publishable Key** (pk_test_...)
3. Copy **Secret Key** (sk_test_...)

Add to `.env`:

```env
STRIPE_PUBLIC_KEY=pk_test_XXXXXXXXXXXXXXXXXXXXX
STRIPE_SECRET_KEY=sk_test_XXXXXXXXXXXXXXXXXXXXX
STRIPE_WEBHOOK_SECRET=whsec_test_XXXXXXXXXXXXXXXXXXXXX  # Will get from stripe listen
```

### 3. Load Demo Data

Run the demo seeder to create:
- Cinema Fénix empresa
- Admin user (admin@cinefenix.local / password123)
- Stripe test keys configuration

```bash
php artisan db:seed --class=DemoSeeder
```

### 4. Start Webhook Tunnel

In a new terminal, start Stripe CLI tunnel:

```bash
stripe login
# Authenticate with your Stripe account

stripe listen --forward-to localhost:8000/webhooks/stripe
# Output will show webhook signing secret
```

Save the `webhook_secret` to `.env`:

```env
STRIPE_WEBHOOK_SECRET=whsec_test_XXXXX...
```

### 5. Start Laravel Development Server

```bash
php artisan serve
# Starts at http://localhost:8000
```

## Testing Payment Flow

### 1. Login to Demo Account

- URL: `http://localhost:8000`
- Email: `admin@cinefenix.local`
- Password: `password123`

### 2. Create a Sale

1. Navigate to **Ventas** section
2. Click **Nueva Venta**
3. Add products (e.g., Palomitas Medianas $8.00)
4. Complete sale form
5. Submit sale → Venta created with `estado_pago = PENDIENTE`

### 3. Initiate Payment

1. From venta list, click on the sale
2. Click **"Pagar con Stripe"** button
3. Client secret is sent to frontend

### 4. Collect Payment with Test Card

In Stripe payment form, use test card numbers:

**Successful payment:**
- Card: `4242 4242 4242 4242`
- Expiry: Any future date (e.g., 12/25)
- CVC: Any 3 digits (e.g., 123)
- ZIP: Any value

**Failed payment:**
- Card: `4000 0000 0000 0002`
- (Other fields same as above)

**Requires authentication:**
- Card: `4000 0025 0000 3155`

### 5. Confirm in Database

After payment:

1. Check `payment_transactions` table:
   ```sql
   SELECT * FROM payment_transactions 
   WHERE venta_id = ? 
   ORDER BY created_at DESC;
   ```

2. Check `ventas` table for `estado_pago`:
   ```sql
   SELECT id, total, estado_pago FROM ventas WHERE id = ?;
   ```

3. Check `movimientos` table for payment entry:
   ```sql
   SELECT * FROM movimientos 
   WHERE venta_id = ? AND tipo_movimiento = 'INGRESO';
   ```

## API Endpoints

### Initiate Payment

```http
POST /admin/ventas/{venta}/pago/iniciar
Authorization: Bearer {token}
Content-Type: application/json

Response (200):
{
    "success": true,
    "client_secret": "pi_xxxxxx",
    "amount": 800,
    "currency": "usd",
    "venta_id": 1
}
```

### Check Payment Status

```http
GET /admin/ventas/{venta}/pago/estado
Authorization: Bearer {token}

Response (200):
{
    "success": true,
    "status": "SUCCESS|PENDING|FAILED|CANCELLED",
    "amount": 800,
    "currency": "usd",
    "created_at": "2026-01-30T21:24:00Z",
    "paid_at": "2026-01-30T21:25:00Z"
}
```

### Webhook

```http
POST /webhooks/stripe
Stripe-Signature: t=timestamp,v1=signature

# Events handled:
- payment_intent.succeeded
- payment_intent.payment_failed
- payment_intent.canceled
```

## Webhook Testing with Stripe CLI

Stripe CLI automatically forwards events to your local webhook endpoint:

```bash
stripe listen --forward-to localhost:8000/webhooks/stripe

# In another terminal, trigger test events:
stripe trigger payment_intent.succeeded
stripe trigger payment_intent.payment_failed
```

Monitor webhook logs:
```bash
stripe logs tail --follow
```

## Production Deployment

### 1. Update Environment Variables

Set production Stripe keys in your hosting platform:

```env
STRIPE_PUBLIC_KEY=pk_live_XXXXXXXXXXXXXXXXXXXXX
STRIPE_SECRET_KEY=sk_live_XXXXXXXXXXXXXXXXXXXXX
STRIPE_WEBHOOK_SECRET=whsec_live_XXXXXXXXXXXXXXXXXXXXX
```

### 2. Configure Webhook Endpoint

In [Stripe Dashboard](https://dashboard.stripe.com/webhooks):

1. Go to **Developers → Webhooks**
2. Click **Add Endpoint**
3. Endpoint URL: `https://yourdomain.com/webhooks/stripe`
4. Events to send:
   - `payment_intent.succeeded`
   - `payment_intent.payment_failed`
   - `payment_intent.canceled`
5. Copy webhook signing secret to `.env`

### 3. Test Webhook

Use Stripe CLI to forward to production:

```bash
stripe listen --api-key sk_live_... --forward-to https://yourdomain.com/webhooks/stripe
```

### 4. Security Checklist

- ✅ HTTPS only (no HTTP)
- ✅ Stripe keys encrypted in database
- ✅ Webhook signature verified
- ✅ Rate limiting on payment endpoints
- ✅ Audit logs created for all payments
- ✅ Error messages don't leak sensitive data

## Troubleshooting

### Webhook not received

1. Check Stripe webhook endpoint is accessible:
   ```bash
   curl -i https://yourdomain.com/webhooks/stripe
   ```

2. Verify webhook secret in `.env` matches Stripe Dashboard

3. Check Laravel logs:
   ```bash
   tail -f storage/logs/laravel.log | grep -i stripe
   ```

### Payment marked FAILED but expected SUCCESS

1. Check test card being used (see Test Cards section)
2. Verify `estado_pago` field exists in `ventas` table
3. Check `payment_transactions` table for error message

### "No PaymentIntent found"

1. Ensure `iniciarPago()` was called first
2. Check `payment_transactions` table for entry
3. Verify empresa_id matches auth user

### Webhook signature verification fails

1. Verify `STRIPE_WEBHOOK_SECRET` in `.env`
2. Ensure secret matches value in Stripe Dashboard
3. Check raw webhook body is not modified

## Key Features

✅ **PaymentIntent workflow**: Non-intrusive, soft decline handling  
✅ **Multi-company isolation**: Each empresa has separate StripeConfig  
✅ **Automatic movimiento creation**: Only on successful payment  
✅ **DB transactions**: Atomic operations for consistency  
✅ **Webhook signature verification**: Security best practice  
✅ **Encrypted keys**: Stripe secrets encrypted in database  
✅ **Activity logging**: All payments logged for audit trail  

## Database Schema

### stripe_configs table
```sql
CREATE TABLE stripe_configs (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  empresa_id BIGINT UNIQUE NOT NULL (FK),
  public_key VARCHAR(255) ENCRYPTED,
  secret_key TEXT ENCRYPTED,
  webhook_secret TEXT ENCRYPTED,
  test_mode BOOLEAN DEFAULT true,
  enabled BOOLEAN DEFAULT false,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  INDEX (empresa_id, enabled)
);
```

### payment_transactions table
```sql
CREATE TABLE payment_transactions (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  empresa_id BIGINT NOT NULL (FK),
  venta_id BIGINT NOT NULL (FK),
  payment_method ENUM('CASH','CARD','STRIPE','OTHER'),
  stripe_payment_intent_id VARCHAR(255),
  stripe_charge_id VARCHAR(255),
  amount_paid DECIMAL(10,2),
  currency VARCHAR(3),
  status ENUM('PENDING','SUCCESS','FAILED','REFUNDED','CANCELLED'),
  metadata JSON,
  error_message TEXT,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  UNIQUE (stripe_payment_intent_id),
  INDEX (empresa_id, venta_id),
  INDEX (empresa_id, status),
  INDEX (created_at)
);
```

### ventas table - new field
```sql
ALTER TABLE ventas ADD COLUMN estado_pago 
  ENUM('PENDIENTE','PAGADA','FALLIDA','CANCELADA') 
  DEFAULT 'PENDIENTE'
  INDEX (empresa_id, estado_pago);
```

## Next Steps

- Frontend integration: Stripe Elements form in venta views
- Refund functionality: Handle payment refunds
- Partial payments: Support installment plans
- Analytics: Payment reports and metrics
- Notifications: Email receipts on payment success

## Support

For issues or questions:
1. Check [Stripe Documentation](https://stripe.com/docs)
2. Review Laravel logs: `storage/logs/laravel.log`
3. Check Stripe Dashboard for event logs
4. Use `stripe logs tail` for webhook debugging

---

**Last Updated:** 2026-01-30  
**Stripe API Version:** Latest  
**Status:** Production Ready ✅
