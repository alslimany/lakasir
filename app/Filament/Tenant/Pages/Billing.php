<?php

namespace App\Filament\Tenant\Pages;

use App\Models\SubscriptionPlan;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class Billing extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static string $view = 'filament.tenant.pages.billing';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 100;

    public function getHeading(): string
    {
        return 'Billing & Subscription';
    }

    protected function getHeaderActions(): array
    {
        $tenant = tenancy()->tenant;

        // Allow choosing plan if on trial or subscription expired
        if (!$tenant || $tenant->onTrial() || !$tenant->hasActiveSubscription()) {
            return [
                Action::make('choosePlan')
                    ->label('Choose Plan')
                    ->icon('heroicon-o-sparkles')
                    ->color('primary')
                    ->form([
                        Select::make('plan')
                            ->label('Select Plan')
                            ->options(SubscriptionPlan::active()->pluck('name', 'id'))
                            ->required()
                            ->reactive(),
                    ])
                    ->action(function (array $data) {
                        $plan = SubscriptionPlan::find($data['plan']);
                        if (!$plan) {
                            Notification::make()
                                ->title('Plan not found')
                                ->danger()
                                ->send();
                            return;
                        }

                        // In production, this would redirect to Stripe Checkout
                        Notification::make()
                            ->title('Redirecting to payment...')
                            ->body('You will be redirected to Stripe to complete your subscription.')
                            ->success()
                            ->send();

                        // TODO: Implement Stripe Checkout redirect
                    }),
            ];
        }

        return [
            Action::make('manageBilling')
                ->label('Manage Billing')
                ->icon('heroicon-o-credit-card')
                ->color('primary')
                ->url(fn () => route('filament.tenant.billing.portal')),
        ];
    }

    public function getCurrentPlan(): ?SubscriptionPlan
    {
        $tenant = tenancy()->tenant;
        return $tenant?->subscriptionPlan;
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
