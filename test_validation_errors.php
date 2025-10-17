<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Http;

echo "=== TESTING VALIDATION ERRORS ===\n\n";

// Get end_user token
$endUser = User::where('email', 'daffa@gmail.com')->first();
$endToken = $endUser->createToken('test-validation')->plainTextToken;

// Get mitra token
$mitra = User::where('email', 'mitra@test.com')->first();
$mitraToken = $mitra->createToken('test-mitra')->plainTextToken;

$baseUrl = 'http://localhost:8000/api';

// Test 1: POST /ratings
echo "1. POST /ratings\n";
$response = Http::withToken($endToken)->post("$baseUrl/ratings", [
    'order_id' => 1,
    'rating' => 5,
    'comment' => 'Great service!'
]);
echo "Status: " . $response->status() . "\n";
if ($response->failed()) {
    echo "Error: " . $response->body() . "\n";
}
echo "\n";

// Test 2: POST /chats
echo "2. POST /chats\n";
$response = Http::withToken($endToken)->post("$baseUrl/chats", [
    'receiver_id' => $mitra->id,
    'message' => 'Hello mitra!'
]);
echo "Status: " . $response->status() . "\n";
if ($response->failed()) {
    echo "Error: " . $response->body() . "\n";
}
echo "\n";

// Test 3: POST /payments
echo "3. POST /payments\n";
$response = Http::withToken($endToken)->post("$baseUrl/payments", [
    'order_id' => 1,
    'amount' => 50000,
    'payment_method' => 'cash'
]);
echo "Status: " . $response->status() . "\n";
if ($response->failed()) {
    echo "Error: " . $response->body() . "\n";
}
echo "\n";

// Test 4: GET /balance/summary
echo "4. GET /balance/summary\n";
$response = Http::withToken($endToken)->get("$baseUrl/balance/summary");
echo "Status: " . $response->status() . "\n";
if ($response->failed()) {
    echo "Error: " . $response->body() . "\n";
}
echo "\n";

// Test 5: GET /balance/ledger
echo "5. GET /balance/ledger\n";
$response = Http::withToken($endToken)->get("$baseUrl/balance/ledger");
echo "Status: " . $response->status() . "\n";
if ($response->failed()) {
    echo "Error: " . $response->body() . "\n";
}
echo "\n";

// Test 6: POST /balance/topup
echo "6. POST /balance/topup\n";
$response = Http::withToken($endToken)->post("$baseUrl/balance/topup", [
    'amount' => 100000,
    'payment_method' => 'transfer'
]);
echo "Status: " . $response->status() . "\n";
if ($response->failed()) {
    echo "Error: " . $response->body() . "\n";
}
echo "\n";

// Test 7: POST /schedules
echo "7. POST /schedules (as MITRA)\n";
$response = Http::withToken($mitraToken)->post("$baseUrl/schedules", [
    'pickup_date' => '2025-01-20',
    'pickup_time' => '09:00:00',
    'area' => 'Jakarta Selatan'
]);
echo "Status: " . $response->status() . "\n";
if ($response->failed()) {
    echo "Error: " . $response->body() . "\n";
}
echo "\n";

// Test 8: POST /orders
echo "8. POST /orders\n";
$response = Http::withToken($endToken)->post("$baseUrl/orders", [
    'service_id' => 1,
    'schedule_id' => 3,
    'address_text' => 'Test Address',
    'latitude' => -6.2,
    'longitude' => 106.8
]);
echo "Status: " . $response->status() . "\n";
if ($response->failed()) {
    echo "Error: " . $response->body() . "\n";
}
echo "\n";

// Test 9: POST /subscription/subscribe
echo "9. POST /subscription/subscribe\n";
$response = Http::withToken($endToken)->post("$baseUrl/subscription/subscribe", [
    'plan_id' => 1,
    'payment_method' => 'credit_card'
]);
echo "Status: " . $response->status() . "\n";
if ($response->failed()) {
    echo "Error: " . $response->body() . "\n";
}
echo "\n";

// Test 10: POST /feedback
echo "10. POST /feedback\n";
$response = Http::withToken($endToken)->post("$baseUrl/feedback", [
    'subject' => 'Test Feedback',
    'message' => 'This is a test feedback message'
]);
echo "Status: " . $response->status() . "\n";
if ($response->failed()) {
    echo "Error: " . $response->body() . "\n";
}
echo "\n";

echo "✅ All validation tests completed!\n";
