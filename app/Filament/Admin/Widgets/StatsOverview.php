<?php

namespace App\Filament\Admin\Widgets;

use App\Tenant;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalTenants = Tenant::count();
        $activeTenants = Tenant::where('is_active', true)->count();
        $onTrial = Tenant::whereNotNull('trial_ends_at')
            ->where('trial_ends_at', '>', now())
            ->count();
        
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
        ];
    }
}
