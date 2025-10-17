<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Http;

echo "========================================\n";
echo "ANALYZING ENDPOINT ISSUES\n";
echo "========================================\n\n";

$baseUrl = 'http://localhost:8000/api';

// Get tokens for all user types
$endUser = User::where('email', 'daffa@gmail.com')->first();
$endToken = $endUser->createToken('test')->plainTextToken;

$mitra = User::where('email', 'mitra@test.com')->first();
$mitraToken = $mitra->createToken('test')->plainTextToken;

$admin = User::where('email', 'admin@test.com')->first();
$adminToken = $admin->createToken('test')->plainTextToken;

echo "✅ Tokens obtained\n\n";

// Test problematic endpoints
$tests = [
    // 404 Issues
    ['GET', '/balance', $endToken, 'Balance endpoint (404 in test)'],
    ['GET', '/balance/summary', $endToken, 'Balance summary (422 in test)'],
    ['GET', '/balance/ledger', $endToken, 'Balance ledger (422 in test)'],
    ['GET', '/subscriptions', $endToken, 'Subscriptions list (404 in test)'],
    ['GET', '/subscription/plans', $endToken, 'Subscription plans (working)'],
    ['POST', '/subscriptions', $endToken, 'Subscribe endpoint (404 in test)', ['plan_id' => 1, 'payment_method' => 'credit_card']],
    ['POST', '/subscription/subscribe', $endToken, 'Alternative subscribe (working)', ['plan_id' => 1, 'payment_method' => 'credit_card']],
    ['PUT', '/notifications/mark-all-read', $endToken, 'Mark all read (404 in test)'],
    ['POST', '/notifications/mark-read', $endToken, 'Alternative mark read (working)'],
    ['GET', '/users', $adminToken, 'Users endpoint (404 in test)'],
    ['GET', '/admin/users', $adminToken, 'Admin users (working)'],
    
    // 403 Issues (permission)
    ['POST', '/tracking', $endToken, 'Tracking POST as end_user (403)', ['schedule_id' => 1, 'latitude' => -6.2, 'longitude' => 106.8, 'recorded_at' => now()->toDateTimeString()]],
    ['POST', '/tracking', $mitraToken, 'Tracking POST as mitra (should work)', ['schedule_id' => 1, 'latitude' => -6.2, 'longitude' => 106.8, 'recorded_at' => now()->toDateTimeString()]],
    ['POST', '/schedules', $endToken, 'Schedule POST as end_user (403)', ['pickup_date' => '2025-01-20', 'pickup_time' => '09:00:00', 'area' => 'Jakarta']],
    ['POST', '/schedules', $mitraToken, 'Schedule POST as mitra (should work)', ['pickup_date' => '2025-01-20', 'pickup_time' => '09:00:00', 'area' => 'Jakarta']],
];

foreach ($tests as $test) {
    list($method, $endpoint, $token, $description, $data) = array_pad($test, 5, null);
    
    echo "Testing: $description\n";
    echo "  $method $endpoint\n";
    
    try {
        $request = Http::withToken($token);
        
        if ($method === 'GET') {
            $response = $request->get("$baseUrl$endpoint");
        } elseif ($method === 'POST') {
            $response = $request->post("$baseUrl$endpoint", $data ?? []);
        } elseif ($method === 'PUT') {
            $response = $request->put("$baseUrl$endpoint", $data ?? []);
        }
        
        if ($response->successful()) {
            echo "  ✅ Status: " . $response->status() . "\n";
        } else {
            echo "  ❌ Status: " . $response->status() . "\n";
            if ($response->status() === 404) {
                echo "  ⚠️  ENDPOINT NOT FOUND - Check routes/api.php\n";
            } elseif ($response->status() === 403) {
                echo "  ⚠️  FORBIDDEN - Permission issue\n";
            } elseif ($response->status() === 422) {
                echo "  ⚠️  VALIDATION ERROR: " . json_encode($response->json()) . "\n";
            }
        }
    } catch (\Exception $e) {
        echo "  ❌ Exception: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

echo "========================================\n";
echo "ANALYSIS COMPLETE\n";
echo "========================================\n";
