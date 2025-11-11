<?php

/**
 * Comprehensive API Endpoint Testing Script
 * Tests all endpoints with proper authentication
 * 
 * Usage: php test_all_endpoints.php
 */

require __DIR__.'/vendor/autoload.php';

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Color output helpers
function success($msg) { echo "\033[32m✓ $msg\033[0m\n"; }
function error($msg) { echo "\033[31m✗ $msg\033[0m\n"; }
function info($msg) { echo "\033[36mℹ $msg\033[0m\n"; }
function section($msg) { echo "\n\033[1;33m═══ $msg ═══\033[0m\n"; }

// Test credentials
$testUser = [
    'email' => 'daffa@gmail.com',
    'password' => 'daffa123'
];

$apiUrl = 'http://127.0.0.1:8000/api';
$token = null;

// HTTP Client helper
function apiRequest($method, $endpoint, $data = [], $token = null, $headers = []) {
    global $apiUrl;
    
    $ch = curl_init();
    $url = $apiUrl . $endpoint;
    
    $defaultHeaders = [
        'Accept: application/json',
        'Content-Type: application/json'
    ];
    
    if ($token) {
        $defaultHeaders[] = "Authorization: Bearer $token";
    }
    
    $allHeaders = array_merge($defaultHeaders, $headers);
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $allHeaders);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    if ($method !== 'GET') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'code' => $httpCode,
        'body' => json_decode($response, true),
        'raw' => $response
    ];
}

// Test counter
$passed = 0;
$failed = 0;

function assertResponse($name, $response, $expectedCode = 200) {
    global $passed, $failed;
    
    if ($response['code'] === $expectedCode) {
        success("$name - Status: {$response['code']}");
        $passed++;
        return true;
    } else {
        error("$name - Expected: $expectedCode, Got: {$response['code']}");
        if (isset($response['body']['message'])) {
            error("  Error: {$response['body']['message']}");
        }
        $failed++;
        return false;
    }
}

echo "\n";
echo "╔═══════════════════════════════════════════════════════════╗\n";
echo "║       GEROBAKS API COMPREHENSIVE ENDPOINT TEST            ║\n";
echo "╚═══════════════════════════════════════════════════════════╝\n";

// Step 1: Database Setup
section('STEP 1: Database Setup');

info('Running migrations...');
try {
    Artisan::call('migrate:fresh', ['--force' => true]);
    success('Migrations completed');
} catch (Exception $e) {
    error('Migration failed: ' . $e->getMessage());
    exit(1);
}

info('Running seeders...');
try {
    Artisan::call('db:seed', ['--force' => true]);
    success('Database seeded');
} catch (Exception $e) {
    error('Seeding failed: ' . $e->getMessage());
    exit(1);
}

// Ensure test user exists
info('Setting up test user...');
$user = DB::table('users')->where('email', $testUser['email'])->first();

if (!$user) {
    DB::table('users')->insert([
        'name' => 'Daffa Test',
        'email' => $testUser['email'],
        'password' => Hash::make($testUser['password']),
        'role' => 'end_user',
        'phone' => '081234567890',
        'created_at' => now(),
        'updated_at' => now()
    ]);
    success('Test user created');
} else {
    // Update password to ensure it's correct
    DB::table('users')->where('email', $testUser['email'])->update([
        'password' => Hash::make($testUser['password']),
        'updated_at' => now()
    ]);
    success('Test user verified');
}

// Step 2: Public Endpoints
section('STEP 2: Testing Public Endpoints');

// Health check
$response = apiRequest('GET', '/health');
assertResponse('GET /health', $response);

// Ping
$response = apiRequest('GET', '/ping');
assertResponse('GET /ping', $response);

// Settings
$response = apiRequest('GET', '/settings');
assertResponse('GET /settings', $response);

// Changelog
$response = apiRequest('GET', '/changelog?page=1&per_page=5');
assertResponse('GET /changelog', $response);

// Changelog stats
$response = apiRequest('GET', '/changelog/stats');
assertResponse('GET /changelog/stats', $response);

// Step 3: Authentication
section('STEP 3: Testing Authentication');

