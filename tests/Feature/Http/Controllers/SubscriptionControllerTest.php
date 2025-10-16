<?php

use App\Http\Controllers\SubscriptionController;
use App\Models\SubscriptionPlan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;

use function Pest\Laravel\{postJson, get};

uses(RefreshDatabase::class);

describe('Subscription Controller Tests', function () {
    beforeEach(function () {
        // Register the subscription routes for testing
        Route::post('/test/subscription/initiate', [SubscriptionController::class, 'initiatePayment']);
        Route::get('/test/subscription/success', [SubscriptionController::class, 'handleSuccess']);
        Route::get('/test/subscription/cancel', [SubscriptionController::class, 'handleCancel']);
        
        // Create a test subscription plan
        $this->plan = SubscriptionPlan::create([
            'name' => 'Test Plan',
            'slug' => 'test-plan',
            'price' => 25.50,
            'interval' => 'month',
            'features' => ['products' => 500, 'users' => 10],
            'description' => 'Test subscription plan',
            'is_active' => true,
            'sort_order' => 1,
        ]);
    });

    it('returns payment initiation data for valid plan', function () {
        $response = postJson('/test/subscription/initiate', [
            'plan_id' => $this->plan->id,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'amount',
                'reference',
                'plan' => ['id', 'name', 'price'],
            ]);

        // Check that amount is in fils (1000 fils = 1 dinar)
        $expectedAmount = (int)($this->plan->price * 1000);
        expect($response->json('amount'))->toBe($expectedAmount);
        expect($response->json('plan.id'))->toBe($this->plan->id);
    })->skip('requires tenant context');

    it('returns error for invalid plan', function () {
        $response = postJson('/test/subscription/initiate', [
            'plan_id' => 99999,
        ]);

        $response->assertStatus(404);
    })->skip('requires tenant context');

    it('handles payment cancellation', function () {
        session(['pending_subscription_plan_id' => $this->plan->id]);

        $response = get('/test/subscription/cancel');

        expect(session('pending_subscription_plan_id'))->toBeNull();
    })->skip('requires tenant context');
});
