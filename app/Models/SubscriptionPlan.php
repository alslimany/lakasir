<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    use HasFactory;

    /**
     * The connection name for the model.
     * Subscription plans are stored in the central database.
     *
     * @var string|null
     */
    protected $connection = 'mysql';

    protected $fillable = [
        'name',
        'slug',
        'stripe_price_id',
        'price',
        'interval',
        'features',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'features' => 'array',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the tenants for this plan.
     */
    public function tenants(): HasMany
    {
        return $this->hasMany(\App\Tenant::class);
    }

    /**
     * Get feature limit value.
     */
    public function getFeatureLimit(string $feature): ?int
    {
        return $this->features[$feature] ?? null;
    }

    /**
     * Check if plan has unlimited feature.
     */
    public function hasUnlimitedFeature(string $feature): bool
    {
        $limit = $this->getFeatureLimit($feature);
        return $limit === -1 || $limit === null;
    }

    /**
     * Get formatted price.
     */
    public function getFormattedPriceAttribute(): string
    {
        return '$' . number_format($this->price, 2);
    }

    /**
     * Get interval label.
     */
    public function getIntervalLabelAttribute(): string
    {
        return match($this->interval) {
            'month' => 'Monthly',
            'year' => 'Yearly',
            default => ucfirst($this->interval),
        };
    }

    /**
     * Scope for active plans.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
