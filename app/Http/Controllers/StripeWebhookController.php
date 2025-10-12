<?php

namespace App\Http\Controllers;

use App\Tenant;
use Illuminate\Http\Request;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierController;

class StripeWebhookController extends CashierController
{
    /**
     * Handle subscription created.
     */
    public function handleCustomerSubscriptionCreated(array $payload): void
    {
        $data = $payload['data']['object'];
        
        if ($tenant = $this->getTenant($data['customer'])) {
            // Update tenant status
            $tenant->update(['is_active' => true]);
            
            // Calculate usage after subscription is created
            if ($tenant->usage) {
                $tenant->usage->calculate();
            }
        }
    }

    /**
     * Handle subscription updated.
     */
    public function handleCustomerSubscriptionUpdated(array $payload): void
    {
        $data = $payload['data']['object'];
        
        if ($tenant = $this->getTenant($data['customer'])) {
            $status = $data['status'];
            
            // Update tenant status based on subscription status
            $tenant->update([
                'is_active' => in_array($status, ['active', 'trialing']),
            ]);
        }
    }

    /**
     * Handle subscription deleted.
     */
    public function handleCustomerSubscriptionDeleted(array $payload): void
    {
        $data = $payload['data']['object'];
        
        if ($tenant = $this->getTenant($data['customer'])) {
            // Deactivate tenant when subscription is deleted
            $tenant->update(['is_active' => false]);
        }
    }

    /**
     * Handle invoice payment succeeded.
     */
    public function handleInvoicePaymentSucceeded(array $payload): void
    {
        $data = $payload['data']['object'];
        
        if ($tenant = $this->getTenant($data['customer'])) {
            // Send notification to tenant about successful payment
            // You can implement this using Laravel notifications
        }
    }

    /**
     * Handle invoice payment failed.
     */
    public function handleInvoicePaymentFailed(array $payload): void
    {
        $data = $payload['data']['object'];
        
        if ($tenant = $this->getTenant($data['customer'])) {
            // Send notification to tenant about failed payment
            // You can implement this using Laravel notifications
        }
    }

    /**
     * Get tenant from Stripe customer ID.
     */
    protected function getTenant(string $customerId): ?Tenant
    {
        return Tenant::where('stripe_id', $customerId)->first();
    }
}
