<?php

namespace App\Filament\Admin\Widgets;

use App\Tenant;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Constants\TenantDefaults;
use Illuminate\Support\Number;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalTenants = Tenant::count();
        $activeTenants = Tenant::where('is_active', true)->count();
        $onTrial = Tenant::whereNotNull('trial_ends_at')
            ->where('trial_ends_at', '>', now())
            ->count();
        $paidTenants = Tenant::where('is_active', true)
            ->whereHas('subscriptionPlan', fn ($query) => $query->where('price', '>', 0))
            ->count();
        $freeTenants = Tenant::whereHas('subscriptionPlan', fn ($query) => $query->where('price', '=', 0))
            ->count();
        $subscriptionRevenue = Tenant::where('is_active', true)
            ->whereHas('subscriptionPlan', fn ($query) => $query->where('price', '>', 0))
            ->with('subscriptionPlan')
            ->get()
            ->sum(function ($tenant) {
                $plan = $tenant->subscriptionPlan;
                if (!$plan) {
                    return 0;
                }

                $price = $plan->price ?? 0;
                return $plan->interval === 'year' ? $price / 12 : $price;
            });
        
        return [
            Stat::make('Total Tenants', $totalTenants)
                ->description('All registered tenants')
                ->descriptionIcon('heroicon-o-building-office')
                ->color('primary'),
            
            Stat::make('Active Tenants', $activeTenants)
                ->description('Currently active')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),
            
            Stat::make('On Trial', $onTrial)
                ->description('Trial period active')
                ->descriptionIcon('heroicon-o-clock')
                ->color('warning'),
            
            Stat::make('Paid Subscriptions', $paidTenants)
                ->description('Active paid tenants')
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('success'),

            Stat::make('Free Subscriptions', $freeTenants)
                ->description('Tenants on the ad-supported plan')
                ->descriptionIcon('heroicon-o-megaphone')
                ->color('gray'),

            Stat::make('Subscription Revenue', Number::currency($subscriptionRevenue, config('cashier.currency', TenantDefaults::CURRENCY)))
                ->description('Monthly recurring from paid plans')
                ->descriptionIcon('heroicon-o-chart-bar')
                ->color('primary'),
        ];
    }
}
