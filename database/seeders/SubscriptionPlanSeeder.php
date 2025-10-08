<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Basic Plan',
                'description' => 'Perfect for small businesses just getting started with waste management',
                'price' => 99000,
                'billing_cycle' => 'monthly',
                'features' => json_encode([
                    'Up to 50 orders per month',
                    'Basic tracking',
                    'Email support',
                    'Standard dashboard'
                ]),
                'max_orders_per_month' => 50,
                'max_tracking_locations' => 10,
                'priority_support' => false,
                'advanced_analytics' => false,
                'custom_branding' => false,
                'is_active' => true
            ],
            [
                'name' => 'Professional Plan',
                'description' => 'Ideal for growing businesses with enhanced features and support',
                'price' => 199000,
                'billing_cycle' => 'monthly',
                'features' => json_encode([
                    'Up to 200 orders per month',
                    'Advanced tracking with real-time updates',
                    'Priority support',
                    'Advanced analytics',
                    'Custom notifications'
                ]),
                'max_orders_per_month' => 200,
                'max_tracking_locations' => 50,
                'priority_support' => true,
                'advanced_analytics' => true,
                'custom_branding' => false,
                'is_active' => true
            ],
            [
                'name' => 'Enterprise Plan',
                'description' => 'Complete solution for large enterprises with unlimited features',
                'price' => 399000,
                'billing_cycle' => 'monthly',
                'features' => json_encode([
                    'Unlimited orders',
                    'Premium tracking with GPS accuracy',
                    '24/7 dedicated support',
                    'Advanced analytics & reporting',
                    'Custom branding',
                    'API access',
                    'Multi-location management'
                ]),
                'max_orders_per_month' => null, // unlimited
                'max_tracking_locations' => null, // unlimited
                'priority_support' => true,
                'advanced_analytics' => true,
                'custom_branding' => true,
                'is_active' => true
            ],
            [
                'name' => 'Basic Annual',
                'description' => 'Basic plan with annual billing - save 20%',
                'price' => 950000,
                'billing_cycle' => 'yearly',
                'features' => json_encode([
                    'Up to 50 orders per month',
                    'Basic tracking',
                    'Email support',
                    'Standard dashboard',
                    '20% savings with annual billing'
                ]),
                'max_orders_per_month' => 50,
                'max_tracking_locations' => 10,
                'priority_support' => false,
                'advanced_analytics' => false,
                'custom_branding' => false,
                'is_active' => true
            ],
            [
                'name' => 'Professional Annual',
                'description' => 'Professional plan with annual billing - save 20%',
                'price' => 1900000,
                'billing_cycle' => 'yearly',
                'features' => json_encode([
                    'Up to 200 orders per month',
                    'Advanced tracking with real-time updates',
                    'Priority support',
                    'Advanced analytics',
                    'Custom notifications',
                    '20% savings with annual billing'
                ]),
                'max_orders_per_month' => 200,
                'max_tracking_locations' => 50,
                'priority_support' => true,
                'advanced_analytics' => true,
                'custom_branding' => false,
                'is_active' => true
            ],
            [
                'name' => 'Enterprise Annual',
                'description' => 'Enterprise plan with annual billing - save 20%',
                'price' => 3800000,
                'billing_cycle' => 'yearly',
                'features' => json_encode([
                    'Unlimited orders',
                    'Premium tracking with GPS accuracy',
                    '24/7 dedicated support',
                    'Advanced analytics & reporting',
                    'Custom branding',
                    'API access',
                    'Multi-location management',
                    '20% savings with annual billing'
                ]),
                'max_orders_per_month' => null, // unlimited
                'max_tracking_locations' => null, // unlimited
                'priority_support' => true,
                'advanced_analytics' => true,
                'custom_branding' => true,
                'is_active' => true
            ]
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::create($plan);
        }
    }
}