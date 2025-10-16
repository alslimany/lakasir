<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use App\Services\SubscriptionService;
use App\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    protected $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Initiate subscription payment with Moamalat
     */
    public function initiatePayment(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|integer',
        ]);

        $tenant = tenancy()->tenant;
        
        // Query from central database explicitly
        $plan = SubscriptionPlan::on('mysql')->findOrFail($request->plan_id);

        if (!$tenant) {
            return response()->json([
                'error' => 'Tenant not found'
            ], 404);
        }

        // Store the pending subscription plan in session
        session([
            'pending_subscription_plan_id' => $plan->id,
            'pending_subscription_amount' => $plan->price,
        ]);

        // Convert price to fils (Libyan Dirham smallest unit)
        // 1 Dinar = 1000 Fils
        $amountInFils = (int)($plan->price * 1000);

        // Generate unique merchant reference
        $merchantReference = 'SUB-' . $tenant->id . '-' . time();

        return response()->json([
            'success' => true,
            'amount' => $amountInFils,
            'reference' => $merchantReference,
            'plan' => [
                'id' => $plan->id,
                'name' => $plan->name,
                'price' => $plan->price,
            ],
        ]);
    }

    /**
     * Handle successful payment callback
     */
    public function handleSuccess(Request $request)
    {
        $tenant = tenancy()->tenant;
        $planId = session('pending_subscription_plan_id');

        if (!$tenant || !$planId) {
            return redirect()->route('filament.tenant.pages.billing')
                ->with('error', 'Invalid payment session');
        }

        try {
            // Query from central database explicitly
            $plan = SubscriptionPlan::on('mysql')->findOrFail($planId);
            
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

            // Clear session
            session()->forget(['pending_subscription_plan_id', 'pending_subscription_amount']);

            Log::info('Subscription activated', [
                'tenant_id' => $tenant->id,
                'plan_id' => $plan->id,
                'started_at' => $now,
                'expires_at' => $expiresAt,
            ]);

            return redirect()->route('filament.tenant.pages.billing')
                ->with('success', 'Subscription activated successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to activate subscription', [
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('filament.tenant.pages.billing')
                ->with('error', 'Failed to activate subscription');
        }
    }

    /**
     * Handle cancelled payment
     */
    public function handleCancel(Request $request)
    {
        // Clear session
        session()->forget(['pending_subscription_plan_id', 'pending_subscription_amount']);

        return redirect()->route('filament.tenant.pages.billing')
            ->with('warning', 'Payment was cancelled');
    }
}
