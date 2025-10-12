# Usage Examples

This document provides practical examples of how to use the multitenancy SaaS features in your code.

## Table of Contents
1. [Checking Subscription Status](#checking-subscription-status)
2. [Enforcing Feature Limits](#enforcing-feature-limits)
3. [Using the ChecksSubscriptionLimits Trait](#using-the-checkssubscriptionlimits-trait)
4. [Displaying Upgrade Prompts](#displaying-upgrade-prompts)
5. [Managing Subscriptions](#managing-subscriptions)
6. [Custom Middleware Usage](#custom-middleware-usage)

## Checking Subscription Status

### In Controllers

```php
use App\Tenant;

class ProductController extends Controller
{
    public function index()
    {
        $tenant = tenancy()->tenant;
        
        // Check if tenant has active subscription
        if (!$tenant->hasActiveSubscription()) {
            return redirect()->route('filament.tenant.pages.billing');
        }
        
        // Check if on trial
        if ($tenant->onTrial()) {
            $daysLeft = now()->diffInDays($tenant->trial_ends_at, false);
            // Show trial banner
        }
        
        return view('products.index');
    }
}
```

### In Blade Views

```blade
@php
    $tenant = tenancy()->tenant;
@endphp

@if($tenant->onTrial())
    <div class="alert alert-warning">
        Your trial expires in {{ $tenant->trial_ends_at->diffForHumans() }}
    </div>
@endif

@if(!$tenant->hasActiveSubscription())
    <div class="alert alert-danger">
        Your subscription has expired. Please update your billing information.
    </div>
@endif
```

## Enforcing Feature Limits

### Check Feature Availability

```php
$tenant = tenancy()->tenant;

// Check if tenant can use a feature
if ($tenant->canUseFeature('advanced_reports')) {
    // Show advanced reports
} else {
    // Show upgrade prompt
}

// Check if limit is reached
if ($tenant->hasReachedLimit('products')) {
    return response()->json([
        'message' => 'Product limit reached. Please upgrade.',
        'limit' => $tenant->subscriptionPlan->getFeatureLimit('products'),
        'current' => $tenant->usage->product_count,
    ], 403);
}
```

### Get Remaining Count

```php
$tenant = tenancy()->tenant;

$remainingProducts = $tenant->getRemainingFeatureCount('products');

if ($remainingProducts === -1) {
    echo "Unlimited products available";
} elseif ($remainingProducts === 0) {
    echo "Product limit reached";
} else {
    echo "You can add {$remainingProducts} more products";
}
```

## Using the ChecksSubscriptionLimits Trait

### In Filament Resources

```php
namespace App\Filament\Tenant\Resources;

use App\Traits\ChecksSubscriptionLimits;
use Filament\Resources\Resource;

class ProductResource extends Resource
{
    use ChecksSubscriptionLimits;
    
    protected static ?string $model = Product::class;
    
    public static function canCreate(): bool
    {
        $tenant = tenancy()->tenant;
        
        if (!$tenant) {
            return true;
        }
        
        // Check if tenant has reached product limit
        return !$tenant->hasReachedLimit('products');
    }
    
    // Alternative: Use the trait method for automatic notification
    public function mount(): void
    {
        parent::mount();
        
        // This will halt creation and show notification if limit is reached
        static::beforeCreate('products')();
    }
}
```

### In Livewire Components

```php
namespace App\Livewire;

use App\Traits\ChecksSubscriptionLimits;
use Livewire\Component;

class CreateProduct extends Component
{
    use ChecksSubscriptionLimits;
    
    public function create()
    {
        // Check limit before creating
        if (!$this->canCreate('products')) {
            return; // Notification already sent by trait
        }
        
        // Create product
        Product::create($this->form);
    }
}
```

## Displaying Upgrade Prompts

### Using the Upgrade Prompt Component

```blade
<!-- In your Blade views -->
<x-upgrade-prompt feature="products" />

<!-- This will automatically display:
     - Warning when limit is reached
     - Progress bar showing usage
     - Upgrade button
-->
```

### Manual Upgrade Prompt

```blade
@php
    $tenant = tenancy()->tenant;
    $usage = $tenant->usage;
    $plan = $tenant->subscriptionPlan;
@endphp

@if($tenant->hasReachedLimit('products'))
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-yellow-700">
                    You've reached your limit of {{ $plan->getFeatureLimit('products') }} products.
                    <a href="{{ route('filament.tenant.pages.billing') }}" class="font-medium underline">
                        Upgrade your plan
                    </a>
                    to add more products.
                </p>
            </div>
        </div>
    </div>
@endif
```

## Managing Subscriptions

### Creating a Subscription

```php
use App\Services\SubscriptionService;
use App\Models\SubscriptionPlan;

$subscriptionService = app(SubscriptionService::class);
$tenant = tenancy()->tenant;
$plan = SubscriptionPlan::where('slug', 'pro')->first();

try {
    $subscriptionService->createSubscription($tenant, $plan);
    
    // Redirect to Stripe checkout
    return $tenant->checkout([$plan->stripe_price_id], [
        'success_url' => route('billing.success'),
        'cancel_url' => route('billing.cancel'),
    ]);
} catch (\Exception $e) {
    // Handle error
    return back()->with('error', 'Failed to create subscription: ' . $e->getMessage());
}
```

### Changing Plans

```php
$subscriptionService = app(SubscriptionService::class);
$tenant = tenancy()->tenant;
$newPlan = SubscriptionPlan::where('slug', 'enterprise')->first();

try {
    $subscriptionService->changePlan($tenant, $newPlan);
    return back()->with('success', 'Plan upgraded successfully!');
} catch (\Exception $e) {
    return back()->with('error', 'Failed to upgrade plan: ' . $e->getMessage());
}
```

### Cancelling Subscription

```php
$subscriptionService = app(SubscriptionService::class);
$tenant = tenancy()->tenant;

// Cancel at period end
$subscriptionService->cancelSubscription($tenant);

// Or cancel immediately
$subscriptionService->cancelSubscription($tenant, immediately: true);
```

### Resuming Subscription

```php
$subscriptionService = app(SubscriptionService::class);
$tenant = tenancy()->tenant;

if ($tenant->subscription('default')->onGracePeriod()) {
    $subscriptionService->resumeSubscription($tenant);
    return back()->with('success', 'Subscription resumed!');
}
```

## Custom Middleware Usage

### Protecting Routes with Subscription Status

```php
// In routes/tenant.php or routes/web.php

use App\Http\Middleware\CheckTenantStatus;
use App\Http\Middleware\CheckSubscriptionFeatures;

// Require active subscription
Route::middleware(['tenant', CheckTenantStatus::class])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::resource('products', ProductController::class);
});

// Require specific feature
Route::middleware(['tenant', 'subscription.features:advanced_reports'])->group(function () {
    Route::get('/reports/advanced', [ReportController::class, 'advanced']);
});
```

### In Controllers

```php
class ReportController extends Controller
{
    public function __construct()
    {
        // Apply middleware to all methods
        $this->middleware('subscription.features:advanced_reports');
    }
    
    public function advanced()
    {
        // Only accessible if tenant has advanced_reports feature
        return view('reports.advanced');
    }
}
```

## API Responses

### Checking Limits in API

```php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $tenant = tenancy()->tenant;
        
        // Check limit
        if ($tenant->hasReachedLimit('products')) {
            return response()->json([
                'success' => false,
                'message' => 'Product limit reached',
                'data' => [
                    'limit' => $tenant->subscriptionPlan->getFeatureLimit('products'),
                    'current' => $tenant->usage->product_count,
                    'remaining' => 0,
                ],
                'upgrade_url' => route('filament.tenant.pages.billing'),
            ], 403);
        }
        
        // Create product
        $product = Product::create($request->validated());
        
        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'data' => $product,
        ], 201);
    }
}
```

## Filament Actions with Limit Checks

### Custom Create Action

```php
use Filament\Actions\CreateAction;

CreateAction::make()
    ->before(function (CreateAction $action) {
        $tenant = tenancy()->tenant;
        
        if ($tenant->hasReachedLimit('products')) {
            Notification::make()
                ->title('Limit Reached')
                ->body('Please upgrade your plan to add more products.')
                ->warning()
                ->send();
                
            $action->halt();
        }
    })
```

## Calculating Usage

### Manual Calculation

```php
use App\Tenant;

$tenant = Tenant::find('tenant-id');

if ($tenant->usage) {
    $tenant->usage->calculate();
    echo "Updated usage: {$tenant->usage->product_count} products, {$tenant->usage->user_count} users";
}
```

### Schedule Automatic Calculation

```php
// In app/Console/Kernel.php

protected function schedule(Schedule $schedule)
{
    // Calculate usage for all tenants daily at 2 AM
    $schedule->command('tenant:calculate-usage')->dailyAt('02:00');
    
    // Or every hour for more accurate tracking
    $schedule->command('tenant:calculate-usage')->hourly();
}
```

## Working with Plans in Code

### Get Plan Features

```php
$plan = SubscriptionPlan::where('slug', 'pro')->first();

// Check if feature is unlimited
if ($plan->hasUnlimitedFeature('products')) {
    echo "Unlimited products!";
}

// Get specific limit
$productLimit = $plan->getFeatureLimit('products');
echo "Product limit: " . ($productLimit === -1 ? 'Unlimited' : $productLimit);

// Get all features
foreach ($plan->features as $feature => $value) {
    echo "{$feature}: {$value}\n";
}
```

### Compare Plans

```php
$plans = SubscriptionPlan::active()->orderBy('price')->get();

foreach ($plans as $plan) {
    echo "{$plan->name} - {$plan->formatted_price} {$plan->interval_label}\n";
    echo "Products: " . ($plan->hasUnlimitedFeature('products') ? 'Unlimited' : $plan->getFeatureLimit('products')) . "\n";
    echo "---\n";
}
```

## Event Listeners

### Listen for Subscription Events

```php
// In app/Providers/EventServiceProvider.php

protected $listen = [
    'Laravel\Cashier\Events\WebhookReceived' => [
        'App\Listeners\HandleStripeWebhook',
    ],
];

// In app/Listeners/HandleStripeWebhook.php
public function handle(WebhookReceived $event)
{
    if ($event->payload['type'] === 'customer.subscription.updated') {
        $tenant = Tenant::where('stripe_id', $event->payload['data']['object']['customer'])->first();
        
        if ($tenant) {
            // Send notification
            $tenant->notify(new SubscriptionUpdated());
        }
    }
}
```

## Testing Examples

### Feature Tests

```php
use Tests\TestCase;
use App\Tenant;
use App\Models\SubscriptionPlan;

class SubscriptionTest extends TestCase
{
    public function test_tenant_can_create_products_within_limit()
    {
        $tenant = Tenant::factory()->create();
        $plan = SubscriptionPlan::factory()->create([
            'features' => ['products' => 10],
        ]);
        
        $tenant->update(['subscription_plan_id' => $plan->id]);
        
        // Create 9 products - should succeed
        for ($i = 0; $i < 9; $i++) {
            $this->assertFalse($tenant->hasReachedLimit('products'));
        }
        
        // 10th product should trigger limit
        $this->assertTrue($tenant->hasReachedLimit('products'));
    }
}
```

This document should help you implement the subscription features throughout your application!
