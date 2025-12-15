<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use App\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use MoamalatPay\Events\ApprovedSaleTransaction;
use MoamalatPay\Events\VerfiedTransaction;
use MoamalatPay\Models\MoamalatPayNotification;

class MoamalatWebhookController extends Controller
{
    /**
     * Listen for approved sale transactions
     */
    public function __construct()
    {
        // Listen to Moamalat events
        \Event::listen(ApprovedSaleTransaction::class, function ($event) {
            $this->handleApprovedTransaction($event->notification);
        });

        \Event::listen(VerfiedTransaction::class, function ($event) {
            Log::info('Moamalat transaction verified', [
                'notification_id' => $event->notification->id,
                'reference' => $event->notification->MerchantReference,
            ]);
        });
    }

    /**
     * Handle approved sale transaction
     */
    protected function handleApprovedTransaction(MoamalatPayNotification $notification)
    {
        try {
            // Extract tenant ID from merchant reference (format: SUB-{tenant_id}-{timestamp})
            $reference = $notification->MerchantReference;
            
            if (!$reference || !str_starts_with($reference, 'SUB-')) {
                Log::warning('Invalid merchant reference format', [
                    'reference' => $reference,
                ]);
                return;
            }

            $parts = explode('-', $reference);
            if (count($parts) < 3) {
                Log::warning('Unable to parse merchant reference', [
                    'reference' => $reference,
                ]);
                return;
            }

            $tenantId = $parts[1];
            $tenant = Tenant::find($tenantId);

            if (!$tenant) {
                Log::error('Tenant not found for payment', [
                    'tenant_id' => $tenantId,
                    'reference' => $reference,
                ]);
                return;
            }

            // Get the amount paid (convert from fils to dinar)
            $amountPaid = $notification->Amount / 1000;

            // Find the subscription plan that matches this amount from central database
            $plan = SubscriptionPlan::on('mysql')
                ->where('price', $amountPaid)
                ->where('is_active', true)
                ->first();

            if (!$plan) {
                Log::error('No matching subscription plan found for amount', [
                    'amount' => $amountPaid,
                    'tenant_id' => $tenantId,
                ]);
                return;
            }

            // Calculate subscription dates
            $now = now();
            $expiresAt = match($plan->interval) {
                'month' => $now->copy()->addMonth(),
                'year' => $now->copy()->addYear(),
                default => $now->copy()->addMonth(),
            };

            // Update tenant subscription
            $tenant->update([
                'subscription_plan_id' => $plan->id,
                'is_active' => true,
                'subscription_started_at' => $now,
                'subscription_expires_at' => $expiresAt,
            ]);

            // Calculate usage after subscription is activated
            if ($tenant->usage) {
                $tenant->usage->calculate();
            }

            Log::info('Subscription activated via webhook', [
                'tenant_id' => $tenant->id,
                'plan_id' => $plan->id,
                'amount' => $amountPaid,
                'reference' => $reference,
                'system_reference' => $notification->SystemReference,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to process Moamalat webhook', [
                'notification_id' => $notification->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
