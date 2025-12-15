<?php

namespace App;

use App\Models\SubscriptionPlan;
use App\Models\TenantUsage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Cashier\Billable;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Stancl\Tenancy\Database\Models\Domain> $domains
 * @property-read int|null $domains_count
 * @method static \Stancl\Tenancy\Database\TenantCollection<int, static> all($columns = ['*'])
 * @method static \Stancl\Tenancy\Database\TenantCollection<int, static> get($columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant query()
 * @method static \Stancl\Tenancy\Database\TenantCollection<int, static> all($columns = ['*'])
 * @method static \Stancl\Tenancy\Database\TenantCollection<int, static> get($columns = ['*'])
 * @mixin \Eloquent
 * @mixin IdeHelperTenant
 */
class Tenant extends BaseTenant implements TenantWithDatabase
{
    use Billable, HasDatabase, HasDomains;

    protected $hidden = [
        'tenancy_db_profile_password',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'trial_ends_at' => 'datetime',
        'subscription_started_at' => 'datetime',
        'subscription_expires_at' => 'datetime',
    ];

    /**
     * Get the subscription plan for this tenant.
     */
    public function subscriptionPlan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    /**
     * Get the usage statistics for this tenant.
     */
    public function usage(): HasOne
    {
        return $this->hasOne(TenantUsage::class);
    }

    /**
     * Check if tenant is on trial.
     */
    public function onTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    /**
     * Check if tenant has active subscription or trial.
     */
    public function hasActiveSubscription(): bool
    {
        // Check if on trial first
        if ($this->onTrial()) {
            return true;
        }

        // Check if subscription is active and not expired
        if (!$this->is_active) {
            return false;
        }

        // If using Cashier (Stripe)
        if ($this->subscribed('default')) {
            return true;
        }

        // If using subscription_expires_at (Moamalat or other)
        if ($this->subscription_expires_at) {
            return $this->subscription_expires_at->isFuture();
        }

        // If has a subscription plan but no expiry date, consider it active
        return $this->subscription_plan_id !== null;
    }

    /**
     * Check if tenant can use feature.
     */
    public function canUseFeature(string $feature): bool
    {
        if (!$this->hasActiveSubscription()) {
            return false;
        }

        if ($this->onTrial()) {
            return true;
        }

        $plan = $this->subscriptionPlan;
        if (!$plan) {
            return false;
        }

        return $plan->hasUnlimitedFeature($feature);
    }

    /**
     * Check if tenant has reached feature limit.
     */
    public function hasReachedLimit(string $feature): bool
    {
        if (!$this->hasActiveSubscription()) {
            return true;
        }

        if ($this->onTrial()) {
            return false;
        }

        $plan = $this->subscriptionPlan;
        if (!$plan || $plan->hasUnlimitedFeature($feature)) {
            return false;
        }

        $limit = $plan->getFeatureLimit($feature);
        $usage = $this->usage;

        if (!$usage || !$limit) {
            return false;
        }

        return match($feature) {
            'products' => $usage->product_count >= $limit,
            'users' => $usage->user_count >= $limit,
            default => false,
        };
    }

    /**
     * Get remaining feature count.
     */
    public function getRemainingFeatureCount(string $feature): ?int
    {
        $plan = $this->subscriptionPlan;
        if (!$plan) {
            return null;
        }

        if ($plan->hasUnlimitedFeature($feature)) {
            return -1; // Unlimited
        }

        $limit = $plan->getFeatureLimit($feature);
        $usage = $this->usage;

        if (!$usage || !$limit) {
            return null;
        }

        return max(0, $limit - match($feature) {
            'products' => $usage->product_count,
            'users' => $usage->user_count,
            default => 0,
        });
    }
}
