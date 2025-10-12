<?php

namespace App\Services;

use App\Models\SubscriptionPlan;
use App\Tenant;
use Illuminate\Support\Facades\Log;

class SubscriptionService
{
    /**
     * Create a subscription for a tenant.
     */
    public function createSubscription(Tenant $tenant, SubscriptionPlan $plan): void
    {
        try {
            // If tenant doesn't have a Stripe customer, create one
            if (!$tenant->hasStripeId()) {
                $tenant->createAsStripeCustomer([
                    'name' => $tenant->id,
                    'email' => $tenant->tenancy_email,
                ]);
            }

            // Create subscription in Stripe
            if ($plan->stripe_price_id) {
                $tenant->newSubscription('default', $plan->stripe_price_id)->create();
            }

            // Update tenant's plan
            $tenant->update([
                'subscription_plan_id' => $plan->id,
                'is_active' => true,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create subscription', [
                'tenant_id' => $tenant->id,
                'plan_id' => $plan->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Change tenant's subscription plan.
     */
    public function changePlan(Tenant $tenant, SubscriptionPlan $newPlan): void
    {
        try {
            $subscription = $tenant->subscription('default');

            if (!$subscription) {
                // If no subscription exists, create a new one
                $this->createSubscription($tenant, $newPlan);
                return;
            }

            // Swap to new plan
            if ($newPlan->stripe_price_id) {
                $subscription->swap($newPlan->stripe_price_id);
            }

            // Update tenant's plan
            $tenant->update([
                'subscription_plan_id' => $newPlan->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to change plan', [
                'tenant_id' => $tenant->id,
                'new_plan_id' => $newPlan->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Cancel tenant's subscription.
     */
    public function cancelSubscription(Tenant $tenant, bool $immediately = false): void
    {
        try {
            $subscription = $tenant->subscription('default');

            if (!$subscription) {
                return;
            }

            if ($immediately) {
                $subscription->cancelNow();
                $tenant->update(['is_active' => false]);
            } else {
                $subscription->cancel();
            }
        } catch (\Exception $e) {
            Log::error('Failed to cancel subscription', [
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Resume a cancelled subscription.
     */
    public function resumeSubscription(Tenant $tenant): void
    {
        try {
            $subscription = $tenant->subscription('default');

            if (!$subscription || !$subscription->onGracePeriod()) {
                throw new \Exception('No subscription to resume');
            }

            $subscription->resume();
        } catch (\Exception $e) {
            Log::error('Failed to resume subscription', [
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Check if tenant needs to upgrade for a feature.
     */
    public function needsUpgrade(Tenant $tenant, string $feature): bool
    {
        if (!$tenant->hasActiveSubscription()) {
            return true;
        }

        return $tenant->hasReachedLimit($feature);
    }
}
