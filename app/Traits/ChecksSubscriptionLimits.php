<?php

namespace App\Traits;

use Filament\Notifications\Notification;

trait ChecksSubscriptionLimits
{
    /**
     * Check if tenant can create more records of a feature.
     */
    protected function canCreate(string $feature): bool
    {
        $tenant = tenancy()->tenant;

        if (!$tenant) {
            return true;
        }

        if ($tenant->hasReachedLimit($feature)) {
            $this->sendLimitNotification($feature);
            return false;
        }

        return true;
    }

    /**
     * Send limit reached notification.
     */
    protected function sendLimitNotification(string $feature): void
    {
        $tenant = tenancy()->tenant;
        $plan = $tenant?->subscriptionPlan;
        $limit = $plan?->getFeatureLimit($feature);

        $featureName = match($feature) {
            'products' => 'products',
            'users' => 'users',
            default => $feature,
        };

        Notification::make()
            ->title('Limit Reached')
            ->body("You've reached your plan limit of {$limit} {$featureName}. Please upgrade your plan to add more.")
            ->warning()
            ->persistent()
            ->actions([
                \Filament\Notifications\Actions\Action::make('upgrade')
                    ->button()
                    ->url(route('filament.tenant.pages.billing')),
            ])
            ->send();
    }

    /**
     * Check limit before creating record (use in Filament Resource).
     */
    public static function beforeCreate(string $feature): \Closure
    {
        return function () use ($feature) {
            $tenant = tenancy()->tenant;

            if (!$tenant || !$tenant->hasReachedLimit($feature)) {
                return;
            }

            $plan = $tenant->subscriptionPlan;
            $limit = $plan?->getFeatureLimit($feature);

            $featureName = match($feature) {
                'products' => 'products',
                'users' => 'users',
                default => $feature,
            };

            Notification::make()
                ->title('Limit Reached')
                ->body("You've reached your plan limit of {$limit} {$featureName}. Please upgrade your plan to add more.")
                ->warning()
                ->persistent()
                ->actions([
                    \Filament\Notifications\Actions\Action::make('upgrade')
                        ->button()
                        ->url(route('filament.tenant.pages.billing')),
                ])
                ->send();

            // Halt the creation
            halt();
        };
    }
}
