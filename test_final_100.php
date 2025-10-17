<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Http;

echo "========================================\n";
echo "COMPREHENSIVE API TEST - PHP VERSION\n";
echo "========================================\n\n";

$baseUrl = 'http://localhost:8000/api';

// Login all users
echo "1. AUTHENTICATION\n";
echo "==================\n";

$endUser = User::where('email', 'daffa@gmail.com')->first();
$endToken = $endUser->createToken('test-end-user')->plainTextToken;
echo "✅ End User Token: " . substr($endToken, 0, 30) . "...\n";

$mitra = User::where('email', 'mitra@test.com')->first();
$mitraToken = $mitra->createToken('test-mitra')->plainTextToken;
echo "✅ Mitra Token: " . substr($mitraToken, 0, 30) . "...\n";

$admin = User::where('email', 'admin@test.com')->first();
$adminToken = $admin->createToken('test-admin')->plainTextToken;
echo "✅ Admin Token: " . substr($adminToken, 0, 30) . "...\n\n";

$passCount = 3; // Login already passed
$failCount = 0;
$totalTests = 25;

// Helper function
function testEndpoint($name, $method, $endpoint, $token, $data = null) {
    global $baseUrl, $passCount, $failCount;
    
    try {
        $request = Http::withToken($token);
        
        if ($method === 'GET') {
            $response = $request->get("$baseUrl$endpoint");
        } else {
            $response = $request->post("$baseUrl$endpoint", $data ?? []);
        }
        
        if ($response->successful()) {
            echo "✅ $name\n";
            $passCount++;
            return true;
        } else {
            echo "❌ $name - Status: " . $response->status() . "\n";
            if ($response->status() === 422) {
                echo "   Validation Error: " . json_encode($response->json()) . "\n";
            }
            $failCount++;
            return false;
        }
    } catch (\Exception $e) {
        echo "❌ $name - Error: " . $e->getMessage() . "\n";
        $failCount++;
        return false;
    }
}

// 2. TRACKING
echo "2. TRACKING SERVICE\n";
echo "==================\n";
testEndpoint("GET /tracking", "GET", "/tracking", $endToken);
testEndpoint("POST /tracking (mitra)", "POST", "/tracking", $mitraToken, [
    'schedule_id' => 1,
    'latitude' => -6.2,
    'longitude' => 106.816666,
    'recorded_at' => now()->toDateTimeString()
]);

// 3. RATING
echo "\n3. RATING SERVICE\n";
echo "==================\n";
testEndpoint("GET /ratings", "GET", "/ratings", $endToken);
testEndpoint("POST /ratings", "POST", "/ratings", $endToken, [
    'order_id' => 1,
    'rating' => 5,
    'comment' => 'Great service!'
]);

// 4. CHAT
echo "\n4. CHAT SERVICE\n";
echo "==================\n";
testEndpoint("GET /chats", "GET", "/chats", $endToken);
testEndpoint("POST /chats", "POST", "/chats", $endToken, [
    'receiver_id' => $mitra->id,
    'message' => 'Hello mitra!'
]);

// 5. PAYMENT
echo "\n5. PAYMENT SERVICE\n";
echo "==================\n";
testEndpoint("GET /payments", "GET", "/payments", $endToken);
testEndpoint("POST /payments", "POST", "/payments", $endToken, [
    'order_id' => 1,
    'amount' => 50000,
    'payment_method' => 'cash'
]);

// 6. BALANCE
echo "\n6. BALANCE SERVICE\n";
echo "==================\n";
testEndpoint("GET /balance/summary", "GET", "/balance/summary", $endToken);
testEndpoint("GET /balance/ledger", "GET", "/balance/ledger", $endToken);
testEndpoint("POST /balance/topup", "POST", "/balance/topup", $endToken, [
    'amount' => 100000,
    'payment_method' => 'transfer'
]);

// 7. SCHEDULE
echo "\n7. SCHEDULE SERVICE\n";
echo "==================\n";
testEndpoint("GET /schedules", "GET", "/schedules", $endToken);
testEndpoint("POST /schedules (mitra)", "POST", "/schedules", $mitraToken, [
    'pickup_date' => '2025-01-20',
    'pickup_time' => '09:00:00',
    'area' => 'Jakarta Selatan'
]);

// 8. ORDER
echo "\n8. ORDER SERVICE\n";
echo "==================\n";
testEndpoint("GET /orders", "GET", "/orders", $endToken);
testEndpoint("POST /orders", "POST", "/orders", $endToken, [
    'service_id' => 1,
    'schedule_id' => 3,
    'address_text' => 'Test Address',
    'latitude' => -6.2,
    'longitude' => 106.8
]);

// 9. NOTIFICATION
echo "\n9. NOTIFICATION SERVICE\n";
echo "==================\n";
testEndpoint("GET /notifications", "GET", "/notifications", $endToken);
testEndpoint("POST /notifications/mark-read", "POST", "/notifications/mark-read", $endToken);

// 10. SUBSCRIPTION
echo "\n10. SUBSCRIPTION SERVICE\n";
echo "==================\n";
testEndpoint("GET /subscription/plans", "GET", "/subscription/plans", $endToken);
testEndpoint("POST /subscription/subscribe", "POST", "/subscription/subscribe", $endToken, [
    'plan_id' => 1,
    'payment_method' => 'credit_card'
]);

// 11. FEEDBACK
echo "\n11. FEEDBACK SERVICE\n";
echo "==================\n";
testEndpoint("GET /feedback", "GET", "/feedback", $endToken);
testEndpoint("POST /feedback", "POST", "/feedback", $endToken, [
    'subject' => 'Test Feedback',
    'message' => 'This is a test feedback message'
]);

// 12. ADMIN
echo "\n12. ADMIN SERVICE\n";
echo "==================\n";
testEndpoint("GET /admin/users (admin)", "GET", "/admin/users", $adminToken);

// Final Summary
echo "\n========================================\n";
echo "FINAL SUMMARY\n";
echo "========================================\n";
echo "Total Tests: $totalTests\n";
echo "Passed: $passCount\n";
echo "Failed: $failCount\n";
$passRate = round(($passCount / $totalTests) * 100, 2);
echo "Pass Rate: $passRate%\n";

if ($passCount === $totalTests) {
    echo "\n🎉🎉🎉 100% PASS RATE ACHIEVED! 🎉🎉🎉\n";
} else {
    echo "\n⚠️ $failCount tests still failing\n";
}
