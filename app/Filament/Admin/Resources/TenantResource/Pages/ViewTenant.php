<?php

namespace App\Filament\Admin\Resources\TenantResource\Pages;

use App\Filament\Admin\Resources\TenantResource;
use App\Services\SubscriptionService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Log;

class ViewTenant extends ViewRecord
{
    protected static string $resource = TenantResource::class;

    public function mount(string|int $record): void
    {
        parent::mount($record);

        // Initialize tenancy for this tenant so relation managers and
        // table queries target the tenant database while the admin view
        // is active. We deliberately do not call `tenancy()->end()` here
        // so Livewire interactions keep the tenant context during the
        // page lifecycle.
        try {
            $tenant = $this->getRecord();
            if ($tenant && function_exists('tenancy')) {
                tenancy()->initialize($tenant->id);
            }
        } catch (\Throwable $e) {
            // If initialization fails, swallow the exception so the page
            // can still render; relation manager will handle missing
            // tenancy when performing mutations.
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('approve')
                ->label('Approve')
                ->color('primary')
                ->requiresConfirmation()
                ->action(function () {
                    $tenant = $this->getRecord();
                    // Basic approve: mark active and set subscription_started_at
                    $tenant->update(['is_active' => true, 'subscription_started_at' => now()]);
                    Notification::make()
                        ->title('Tenant approved')
                        ->success()
                        ->send();
                    $this->redirect($this->getResource()::getUrl('index'));
                }),

            Actions\Action::make('cancel')
                ->label('Cancel Subscription')
                ->color('danger')
                ->requiresConfirmation()
                ->action(function () {
                    $tenant = $this->getRecord();
                    try {
                        app(SubscriptionService::class)->cancelSubscription($tenant, true);
                        Notification::make()->title('Subscription cancelled')->success()->send();
                    } catch (\Throwable $e) {
                        Notification::make()->title('Failed to cancel: ' . $e->getMessage())->danger()->send();
                    }
                    $this->redirect($this->getResource()::getUrl('index'));
                }),

            Actions\Action::make('renew')
                ->label('Renew/Resume')
                ->color('success')
                ->requiresConfirmation()
                ->action(function () {
                    $tenant = $this->getRecord();
                    try {
                        app(SubscriptionService::class)->resumeSubscription($tenant);
                        Notification::make()->title('Subscription resumed')->success()->send();
                    } catch (\Throwable $e) {
                        // If resume fails, try to create a subscription for existing plan
                        try {
                            if ($tenant->subscriptionPlan) {
                                app(SubscriptionService::class)->createSubscription($tenant, $tenant->subscriptionPlan);
                                Notification::make()->title('Subscription created for tenant')->success()->send();
                            } else {
                                Notification::make()->title('No plan to subscribe the tenant to')->danger()->send();
                            }
                        } catch (\Throwable $err) {
                            Notification::make()->title('Failed to renew: ' . $err->getMessage())->danger()->send();
                        }
                    }
                    $this->redirect($this->getResource()::getUrl('index'));
                }),

            Actions\Action::make('reset')
                ->label('Reset Tenant')
                ->color('danger')
                ->requiresConfirmation()
                ->action(function () {
                    $tenant = $this->getRecord();
                    // Full tenant reset: attempt to run tenant migrations fresh to wipe tenant DB
                    try {
                        // Ensure tenancy is initialized for this tenant
                        tenancy()->initialize($tenant->id);

                        // Run migrate:fresh for tenant migrations (use tenant migrations path)
                        tenant()->run(function () {
                            \Illuminate\Support\Facades\Artisan::call('migrate:fresh', [
                                '--force' => true,
                                '--path' => 'database/migrations/tenant',
                            ]);

                            \Illuminate\Support\Facades\Artisan::call('db:seed', [
                                '--class' => 'PermissionSeeder',
                                '--force' => true,
                            ]);
                            \Illuminate\Support\Facades\Artisan::call('db:seed', [
                                '--class' => 'PaymentMethodSeeder',
                                '--force' => true,
                            ]);
                            \Illuminate\Support\Facades\Artisan::call('db:seed', [
                                '--class' => 'CategorySeeder',
                                '--force' => true,
                            ]);

                            try {
                                \App\Models\Tenants\Setting::set('language', 'ar');
                                \App\Models\Tenants\Setting::set('currency', 'LYD');
                            } catch (\Throwable $e) {
                                Log::warning('Failed to set tenant defaults after reset', [
                                    'tenant_id' => $tenant->id ?? null,
                                    'error' => $e->getMessage(),
                                ]);
                            }
                        });

                        // Also remove central tenant users and reset central usage
                        // try {
                        //     \App\TenantUser::where('tenant_id', $tenant->id)->delete();
                        // } catch (\Throwable $e) {
                        //     // continue
                        // }

                        try {
                            if ($tenant->usage) {
                                $tenant->usage->product_count = 0;
                                $tenant->usage->user_count = 0;
                                $tenant->usage->save();
                            }
                        } catch (\Throwable $e) {
                            // ignore
                        }

                        // Clear subscription on central tenant record
                        $tenant->update([
                            'subscription_plan_id' => null,
                            'is_active' => false,
                            'subscription_started_at' => null,
                            'subscription_expires_at' => null,
                            'trial_ends_at' => null,
                        ]);

                        Notification::make()->title('Tenant reset performed (full).')->success()->send();
                    } catch (\Throwable $e) {
                        Notification::make()->title('Failed to reset tenant: ' . $e->getMessage())->danger()->send();
                    } finally {
                        // Ensure tenancy context is ended if possible
                        try { tenancy()->end(); } catch (\Throwable $_) { }
                    }

                    $this->redirect($this->getResource()::getUrl('index'));
                }),
        ];
    }
}
