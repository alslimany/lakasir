<?php

namespace App\Filament\Tenant\Pages;

use App\Models\SubscriptionPlan;
use Filament\Pages\Page;

class Billing extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static string $view = 'filament.tenant.pages.billing';

    protected static ?string $navigationLabel = 'Billing';

    public static function getLabel(): string
    {
        return __('Billing');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Settings');
    }

    protected static ?int $navigationSort = 100;

    public function getHeading(): string
    {
        return __('Billing & Subscription');
    }

    protected function getHeaderActions(): array
    {
        // Subscription management is handled by system admin only
        // Tenants can only view their subscription information
        return [];
    }

    public function getCurrentPlan(): ?SubscriptionPlan
    {
        $tenant = tenancy()->tenant;
        return $tenant?->subscriptionPlan;
    }

    public function getSubscriptionInfo(): ?array
    {
        $tenant = tenancy()->tenant;
        if (!$tenant || !$tenant->subscriptionPlan) {
            return null;
        }

        return [
            'started_at' => $tenant->subscription_started_at,
            'expires_at' => $tenant->subscription_expires_at,
            'is_expired' => $tenant->subscription_expires_at && $tenant->subscription_expires_at->isPast(),
        ];
    }

    public function getTrialInfo(): ?array
    {
        $tenant = tenancy()->tenant;
        if (!$tenant || !$tenant->onTrial()) {
            return null;
        }

        return [
            'ends_at' => $tenant->trial_ends_at,
            'days_left' => now()->diffInDays($tenant->trial_ends_at, false),
        ];
    }

    public function getUsageInfo(): ?array
    {
        $tenant = tenancy()->tenant;
        $usage = $tenant?->usage;
        $plan = $tenant?->subscriptionPlan;

        if (!$usage || !$plan) {
            return null;
        }

        return [
            'products' => [
                'current' => $usage->product_count,
                'limit' => $plan->getFeatureLimit('products'),
                'unlimited' => $plan->hasUnlimitedFeature('products'),
            ],
            'users' => [
                'current' => $usage->user_count,
                'limit' => $plan->getFeatureLimit('users'),
                'unlimited' => $plan->hasUnlimitedFeature('users'),
            ],
            'storage' => [
                'current' => $usage->formatted_storage,
                'used_bytes' => $usage->storage_used,
            ],
        ];
    }

    public function getAvailablePlans()
    {
        return SubscriptionPlan::active()->orderBy('sort_order')->get();
    }

    public function isSubscriptionExpired(): bool
    {
        $tenant = tenancy()->tenant;
        return $tenant && !$tenant->hasActiveSubscription() && !$tenant->onTrial();
    }
}
