<?php

use App\Models\SubscriptionPlan;
use App\Services\RegisterTenant;
use App\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

describe('Trial Period Tests', function () {
    beforeEach(function () {
        config(['tenancy.central_domains' => ['localhost.com']]);
        DB::statement('DROP DATABASE IF EXISTS lakasir_trial_test');
    });

    it('creates tenant with 1 month trial period', function () {
        $data = [
            'name' => 'trial_test',
            'domain' => 'trial_test.localhost.com',
            'email' => 'trial@test.com',
            'password' => 'password',
            'full_name' => 'Trial Test',
            'business_type' => 'Retail',
        ];

        $registerTenant = new RegisterTenant();
        $tenant = $registerTenant->create($data);

        expect($tenant)->toBeInstanceOf(Tenant::class);
        expect($tenant->trial_ends_at)->not->toBeNull();
        
        // Check that trial is approximately 30 days (accounting for slight time differences)
        $daysUntilExpiry = now()->diffInDays($tenant->trial_ends_at);
        expect($daysUntilExpiry)->toBeGreaterThanOrEqual(29);
        expect($daysUntilExpiry)->toBeLessThanOrEqual(31);
    });

    afterEach(function () {
        DB::statement('DROP DATABASE IF EXISTS lakasir_trial_test');
        Tenant::where('id', 'trial_test')->forceDelete();
    });
});

describe('Subscription Tests', function () {
    beforeEach(function () {
        config(['tenancy.central_domains' => ['localhost.com']]);
        DB::statement('DROP DATABASE IF EXISTS lakasir_sub_test');
        
        // Create a test subscription plan
        SubscriptionPlan::create([
            'name' => 'Basic Plan',
            'slug' => 'basic',
            'price' => 10.00,
            'interval' => 'month',
            'features' => ['products' => 100, 'users' => 5],
            'description' => 'Basic subscription plan',
            'is_active' => true,
            'sort_order' => 1,
        ]);
    });

    it('tenant can have an active subscription', function () {
        $plan = SubscriptionPlan::where('slug', 'basic')->first();
        
        $data = [
            'name' => 'sub_test',
            'domain' => 'sub_test.localhost.com',
            'email' => 'sub@test.com',
            'password' => 'password',
            'full_name' => 'Sub Test',
            'business_type' => 'Retail',
            'subscription_plan_id' => $plan->id,
        ];

        $registerTenant = new RegisterTenant();
        $tenant = $registerTenant->create($data);

        expect($tenant->subscription_plan_id)->toBe($plan->id);
        expect($tenant->subscriptionPlan)->toBeInstanceOf(SubscriptionPlan::class);
    });

    it('tenant on trial has active subscription', function () {
        $data = [
            'name' => 'sub_test2',
            'domain' => 'sub_test2.localhost.com',
            'email' => 'sub2@test.com',
            'password' => 'password',
            'full_name' => 'Sub Test 2',
            'business_type' => 'Retail',
        ];

        $registerTenant = new RegisterTenant();
        $tenant = $registerTenant->create($data);

        expect($tenant->onTrial())->toBeTrue();
        expect($tenant->hasActiveSubscription())->toBeTrue();
    });

    it('tenant with expired subscription is inactive', function () {
        $plan = SubscriptionPlan::where('slug', 'basic')->first();
        
        $tenant = Tenant::create([
            'id' => 'expired_test',
            'tenancy_db_name' => 'lakasir_expired_test',
            'tenancy_email' => 'expired@test.com',
            'is_active' => true,
            'trial_ends_at' => now()->subDays(5), // Expired trial
            'subscription_plan_id' => $plan->id,
            'subscription_started_at' => now()->subMonth(),
            'subscription_expires_at' => now()->subDays(1), // Expired subscription
        ]);

        expect($tenant->onTrial())->toBeFalse();
        expect($tenant->hasActiveSubscription())->toBeFalse();
    });

    afterEach(function () {
        DB::statement('DROP DATABASE IF EXISTS lakasir_sub_test');
        DB::statement('DROP DATABASE IF EXISTS lakasir_sub_test2');
        DB::statement('DROP DATABASE IF EXISTS lakasir_expired_test');
        Tenant::whereIn('id', ['sub_test', 'sub_test2', 'expired_test'])->forceDelete();
    });
});