// Login
info('Logging in with: ' . $testUser['email']);
$response = apiRequest('POST', '/login', $testUser);
if (assertResponse('POST /login', $response)) {
    // Try different token paths
    if (isset($response['body']['data']['token'])) {
        $token = $response['body']['data']['token'];
        success('Token obtained: ' . substr($token, 0, 20) . '...');
    } elseif (isset($response['body']['access_token'])) {
        $token = $response['body']['access_token'];
        success('Token obtained: ' . substr($token, 0, 20) . '...');
    } elseif (isset($response['body']['token'])) {
        $token = $response['body']['token'];
        success('Token obtained: ' . substr($token, 0, 20) . '...');
    } else {
        error('No token in response');
        print_r($response['body']);
        exit(1);
    }
}

if (assertResponse('POST /login', $response)) {
    // Try different token paths
    if (isset($response['body']['data']['token'])) {
        $token = $response['body']['data']['token'];
        $userId = $response['body']['data']['user']['id'];
        success('Token obtained: ' . substr($token, 0, 20) . '...');
    } elseif (isset($response['body']['access_token'])) {
        $token = $response['body']['access_token'];
        $userId = $response['body']['user']['id'] ?? null;
        success('Token obtained: ' . substr($token, 0, 20) . '...');
    } elseif (isset($response['body']['token'])) {
        $token = $response['body']['token'];
        $userId = $response['body']['user']['id'] ?? null;
        success('Token obtained: ' . substr($token, 0, 20) . '...');
    } else {
        error('No token in response');
        print_r($response['body']);
        exit(1);
    }
}

// Auth me
$response = apiRequest('GET', '/auth/me', [], $token);
assertResponse('GET /auth/me', $response);

// Step 4: User Endpoints
section('STEP 4: Testing User Endpoints');

// Update profile
$response = apiRequest('POST', '/user/update-profile', [
    'name' => 'Daffa Updated',
    'phone' => '081234567890'
], $token);
assertResponse('POST /user/update-profile', $response);

// Step 5: Schedules
section('STEP 5: Testing Schedule Endpoints');

// List schedules (public)
$response = apiRequest('GET', '/schedules');
assertResponse('GET /schedules', $response);

// Create schedule (authenticated)
$scheduleData = [
    'service_type' => 'waste_collection',
    'waste_type' => 'organic',
    'scheduled_at' => now()->addDays(1)->format('Y-m-d H:i:s'),
    'pickup_address' => 'Jl. Test No. 123',
    'pickup_latitude' => -6.2088,
    'pickup_longitude' => 106.8456,
    'estimated_weight' => 5.5,
    'notes' => 'Test schedule creation'
];

$response = apiRequest('POST', '/schedules', $scheduleData, $token);
if (assertResponse('POST /schedules', $response, 201)) {
    $scheduleId = $response['body']['data']['id'] ?? null;
    info("Created schedule ID: $scheduleId");
    
    // Get specific schedule
    if ($scheduleId) {
        $response = apiRequest('GET', "/schedules/$scheduleId");
        assertResponse("GET /schedules/$scheduleId", $response);
        
        // Update schedule
        $response = apiRequest('PUT', "/schedules/$scheduleId", [
            'notes' => 'Updated notes',
            'estimated_weight' => 6.0
        ], $token);
        assertResponse("PUT /schedules/$scheduleId", $response);
        
        // Cancel schedule
        $response = apiRequest('POST', "/schedules/$scheduleId/cancel", [
            'cancellation_reason' => 'Test cancellation - automated testing'
        ], $token);
        assertResponse("POST /schedules/$scheduleId/cancel", $response);
    }
}

// Step 6: Services
section('STEP 6: Testing Service Endpoints');

// List services
$response = apiRequest('GET', '/services');
assertResponse('GET /services', $response);

if (!empty($response['body']['data'])) {
    $serviceId = $response['body']['data'][0]['id'];
    $response = apiRequest('GET', "/services/$serviceId");
    assertResponse("GET /services/$serviceId", $response);
}

// Step 7: Tracking
section('STEP 7: Testing Tracking Endpoints');

