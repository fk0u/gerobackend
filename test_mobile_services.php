<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Http;

echo "========================================\n";
echo "MOBILE SERVICES - COMPREHENSIVE TEST\n";
echo "All Endpoints with Correct Paths & Roles\n";
echo "========================================\n\n";

$baseUrl = 'http://localhost:8000/api';

// Login all users
echo "1. AUTHENTICATION\n";
echo "==================\n";

$endUser = User::where('email', 'daffa@gmail.com')->first();
$endToken = $endUser->createToken('mobile-test')->plainTextToken;
echo "✅ End User (daffa@gmail.com) - Token: " . substr($endToken, 0, 20) . "...\n";

$mitra = User::where('email', 'mitra@test.com')->first();
$mitraToken = $mitra->createToken('mobile-test')->plainTextToken;
echo "✅ Mitra (mitra@test.com) - Token: " . substr($mitraToken, 0, 20) . "...\n";

$admin = User::where('email', 'admin@test.com')->first();
$adminToken = $admin->createToken('mobile-test')->plainTextToken;
echo "✅ Admin (admin@test.com) - Token: " . substr($adminToken, 0, 20) . "...\n\n";

$passCount = 3; // Login
$failCount = 0;
$totalTests = 0;

// Helper function
function test($name, $method, $endpoint, $token, $data = null) {
    global $baseUrl, $passCount, $failCount, $totalTests;
    $totalTests++;
    
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
test("GET /tracking", "GET", "/tracking", $endToken);
test("POST /tracking (as mitra)", "POST", "/tracking", $mitraToken, [
    'schedule_id' => 1,
    'latitude' => -6.2,
    'longitude' => 106.816666,
    'recorded_at' => now()->toDateTimeString()
]);

// 3. RATING
echo "\n3. RATING SERVICE\n";
echo "==================\n";
test("GET /ratings", "GET", "/ratings", $endToken);
test("POST /ratings", "POST", "/ratings", $endToken, [
    'order_id' => 1,
    'rating' => 5,
    'comment' => 'Great service!'
]);

// 4. CHAT
echo "\n4. CHAT SERVICE\n";
echo "==================\n";
test("GET /chats", "GET", "/chats", $endToken);
test("POST /chats", "POST", "/chats", $endToken, [
    'receiver_id' => $mitra->id,
    'message' => 'Hello from mobile test!'
]);

// 5. PAYMENT
echo "\n5. PAYMENT SERVICE\n";
echo "==================\n";
test("GET /payments", "GET", "/payments", $endToken);
test("POST /payments", "POST", "/payments", $endToken, [
    'order_id' => 1,
    'amount' => 50000,
    'payment_method' => 'cash'
]);

// 6. BALANCE (CORRECT PATHS)
echo "\n6. BALANCE SERVICE\n";
echo "==================\n";
test("GET /balance/summary", "GET", "/balance/summary", $endToken);
test("GET /balance/ledger", "GET", "/balance/ledger", $endToken);
test("POST /balance/topup", "POST", "/balance/topup", $endToken, [
    'amount' => 100000,
    'payment_method' => 'transfer'
]);

// 7. SCHEDULE
echo "\n7. SCHEDULE SERVICE\n";
echo "==================\n";
test("GET /schedules", "GET", "/schedules", $endToken);
test("POST /schedules (as mitra)", "POST", "/schedules", $mitraToken, [
    'pickup_date' => '2025-01-20',
    'pickup_time' => '09:00:00',
    'area' => 'Jakarta Selatan'
]);

// 8. ORDER
echo "\n8. ORDER SERVICE\n";
echo "==================\n";
test("GET /orders", "GET", "/orders", $endToken);
test("POST /orders", "POST", "/orders", $endToken, [
    'service_id' => 1,
    'schedule_id' => 3,
    'address_text' => 'Test Address from Mobile',
    'latitude' => -6.2,
    'longitude' => 106.8
]);

// 9. NOTIFICATION (CORRECT PATH)
echo "\n9. NOTIFICATION SERVICE\n";
echo "==================\n";
test("GET /notifications", "GET", "/notifications", $endToken);
test("POST /notifications/mark-read", "POST", "/notifications/mark-read", $endToken);

// 10. SUBSCRIPTION (CORRECT PATHS)
echo "\n10. SUBSCRIPTION SERVICE\n";
echo "==================\n";
test("GET /subscription/plans", "GET", "/subscription/plans", $endToken);
test("POST /subscription/subscribe", "POST", "/subscription/subscribe", $endToken, [
    'plan_id' => 1,
    'payment_method' => 'credit_card'
]);

// 11. FEEDBACK
echo "\n11. FEEDBACK SERVICE\n";
echo "==================\n";
test("GET /feedback", "GET", "/feedback", $endToken);
test("POST /feedback", "POST", "/feedback", $endToken, [
    'subject' => 'Mobile App Feedback',
    'message' => 'Testing from mobile comprehensive test'
]);

// 12. ADMIN (CORRECT PATH)
echo "\n12. ADMIN SERVICE\n";
echo "==================\n";
test("GET /admin/users (as admin)", "GET", "/admin/users", $adminToken);

// SUMMARY
echo "\n========================================\n";
echo "FINAL SUMMARY\n";
echo "========================================\n";
echo "Total Tests: $totalTests\n";
echo "Passed: $passCount\n";
echo "Failed: $failCount\n";
$passRate = round(($passCount / ($totalTests + 3)) * 100, 2); // +3 for login
echo "Pass Rate: $passRate%\n";

if ($failCount === 0) {
    echo "\n🎉🎉🎉 100% PASS RATE - ALL MOBILE ENDPOINTS WORKING! 🎉🎉🎉\n";
} else {
    echo "\n⚠️ $failCount tests failed\n";
}
