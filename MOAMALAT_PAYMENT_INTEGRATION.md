# Moamalat Payment Gateway Integration

## Overview

Lakasir now supports Moamalat payment gateway for processing subscription payments. This integration allows tenants to subscribe to plans using the Moamalat Lightbox payment system.

## Features

- **Local Payment Support**: Integration with Moamalat, a Libyan payment gateway
- **Subscription Management**: Users can subscribe, upgrade, or downgrade plans
- **Trial Period**: New tenants receive a 30-day (1 month) free trial
- **Automatic Subscription Tracking**: System tracks subscription start date and expiry date
- **Webhook Support**: Real-time payment confirmation via Moamalat webhooks

## Configuration

### Environment Variables

Add the following variables to your `.env` file:

```env
# Moamalat Payment Gateway Configuration
MOAMALATPAY_MID=your_merchant_id
MOAMALATPAY_TID=your_terminal_id
MOAMALATPAY_KEY=your_secure_key
MOAMALATPAY_NOTIFICATION_KEY=your_notification_key
MOAMALATPAY_PRODUCTION=false
```

### Testing Credentials

For testing purposes, you can use these credentials provided by Moamalat:

```env
MOAMALATPAY_MID=10081014649
MOAMALATPAY_TID=99179395
MOAMALATPAY_KEY=39636630633731362D663963322D346362642D386531662D633963303432353936373431
MOAMALATPAY_PRODUCTION=false
```

**Test Card Details:**
- Card Number: `6394993077260781`
- Expiry: `12/24`
- OTP: `111111`

## Database Migrations

Run the following migrations to set up the payment system:

```bash
php artisan migrate
```

This will create:
- `moamalat_pay_notifications` table for storing payment notifications
- Subscription date fields (`subscription_started_at`, `subscription_expires_at`) in tenants table

## Usage

### For Tenants

1. **Navigate to Billing Page**: Go to Settings > Billing & Subscription
2. **Choose a Plan**: Click "Choose Plan" button and select your desired subscription plan
3. **Complete Payment**: 
   - A payment modal will appear
   - Click "Pay Now" to initiate Moamalat payment
   - Complete the payment using your card details
4. **Confirmation**: After successful payment, your subscription will be activated automatically

### Subscription Details

The billing page displays:
- Current active plan
- Subscription start date
- Subscription expiry date
- Time remaining until expiry
- Usage statistics (products, users, storage)
- Available plans for upgrade/downgrade

## Payment Flow

1. **Plan Selection**: User selects a subscription plan
2. **Payment Initiation**: System calls `/subscription/initiate-payment` endpoint
3. **Moamalat Lightbox**: Payment modal opens with plan details
4. **Payment Processing**: User completes payment through Moamalat
5. **Webhook Notification**: Moamalat sends payment confirmation to webhook
6. **Subscription Activation**: System updates tenant subscription status
7. **Redirect**: User is redirected to billing page with success message

## API Endpoints

### Subscription Routes (Tenant Context)

```
POST   /subscription/initiate-payment  - Initiate payment for a plan
GET    /subscription/payment/success   - Handle successful payment callback
GET    /subscription/payment/cancel    - Handle cancelled payment
```

### Webhook (Central)

```
POST   /moamalat-pay/notify  - Moamalat payment webhook (handled by package)
```

## Implementation Details

### Controllers

- **SubscriptionController**: Handles payment initiation and callbacks
- **MoamalatWebhookController**: Processes payment notifications from Moamalat

### Models

- **Tenant**: Extended with subscription date tracking
  - `subscription_started_at`: When subscription began
  - `subscription_expires_at`: When subscription will expire
- **SubscriptionPlan**: Stores plan details, pricing, and features

### Services

- **RegisterTenant**: Updated to create tenants with 30-day trial period

## Trial Period

New tenants automatically receive a **30-day (1 month) free trial** upon registration. During this period:
- Full access to all features based on trial plan
- No payment required
- Trial countdown displayed on billing page
- Must subscribe to a paid plan before trial expires

## Subscription Expiry

When a subscription expires:
- System marks tenant as inactive
- Access to features is restricted
- User is prompted to renew subscription on billing page
- Grace period may apply (configurable)

## Webhook Security

Moamalat webhooks are secured using:
- Secure hash validation
- IP address filtering (configurable in `config/moamalat-pay.php`)
- Request verification

### Configuring Allowed IPs

Edit `config/moamalat-pay.php`:

```php
'notification' => [
    'allowed_ips' => ['*'],  // Allow all (testing only)
    // Or specify Moamalat server IPs:
    // 'allowed_ips' => ['xxx.xxx.xxx.xxx', 'yyy.yyy.yyy.yyy'],
]
```

## Troubleshooting

### Payment Not Completing

1. Check browser console for JavaScript errors
2. Verify Moamalat credentials in `.env`
3. Ensure webhook URL is accessible from Moamalat servers
4. Check `storage/logs/laravel.log` for errors

### Subscription Not Activating

1. Verify webhook is being received (check logs)
2. Ensure tenant ID is correctly encoded in payment reference
3. Check subscription plan exists and matches payment amount
4. Verify database migrations are complete

### Testing Issues

1. Use test credentials provided above
2. Set `MOAMALATPAY_PRODUCTION=false`
3. Clear browser cache and cookies
4. Check that Moamalat Lightbox script is loading

## Support

For issues related to:
- **Moamalat Integration**: Refer to [Moamalat Documentation](https://docs.moamalat.net/lightBox.html)
- **Package Issues**: Check [laravel-moamalat-pay GitHub](https://github.com/alifaraun/laravel-moamalat-pay)
- **Lakasir Issues**: Create an issue on the Lakasir repository

## Related Documentation

- [Moamalat Lightbox Documentation](https://docs.moamalat.net/lightBox.html)
- [Laravel Moamalat Package](https://github.com/alifaraun/laravel-moamalat-pay)
- [Subscription Plans Documentation](./MULTITENANCY_SAAS.md)
