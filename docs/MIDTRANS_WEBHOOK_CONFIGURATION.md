# Midtrans Webhook Configuration Guide

## Overview

This document provides instructions on how to configure Midtrans webhooks for production deployment. Webhooks enable Bread Heaven to automatically update order payment status when payment events occur.

## Important Notes

- **Local Development**: Webhooks do NOT work on localhost due to Midtrans servers not being able to reach your local machine
- **Solution for Local Testing**: Use the admin panel feature "Tandai Sebagai Pembayaran Manual" (Mark as Manual Payment) to manually mark orders as paid
- **Production**: Follow the instructions below to enable automatic webhook processing

---

## Configuration Steps

### 1. Access Midtrans Dashboard

1. Go to [Midtrans Dashboard](https://dashboard.midtrans.com)
2. Log in with your Midtrans account
3. Select your merchant account

### 2. Navigate to Webhook Settings

1. From the left sidebar, go to **Settings** → **Configuration**
2. Look for **Notification/Webhook URL** section
3. Or go directly to **Settings** → **Webhook**

### 3. Configure Webhook URL

**For Production:**
```
https://your-domain.com/webhook/payment/notification
```

**Example:**
```
https://bread-heaven.com/webhook/payment/notification
```

### 4. Webhook Events to Enable

Make sure these notification types are enabled:
- ✅ **Payment Success** (settlement)
- ✅ **Payment Failure** (deny)
- ✅ **Payment Pending** (pending)
- ✅ **Payment Expired** (expire)
- ✅ **Payment Cancelled** (cancel)
- ✅ **Payment Refund** (refund)

### 5. Webhook HTTP Method

- Set to: **POST**
- Make sure: **Content-Type: application/json**

### 6. Webhook Timeout

- Default timeout: 30 seconds (this is fine)
- Make sure your server can respond within this timeframe

---

## Webhook Payload Structure

When Midtrans sends a webhook notification, the payload will contain:

```json
{
  "transaction_time": "2026-07-23 21:30:00",
  "transaction_status": "settlement",
  "transaction_id": "0000000001234567-1234",
  "status_message": "The transaction has been successfully completed",
  "status_code": "200",
  "signature_key": "xxxxxxxxxxxx",
  "payment_type": "credit_card",
  "order_id": "INV-20260723-ABC123",
  "merchant_id": "G000000000",
  "masked_card": "48111111-1114",
  "gross_amount": "150000.00",
  "fraud_status": "accept",
  "eci": "05",
  "currency": "IDR"
}
```

---

## Application Webhook Handler

The webhook handler is located at:

**File:** `app/Http/Controllers/PaymentController.php`  
**Method:** `notification(Request $request)`  
**Route:** `POST /webhook/payment/notification`

### What the Handler Does

1. **Validates** the webhook signature (security check)
2. **Processes** the payment status update
3. **Updates** order payment status in database
4. **Creates** payment transaction records
5. **Logs** all webhook activities

### Webhook Processing Flow

```
Midtrans Webhook Received
         ↓
   Validate Signature
         ↓
   Process Status Change
         ↓
   Update Order Payment Status
         ↓
   Create Payment Transaction Record
         ↓
   Log & Return 200 OK
```

---

## Testing Webhook (Production Environment)

### Option 1: Midtrans Sandbox Testing

Midtrans provides a sandbox environment for testing:

1. Create test cards in [Midtrans Sandbox Documentation](https://docs.midtrans.com/en/technical-reference/test-transactions)
2. Use sandbox credentials in your `.env`:
   ```
   MIDTRANS_SERVER_KEY=your_sandbox_server_key
   MIDTRANS_CLIENT_KEY=your_sandbox_client_key
   MIDTRANS_IS_PRODUCTION=false
   ```
3. Make a test transaction and verify webhook logs

### Option 2: Manual Testing via Admin Panel

For testing without relying on Midtrans webhooks:

1. Go to **Admin** → **Orders**
2. Find the order with status "Unpaid"
3. Click the **"Tandai Sebagai Pembayaran Manual"** button
4. Confirm the action
5. Order status will update to "Paid" and a payment transaction record will be created

---

## Troubleshooting

### Webhook Not Triggering?

1. **Check Webhook URL is Correct**
   - Verify the URL is accessible from the internet
   - Test with: `curl -X POST https://your-domain.com/webhook/payment/notification`

2. **Verify HTTPS**
   - Midtrans requires HTTPS (not HTTP)
   - Install an SSL certificate on your server

3. **Check Firewall Rules**
   - Ensure your server's firewall allows POST requests
   - Whitelist Midtrans IP addresses if needed

4. **Check Laravel Logs**
   - File: `storage/logs/laravel.log`
   - Look for error messages related to webhook processing

5. **Midtrans Notification History**
   - Log into Midtrans Dashboard
   - Go to **Transactions** or **Notification History**
   - Check if webhook was sent and the response status

### Webhook Signature Validation Failed?

1. **Verify Credentials**
   - Check `MIDTRANS_SERVER_KEY` in `.env` matches Midtrans Dashboard
   - Server key should start with `SB-` (sandbox) or `MT-` (production)

2. **Check Webhook Secret**
   - In Midtrans Dashboard, verify the webhook secret matches your `.env`
   - This is used to validate webhook authenticity

---

## Security Considerations

### Webhook Signature Verification

All incoming webhooks are verified using SHA512 signature validation:

```php
// Signature is validated in MidtransService->handleNotification()
$signature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
```

This ensures:
- ✅ Webhook is genuinely from Midtrans
- ✅ Webhook payload has not been tampered with
- ✅ Only legitimate payment notifications are processed

### HTTPS Requirement

Always use HTTPS in production:
- ✅ Encrypts webhook data in transit
- ✅ Prevents man-in-the-middle attacks
- ✅ Required by Midtrans

---

## Webhook Events Handled

| Event | Status | Action |
|-------|--------|--------|
| **settlement** | Paid | Mark order as paid, process payment |
| **capture** | Paid | Mark order as paid (for certain payment types) |
| **pending** | Pending | Keep order in pending state |
| **deny** | Failed | Mark payment as failed |
| **expire** | Expired | Mark payment as expired |
| **cancel** | Cancelled | Mark payment as cancelled |
| **refund** | Refunded | Mark payment and order as refunded |

---

## Production Deployment Checklist

- [ ] SSL certificate installed (HTTPS enabled)
- [ ] Webhook URL added to Midtrans Dashboard
- [ ] Environment set to production (`MIDTRANS_IS_PRODUCTION=true`)
- [ ] Correct server keys in `.env`
- [ ] Firewall allows POST requests to webhook endpoint
- [ ] Database backups configured
- [ ] Error logging configured
- [ ] Test transaction completed successfully
- [ ] Admin panel "Mark as Manual Payment" tested
- [ ] Order and payment transaction records verified in database

---

## Related Files

- **Controller:** `app/Http/Controllers/PaymentController.php`
- **Service:** `app/Services/MidtransService.php`
- **Model:** `app/Models/PaymentTransaction.php`
- **Routes:** `routes/web.php` (webhook route)
- **Database:** `database/migrations/*payment_transactions*`

---

## Support & Additional Resources

- [Midtrans Documentation](https://docs.midtrans.com)
- [Midtrans Webhook Documentation](https://docs.midtrans.com/en/api/notification-webhook)
- [Laravel Security Best Practices](https://laravel.com/docs/security)

---

## Questions?

Contact your development team or Midtrans support for assistance.

Last Updated: 2026-07-23
