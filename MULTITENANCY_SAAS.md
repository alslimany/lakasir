# Multitenancy SaaS System Documentation

## Overview

This document describes the multitenancy SaaS implementation for Lakasir POS system. The system uses `stancl/tenancy` for database separation and `laravel/cashier` for Stripe subscriptions.

## Features Implemented

### 1. Multitenancy Setup
- ✅ Database separation using `stancl/tenancy`
- ✅ Tenant identification via subdomains (e.g., tenant1.lakasir.test)
- ✅ Central users table with tenant-specific databases
- ✅ Automatic tenant database creation and migration

### 2. Subscription Plans

Three subscription plans are available:

#### Basic Plan ($29/month)
- 100 products
- 2 users
- 1GB storage
- Basic reports
- Email support

#### Pro Plan ($99/month)
- 1,000 products
- 10 users
- 10GB storage
- Advanced reports
- Advanced analytics
- Priority support

#### Enterprise Plan ($299/month)
- Unlimited products
- Unlimited users
- Unlimited storage
- Advanced reports
- Advanced analytics
- Dedicated support
- Custom integrations
- White label branding

### 3. Tenant Management

#### Admin Panel
Access the admin panel at: `https://admin.yourdomain.com/admin`

Features:
- View all tenants
- Create/edit/delete tenants
- Suspend/activate tenant accounts
- Monitor tenant usage
- View subscription status

#### Tenant Dashboard
Each tenant has access to:
- Billing page (`/tenant/billing`)
- Current plan information
- Usage statistics
- Plan upgrade options

### 4. Subscription Lifecycle

#### Trial Period
- New tenants automatically receive a 14-day free trial
- Full access to all features during trial
- No credit card required during trial

#### Subscription Status
- **Active**: Tenant has an active paid subscription
- **Trial**: Tenant is in the trial period
- **Suspended**: Tenant account is suspended (payment failed or manual suspension)

### 5. Usage Tracking

The system tracks:
- **Product count**: Number of products in inventory
- **User count**: Number of users in the tenant account
- **Storage used**: Total file storage in bytes

Usage is automatically updated when:
- Products are created or deleted
- Users are created or deleted
- Files are uploaded

### 6. Feature Gating

Features are gated based on subscription plan:

```php
// Check if tenant can use a feature
$tenant = tenancy()->tenant;
if ($tenant->canUseFeature('products')) {
    // Allow access
}

// Check if limit is reached
if ($tenant->hasReachedLimit('products')) {
    // Show upgrade prompt
}
```

### 7. Middleware

#### CheckTenantStatus
Ensures tenant account is active and has valid subscription:
```php
Route::middleware(['tenant', CheckTenantStatus::class])->group(function () {
    // Protected routes
});
```

#### CheckSubscriptionFeatures
Validates specific feature access:
```php
Route::middleware(['tenant', 'subscription.features:advanced_reports'])->group(function () {
    // Feature-specific routes
});
```

## Setup Instructions

### 1. Environment Configuration

Add the following to your `.env` file:

```env
# Stripe Configuration
STRIPE_KEY=your_stripe_publishable_key
STRIPE_SECRET=your_stripe_secret_key
STRIPE_WEBHOOK_SECRET=your_webhook_secret
CASHIER_CURRENCY=usd
CASHIER_CURRENCY_LOCALE=en

# Tenancy Configuration
APP_CENTRAL_DOMAIN=lakasir.test
APP_ADMIN_DOMAIN=admin.lakasir.test
```

### 2. Database Migration

Run migrations to create necessary tables:

```bash
php artisan migrate
```

### 3. Seed Subscription Plans

```bash
php artisan db:seed --class=SubscriptionPlanSeeder
```

### 4. Stripe Configuration

1. Create products and prices in Stripe dashboard
2. Update `subscription_plans` table with Stripe price IDs:

```sql
UPDATE subscription_plans SET stripe_price_id = 'price_xxxxx' WHERE slug = 'basic';
UPDATE subscription_plans SET stripe_price_id = 'price_xxxxx' WHERE slug = 'pro';
UPDATE subscription_plans SET stripe_price_id = 'price_xxxxx' WHERE slug = 'enterprise';
```

