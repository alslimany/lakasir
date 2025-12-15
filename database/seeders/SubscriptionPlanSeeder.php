<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Free (Ads)',
                'slug' => 'free-ads',
                'price' => 0,
                'interval' => 'month',
                'description' => 'Free plan supported by ads',
                'features' => [
                    'products' => 50,
                    'users' => 1,
                    'storage' => 536870912, // 512MB in bytes
                    'ads' => true,
                ],
                'is_active' => true,
                'ads_enabled' => true,
                'sort_order' => 0,
            ],
            [
                'name' => 'Basic',
                'slug' => 'basic',
                'price' => 29.00,
                'interval' => 'month',
                'description' => 'Perfect for small businesses getting started',
                'features' => [
                    'products' => 100,
                    'users' => 2,
                    'storage' => 1073741824, // 1GB in bytes
                    'reports' => 'basic',
                    'support' => 'email',
                ],
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro',
                'price' => 99.00,
                'interval' => 'month',
                'description' => 'For growing businesses with advanced needs',
                'features' => [
                    'products' => 1000,
                    'users' => 10,
                    'storage' => 10737418240, // 10GB in bytes
                    'reports' => 'advanced',
                    'analytics' => true,
                    'support' => 'priority',
                ],
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'price' => 299.00,
                'interval' => 'month',
                'description' => 'Unlimited everything for large businesses',
                'features' => [
                    'products' => -1, // Unlimited
                    'users' => -1, // Unlimited
                    'storage' => -1, // Unlimited
                    'reports' => 'advanced',
                    'analytics' => true,
                    'support' => 'dedicated',
                    'custom_integrations' => true,
                    'white_label' => true,
                ],
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::updateOrCreate(
                ['slug' => $plan['slug']],
                $plan
            );
        }
    }
}
