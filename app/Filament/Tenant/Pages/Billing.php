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
        $tenant = tenancy()->tenant;

        // Allow choosing plan if on trial or subscription expired
        if (!$tenant || $tenant->onTrial() || !$tenant->hasActiveSubscription()) {
            return [
                Action::make('choosePlan')
                    ->label(__('Choose Plan'))
                    ->icon('heroicon-o-sparkles')
                    ->color('primary')
                    ->form([
                        Select::make('plan')
                            ->label(__('Select Plan'))
                            ->options(SubscriptionPlan::on('mysql')->active()->pluck('name', 'id'))
                            ->required()
                            ->reactive(),
                    ])
                    ->action(function (array $data) {
                        $plan = SubscriptionPlan::on('mysql')->find($data['plan']);
                        if (!$plan) {
                            Notification::make()
                                ->title(__('Plan not found'))
                                ->danger()
                                ->send();
                            return;
                        }

                        // Store plan in session and redirect to payment page
                        session(['selected_plan_id' => $plan->id]);
                        
                        Notification::make()
                            ->title(__('Redirecting to payment...'))
                            ->body(__('You will be redirected to complete your payment.'))
                            ->success()
                            ->send();
                        
                        // Redirect to the payment page where Moamalat will be initialized
                        $this->redirect(route('filament.tenant.pages.billing'));
                    }),
            ];
        }

        return [
            Action::make('manageBilling')
                ->label(__('Manage Subscription'))
                ->icon('heroicon-o-credit-card')
                ->color('primary')
                ->form([
                    Select::make('plan')
                        ->label(__('Select New Plan'))
                        ->options(SubscriptionPlan::on('mysql')->active()->pluck('name', 'id'))
                        ->required()
                        ->reactive(),
                ])
                ->action(function (array $data) {
                    $plan = SubscriptionPlan::on('mysql')->find($data['plan']);
                    if (!$plan) {
                        Notification::make()
                            ->title(__('Plan not found'))
                            ->danger()
                            ->send();
                        return;
                    }

                    // Store plan in session and redirect to payment page
                    session(['selected_plan_id' => $plan->id]);
                    
                    Notification::make()
                        ->title(__('Redirecting to payment...'))
                        ->body(__('You will be redirected to complete your payment.'))
                        ->success()
                        ->send();
                    
                    $this->redirect(route('filament.tenant.pages.billing'));
                }),
        ];
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
        
        // Don't show trial info if tenant has an active paid subscription
        if (!$tenant || !$tenant->onTrial()) {
            return null;
        }
        
        // Don't show trial if user has an active paid subscription
        if ($tenant->subscription_plan_id && $tenant->subscription_started_at) {
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
        return SubscriptionPlan::on('mysql')->active()->orderBy('sort_order')->get();
    }

    public function isSubscriptionExpired(): bool
    {
        $tenant = tenancy()->tenant;
        return $tenant && !$tenant->hasActiveSubscription() && !$tenant->onTrial();
    }

    public function getSelectedPlan(): ?SubscriptionPlan
    {
        $planId = session('selected_plan_id');
        if ($planId) {
            return SubscriptionPlan::on('mysql')->find($planId);
        }
        return null;
    }

    public function clearSelectedPlan()
    {
        session()->forget('selected_plan_id');
        $this->redirect(route('filament.tenant.pages.billing'));
    }
}
