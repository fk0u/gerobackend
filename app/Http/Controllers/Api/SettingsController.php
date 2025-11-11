<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    use ApiResponseTrait;

    /**
     * Get application settings
     */
    public function index()
    {
        $settings = [
            'app_name' => config('app.name'),
            'app_version' => '1.0.0',
            'api_version' => 'v1',
            'maintenance_mode' => false,
            'supported_payment_methods' => ['cash', 'transfer', 'wallet'],
            'supported_service_types' => [
                'organic_waste',
                'plastic_waste',
                'paper_waste',
                'metal_waste',
                'electronic_waste',
                'mixed_waste'
            ],
            'default_estimation_duration' => 60, // minutes
            'max_estimation_duration' => 480, // 8 hours
            'min_estimation_duration' => 15, // 15 minutes
        ];

        return $this->successResponse($settings, 'Settings retrieved successfully');
    }

    /**
     * Update application settings (admin only)
     */
    public function update(Request $request)
    {
        // Only admin can update settings
        if (!$request->user()->isAdmin()) {
            return $this->forbiddenResponse('Only admin can update settings');
        }

        $data = $request->validate([
            'maintenance_mode' => 'sometimes|boolean',
            'app_version' => 'sometimes|string',
        ]);

        // In a real application, you'd save these to database or cache
        // For now, we'll just return success
        
        return $this->successResponse($data, 'Settings updated successfully');
    }

    /**
     * Get admin panel settings
     */
    public function admin(Request $request)
    {
        $adminSettings = [
            'system' => [
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
                'database' => config('database.default'),
                'cache_driver' => config('cache.default'),
                'queue_driver' => config('queue.default'),
            ],
            'stats' => [
                'total_users' => \App\Models\User::count(),
                'total_schedules' => \App\Models\Schedule::count(),
                'total_orders' => \App\Models\Order::count(),
            ],
            'config' => [
                'app_env' => config('app.env'),
                'app_debug' => config('app.debug'),
                'app_url' => config('app.url'),
            ],
        ];

        return $this->successResponse($adminSettings, 'Admin settings retrieved successfully');
    }

    /**
     * Get API configuration for mobile app
     */
    public function apiConfig()
    {
        $config = [
            'base_url' => config('app.url'),
            'api_url' => config('app.url') . '/api',
            'timeout' => 30000, // 30 seconds
            'retry_attempts' => 3,
            'encryption' => [
                'enabled' => true,
                'algorithm' => 'AES-256-CBC',
            ],
        ];

        return $this->successResponse($config, 'API configuration retrieved successfully');
    }
}