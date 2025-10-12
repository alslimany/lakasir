# Lakasir Multitenancy SaaS - Complete Guide

> A complete multitenancy SaaS implementation for Lakasir POS with Stripe subscriptions, usage tracking, and feature gating.

## 📋 Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Quick Start](#quick-start)
- [Documentation](#documentation)
- [Architecture](#architecture)
- [Subscription Plans](#subscription-plans)
- [Configuration](#configuration)
- [Usage](#usage)
- [API Reference](#api-reference)
- [Testing](#testing)
- [Deployment](#deployment)
- [Troubleshooting](#troubleshooting)
- [Contributing](#contributing)

## 🎯 Overview

This implementation transforms Lakasir POS into a complete SaaS platform with:

- **Multi-tenant architecture** with database separation
- **Stripe-powered subscriptions** with three pricing tiers
- **Usage tracking and limits** for products, users, and storage
- **14-day free trial** for new tenants
- **Admin panel** for super admin management
- **Tenant billing portal** with self-service capabilities

## ✨ Features

### Core Features

- ✅ **Multi-Tenancy**
  - Database-level isolation using stancl/tenancy
  - Subdomain-based tenant identification
  - Automatic tenant database creation and migration
  
- ✅ **Subscription Management**
  - Three pricing tiers (Basic, Pro, Enterprise)
  - Stripe Checkout integration
  - Automatic trial period (14 days)
  - Upgrade/downgrade capabilities
  - Grace period for cancellations
  
- ✅ **Usage Tracking**
  - Real-time product count tracking
  - User count monitoring
  - Storage usage calculation
  - Automatic observer-based updates
  
- ✅ **Feature Gating**
  - Plan-based feature access
  - Usage limit enforcement
  - Upgrade prompts when limits reached
  - Middleware-based protection
  
- ✅ **Admin Panel**
  - Tenant management (CRUD)
  - Subscription plan management
  - Suspend/activate tenants
  - Analytics dashboard
  - Usage monitoring
  
- ✅ **Tenant Portal**
  - Self-service billing page
  - Usage statistics display
  - Plan comparison
  - Upgrade options

## 🚀 Quick Start

### 1. Install Dependencies

```bash
# Already included in composer.json
composer install
```

### 2. Run Migrations

```bash
php artisan migrate
```

### 3. Seed Subscription Plans

```bash
php artisan db:seed --class=SubscriptionPlanSeeder
```

### 4. Configure Environment

```bash
# Copy .env.example and configure
cp .env.example .env

# Add Stripe credentials
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...

# Configure domains
APP_CENTRAL_DOMAIN=lakasir.test
APP_ADMIN_DOMAIN=admin.lakasir.test
```

### 5. Set Up Stripe

1. Create products in [Stripe Dashboard](https://dashboard.stripe.com)
2. Get Price IDs for each plan
3. Update database:

```sql
UPDATE subscription_plans SET stripe_price_id = 'price_xxxxx' WHERE slug = 'basic';
UPDATE subscription_plans SET stripe_price_id = 'price_xxxxx' WHERE slug = 'pro';
UPDATE subscription_plans SET stripe_price_id = 'price_xxxxx' WHERE slug = 'enterprise';
```

4. Configure webhook:
   - URL: `https://yourdomain.com/stripe/webhook`
   - Events: `customer.subscription.*`, `invoice.payment_*`

### 6. Access the Application

- **Admin Panel**: `https://admin.yourdomain.com/admin`
- **Tenant Registration**: `https://yourdomain.com/auth/register`
- **Tenant Domain**: `https://tenant1.yourdomain.com`

## 📚 Documentation

Comprehensive documentation is available in multiple files:

| Document | Description |
|----------|-------------|
| [MULTITENANCY_SAAS.md](MULTITENANCY_SAAS.md) | Complete system documentation and setup guide |
| [IMPLEMENTATION_SUMMARY.md](IMPLEMENTATION_SUMMARY.md) | Technical implementation details and file structure |
| [USAGE_EXAMPLES.md](USAGE_EXAMPLES.md) | Practical code examples for common tasks |
| [ARCHITECTURE.md](ARCHITECTURE.md) | System architecture and data flow diagrams |

## 🏗️ Architecture

### High-Level Overview

```
User → Subdomain → Middleware → Tenant Context → Database
     ↓
Stripe Webhooks → Subscription Management → Usage Tracking
```

### Key Components

1. **Tenant Model** (`app/Tenant.php`)
   - Enhanced with Laravel Cashier's Billable trait
   - Subscription status methods
   - Feature access checking
   - Usage limit validation

2. **Subscription Plans** (`app/Models/SubscriptionPlan.php`)
   - Plan configuration
   - Feature limits
   - Pricing information

3. **Usage Tracking** (`app/Models/TenantUsage.php`)
   - Product count
   - User count
   - Storage usage
   - Automatic calculation

4. **Admin Panel** (`app/Filament/Admin/*`)
   - Tenant management
   - Plan management
   - Analytics

5. **Billing Portal** (`app/Filament/Tenant/Pages/Billing.php`)
   - Current plan display
   - Usage statistics
   - Plan selection

See [ARCHITECTURE.md](ARCHITECTURE.md) for detailed diagrams.

## 💰 Subscription Plans

| Feature | Basic | Pro | Enterprise |
|---------|-------|-----|------------|
| **Price** | $29/month | $99/month | $299/month |
| **Products** | 100 | 1,000 | Unlimited |
| **Users** | 2 | 10 | Unlimited |
| **Storage** | 1GB | 10GB | Unlimited |
| **Reports** | Basic | Advanced | Advanced |
| **Analytics** | ❌ | ✅ | ✅ |
| **Support** | Email | Priority | Dedicated |
| **Custom Integrations** | ❌ | ❌ | ✅ |
| **White Label** | ❌ | ❌ | ✅ |

All plans include a **14-day free trial** with full access to features.

## ⚙️ Configuration

### Environment Variables

```env
# Stripe
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...
CASHIER_CURRENCY=usd
CASHIER_CURRENCY_LOCALE=en

# Tenancy
APP_CENTRAL_DOMAIN=lakasir.test
APP_ADMIN_DOMAIN=admin.lakasir.test

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lakasir
```

### Scheduled Tasks

Add to `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    // Calculate usage daily
    $schedule->command('tenant:calculate-usage')->daily();
    
    // Or hourly for more accurate tracking
    // $schedule->command('tenant:calculate-usage')->hourly();
}
```

## 💻 Usage

### Check Subscription Status

```php
$tenant = tenancy()->tenant;

if ($tenant->hasActiveSubscription()) {
    // Tenant has active subscription or trial
}

if ($tenant->onTrial()) {
    // Tenant is in trial period
    $daysLeft = now()->diffInDays($tenant->trial_ends_at);
}
```

### Enforce Feature Limits

```php
$tenant = tenancy()->tenant;

if ($tenant->hasReachedLimit('products')) {
    // Show upgrade prompt
    return redirect()->route('filament.tenant.pages.billing');
}

// Get remaining count
$remaining = $tenant->getRemainingFeatureCount('products');
// Returns -1 for unlimited, 0 if at limit, or positive number
```

### Use in Blade Views

```blade
{{-- Display upgrade prompt --}}
<x-upgrade-prompt feature="products" />

{{-- Check feature access --}}
@if(tenancy()->tenant->canUseFeature('advanced_reports'))
    <a href="/reports/advanced">Advanced Reports</a>
@endif
```

### Use in Filament Resources

```php
use App\Traits\ChecksSubscriptionLimits;

class ProductResource extends Resource
{
    use ChecksSubscriptionLimits;
    
    public static function canCreate(): bool
    {
        return !tenancy()->tenant->hasReachedLimit('products');
    }
}
```

See [USAGE_EXAMPLES.md](USAGE_EXAMPLES.md) for more examples.

## 📖 API Reference

### Tenant Model Methods

```php
// Subscription status
$tenant->hasActiveSubscription(): bool
$tenant->onTrial(): bool

// Feature access
$tenant->canUseFeature(string $feature): bool
$tenant->hasReachedLimit(string $feature): bool
$tenant->getRemainingFeatureCount(string $feature): ?int

// Relationships
$tenant->subscriptionPlan(): BelongsTo
$tenant->usage(): HasOne
```

### SubscriptionService Methods

```php
$service = app(SubscriptionService::class);

// Create subscription
$service->createSubscription(Tenant $tenant, SubscriptionPlan $plan): void

// Change plan
$service->changePlan(Tenant $tenant, SubscriptionPlan $newPlan): void

// Cancel subscription
$service->cancelSubscription(Tenant $tenant, bool $immediately = false): void

// Resume subscription
$service->resumeSubscription(Tenant $tenant): void
```

### Console Commands

```bash
# Calculate usage for all tenants
php artisan tenant:calculate-usage

# Calculate usage for specific tenant
php artisan tenant:calculate-usage tenant_id
```

## 🧪 Testing

### Manual Testing Checklist

- [ ] Create new tenant with trial
- [ ] Subscribe to Basic plan via Stripe
- [ ] Create products up to limit
- [ ] Test upgrade prompt when limit reached
- [ ] Upgrade to Pro plan
- [ ] Test increased limits
- [ ] Cancel subscription
- [ ] Verify grace period
- [ ] Resume subscription
- [ ] Test webhook events
- [ ] Suspend tenant from admin
- [ ] Verify suspended page
- [ ] Calculate usage manually
- [ ] Check admin panel analytics

### Feature Tests

```php
/** @test */
public function tenant_can_create_products_within_limit()
{
    $tenant = Tenant::factory()->create();
    $plan = SubscriptionPlan::factory()->create([
        'features' => ['products' => 10],
    ]);
    
    $tenant->update(['subscription_plan_id' => $plan->id]);
    
    $this->assertFalse($tenant->hasReachedLimit('products'));
    
    // Create 10 products
    // ...
    
    $this->assertTrue($tenant->hasReachedLimit('products'));
}
```

## 🚢 Deployment

### Prerequisites

- PHP 8.1+
- MySQL 8.0+
- Composer
- Node.js & NPM
- Stripe account

### Production Checklist

- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Configure proper database credentials
- [ ] Set up Stripe production keys
- [ ] Configure webhook endpoint in Stripe
- [ ] Set up proper domain DNS
- [ ] Configure SSL certificates
- [ ] Set up cron for scheduled tasks
- [ ] Configure queue worker
- [ ] Set up monitoring and logging
- [ ] Run migrations
- [ ] Seed subscription plans
- [ ] Test complete flow

### Deployment Steps

```bash
# 1. Clone repository
git clone https://github.com/alslimany/lakasir.git
cd lakasir

# 2. Install dependencies
composer install --no-dev
npm install && npm run build

# 3. Configure environment
cp .env.example .env
php artisan key:generate

# 4. Run migrations
php artisan migrate --force

# 5. Seed plans
php artisan db:seed --class=SubscriptionPlanSeeder

# 6. Set up cron
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1

# 7. Start queue worker
php artisan queue:work --daemon
```

## 🔧 Troubleshooting

### Common Issues

**Issue: Tenant not found**
- Check subdomain configuration
- Verify `tenants` and `domains` tables
- Check DNS/hosts file

**Issue: Subscription not working**
- Verify Stripe keys are correct
- Check webhook configuration
- Review Stripe webhook logs

**Issue: Usage not updating**
- Run `php artisan tenant:calculate-usage`
- Check observers are registered
- Verify tenant context is initialized

**Issue: Admin panel not accessible**
- Check `APP_ADMIN_DOMAIN` in `.env`
- Verify AdminPanelProvider is registered
- Check route configuration

See [MULTITENANCY_SAAS.md](MULTITENANCY_SAAS.md) for detailed troubleshooting.

## 🤝 Contributing

We welcome contributions! Please:

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests
5. Submit a pull request

## 📝 License

This project is part of Lakasir POS. See main repository for license information.

## 🆘 Support

- **Documentation**: See linked documentation files above
- **Issues**: [GitHub Issues](https://github.com/alslimany/lakasir/issues)
- **Email**: support@lakasir.com

## 🎉 Credits

Developed as a complete multitenancy SaaS solution for Lakasir POS.

### Technologies Used

- [Laravel](https://laravel.com) - PHP Framework
- [Laravel Cashier](https://laravel.com/docs/billing) - Stripe Integration
- [stancl/tenancy](https://tenancyforlaravel.com) - Multi-tenancy Package
- [Filament](https://filamentphp.com) - Admin Panel
- [Stripe](https://stripe.com) - Payment Processing

---

**Ready to get started?** Follow the [Quick Start](#quick-start) guide above!

For detailed information, see the comprehensive documentation files:
- [MULTITENANCY_SAAS.md](MULTITENANCY_SAAS.md)
- [IMPLEMENTATION_SUMMARY.md](IMPLEMENTATION_SUMMARY.md)
- [USAGE_EXAMPLES.md](USAGE_EXAMPLES.md)
- [ARCHITECTURE.md](ARCHITECTURE.md)