3. Configure webhook endpoint in Stripe:
   - URL: `https://yourdomain.com/stripe/webhook`
   - Events: `customer.subscription.*`, `invoice.payment_succeeded`, `invoice.payment_failed`

### 5. Calculate Tenant Usage

Set up a cron job to calculate usage daily:

```bash
# In app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    $schedule->command('tenant:calculate-usage')->daily();
}
```

Or run manually:
```bash
php artisan tenant:calculate-usage
```

## API Usage

### Creating a Subscription

```php
use App\Services\SubscriptionService;
use App\Models\SubscriptionPlan;

$subscriptionService = app(SubscriptionService::class);
$plan = SubscriptionPlan::where('slug', 'pro')->first();
$tenant = tenancy()->tenant;

$subscriptionService->createSubscription($tenant, $plan);
```

### Changing Plans

```php
$newPlan = SubscriptionPlan::where('slug', 'enterprise')->first();
$subscriptionService->changePlan($tenant, $newPlan);
```

### Cancelling Subscription

```php
// Cancel at period end
$subscriptionService->cancelSubscription($tenant);

// Cancel immediately
$subscriptionService->cancelSubscription($tenant, immediately: true);
```

## File Structure

```
app/
├── Filament/
│   ├── Admin/
│   │   ├── Resources/
│   │   │   ├── TenantResource.php
│   │   │   └── SubscriptionPlanResource.php
│   │   └── Widgets/
│   │       └── StatsOverview.php
│   └── Tenant/
│       └── Pages/
│           └── Billing.php
├── Http/
│   ├── Controllers/
│   │   └── StripeWebhookController.php
│   └── Middleware/
│       ├── CheckTenantStatus.php
│       └── CheckSubscriptionFeatures.php
├── Models/
│   ├── SubscriptionPlan.php
│   └── TenantUsage.php
├── Services/
│   ├── RegisterTenant.php
│   └── SubscriptionService.php
└── Tenant.php (enhanced with Billable trait)

database/
├── migrations/
│   ├── 2024_10_12_000001_create_subscription_plans_table.php
│   ├── 2024_10_12_000002_add_subscription_fields_to_tenants_table.php
│   └── 2024_10_12_000003_create_tenant_usage_table.php
└── seeders/
    └── SubscriptionPlanSeeder.php
```

## Testing

### Create a Test Tenant

```bash
php artisan tinker
```

```php
$tenant = \App\Tenant::create([
    'id' => 'test',
    'tenancy_db_name' => 'lakasir_test',
    'tenancy_email' => 'test@example.com',
    'is_active' => true,
    'trial_ends_at' => now()->addDays(14),
]);

$tenant->domains()->create(['domain' => 'test.lakasir.test']);
$tenant->usage()->create(['product_count' => 0, 'user_count' => 0, 'storage_used' => 0]);
```

### Test Subscription Flow

1. Visit tenant domain: `http://test.lakasir.test`
2. Navigate to Billing page
3. Select a subscription plan
4. Complete Stripe checkout
5. Verify subscription is active

## Troubleshooting

### Tenant Not Found
- Verify subdomain is correctly configured
- Check `tenants` and `domains` tables
- Ensure DNS/hosts file is configured

### Subscription Not Working
- Verify Stripe keys are correct
- Check webhook is configured in Stripe
- Review webhook logs in Stripe dashboard

### Usage Not Updating
- Run `php artisan tenant:calculate-usage`
- Check observers are registered in `EventServiceProvider`
- Verify tenant context is initialized

## Security Considerations

1. **Webhook Verification**: Always verify Stripe webhook signatures
2. **Tenant Isolation**: Each tenant's data is in a separate database
3. **Access Control**: Use middleware to enforce subscription limits
4. **Secure Keys**: Store Stripe keys in `.env`, never commit to repository

## Future Enhancements

- [ ] Add metered billing for API usage
- [ ] Implement usage alerts and notifications
- [ ] Add custom plan creation in admin panel
- [ ] Implement tenant data export/backup
- [ ] Add multi-currency support
- [ ] Implement referral/affiliate system

## Support

For issues or questions:
- Email: support@lakasir.com
- Documentation: https://docs.lakasir.com
- GitHub: https://github.com/alslimany/lakasir