// List tracking
$response = apiRequest('GET', '/tracking');
assertResponse('GET /tracking', $response);

// Step 8: Orders
section('STEP 8: Testing Order Endpoints');

// List orders (authenticated)
$response = apiRequest('GET', '/orders', [], $token);
assertResponse('GET /orders', $response);

// Create order
$orderData = [
    'user_id' => $userId ?? 1,
    'service_id' => 1,
    'address_text' => 'Jl. Order Test No. 456',
    'latitude' => -6.2088,
    'longitude' => 106.8456,
    'notes' => 'Test order'
];

$response = apiRequest('POST', '/orders', $orderData, $token);
if ($response['code'] === 201 || $response['code'] === 200) {
    assertResponse('POST /orders', $response, $response['code']);
} else {
    assertResponse('POST /orders', $response, 201);
}

// Step 9: Ratings
section('STEP 9: Testing Rating Endpoints');

// List ratings
$response = apiRequest('GET', '/ratings');
assertResponse('GET /ratings', $response);

// Step 10: Notifications
section('STEP 10: Testing Notification Endpoints');

// List notifications (authenticated)
$response = apiRequest('GET', '/notifications', [], $token);
assertResponse('GET /notifications', $response);

// Step 11: Balance
section('STEP 11: Testing Balance Endpoints');

// Get balance
$response = apiRequest('GET', '/balance', [], $token);
assertResponse('GET /balance', $response);

// Step 12: Chat
section('STEP 12: Testing Chat Endpoints');

// List conversations
$response = apiRequest('GET', '/chat/conversations', [], $token);
assertResponse('GET /chat/conversations', $response);

// Step 13: Dashboard
section('STEP 13: Testing Dashboard Endpoints');

// Get dashboard stats
$response = apiRequest('GET', '/dashboard', [], $token);
assertResponse('GET /dashboard', $response);

// Step 14: Subscription Plans
section('STEP 14: Testing Subscription Endpoints');

// List subscription plans
$response = apiRequest('GET', '/subscription-plans');
assertResponse('GET /subscription-plans', $response);

// Get user subscription
$response = apiRequest('GET', '/subscription', [], $token);
assertResponse('GET /subscription', $response);

// Step 15: Feedback
section('STEP 15: Testing Feedback Endpoints');

// List feedbacks
$response = apiRequest('GET', '/feedbacks', [], $token);
assertResponse('GET /feedbacks', $response);

// Create feedback
$feedbackData = [
    'type' => 'general',
    'title' => 'Test Feedback',
    'description' => 'This is a test feedback message from API testing',
    'rating' => 5,
    'email' => 'daffa@gmail.com'
];

$response = apiRequest('POST', '/feedbacks', $feedbackData, $token);
if ($response['code'] === 201 || $response['code'] === 200) {
    assertResponse('POST /feedbacks', $response, $response['code']);
} else {
    assertResponse('POST /feedbacks', $response, 201);
}

// Step 16: Logout
section('STEP 16: Testing Logout');

$response = apiRequest('POST', '/auth/logout', [], $token);
assertResponse('POST /auth/logout', $response);

// Summary
echo "\n";
echo "╔═══════════════════════════════════════════════════════════╗\n";
echo "║                    TEST SUMMARY                           ║\n";
echo "╚═══════════════════════════════════════════════════════════╝\n";
echo "\n";

success("Passed: $passed");
if ($failed > 0) {
    error("Failed: $failed");
} else {
    success("Failed: 0");
}

$total = $passed + $failed;
$percentage = $total > 0 ? round(($passed / $total) * 100, 2) : 0;

echo "\n";
if ($percentage >= 80) {
    success("Success Rate: $percentage% ($passed/$total)");
} else {
    error("Success Rate: $percentage% ($passed/$total)");
}

echo "\n";

if ($failed === 0) {
    echo "🎉 \033[32mALL TESTS PASSED!\033[0m 🎉\n";
    exit(0);
} else {
    echo "⚠️  \033[33mSOME TESTS FAILED\033[0m ⚠️\n";
    exit(1);
}
