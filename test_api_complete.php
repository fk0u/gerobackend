<?php

echo "\n";
echo "==============================================\n";
echo " 🧪 TESTING MITRA ROLE API ENDPOINTS\n";
echo "==============================================\n\n";

// Get token
echo "📝 [1/6] Getting Mitra authentication token...\n";
require __DIR__ . '/vendor/autoload.php';
use App\Models\User;
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$mitra = User::where('email', 'driver.jakarta@gerobaks.com')->first();
if (!$mitra) {
    echo "❌ Mitra not found!\n";
    exit(1);
}

$token = $mitra->createToken('test-api')->plainTextToken;
echo "   ✅ Token: " . substr($token, 0, 30) . "...\n\n";

sleep(2); // Wait for server

$baseUrl = 'http://127.0.0.1:8000/api';
$headers = [
    'Authorization: Bearer ' . $token,
    'Accept: application/json',
    'Content-Type: application/json',
];

// Test 1: Accept Schedule
echo "🔵 [2/6] Testing ACCEPT endpoint (Schedule ID: 10)...\n";
$ch = curl_init("$baseUrl/schedules/10/accept");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $data = json_decode($response, true);
    echo "   ✅ SUCCESS: Status = " . ($data['data']['status'] ?? 'N/A') . "\n";
    echo "   📊 Response: " . substr($response, 0, 100) . "...\n\n";
} else {
    echo "   ❌ FAILED: HTTP $httpCode\n";
    echo "   Response: $response\n\n";
}

sleep(1);

// Test 2: Start Schedule
echo "🟢 [3/6] Testing START endpoint (Schedule ID: 11)...\n";
$ch = curl_init("$baseUrl/schedules/11/start");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $data = json_decode($response, true);
    echo "   ✅ SUCCESS: Status = " . ($data['data']['status'] ?? 'N/A') . "\n";
    echo "   📊 Response: " . substr($response, 0, 100) . "...\n\n";
} else {
    echo "   ❌ FAILED: HTTP $httpCode\n";
    echo "   Response: $response\n\n";
}

sleep(1);

// Test 3: Complete Schedule
echo "🟡 [4/6] Testing COMPLETE endpoint (Schedule ID: 12)...\n";
$ch = curl_init("$baseUrl/schedules/12/complete");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'actual_weight' => 10.5,
    'completion_notes' => 'Test berhasil dari API testing script',
]));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $data = json_decode($response, true);
    echo "   ✅ SUCCESS: Status = " . ($data['data']['status'] ?? 'N/A') . "\n";
    echo "   📊 Actual Weight: " . ($data['data']['actual_weight'] ?? 'N/A') . " kg\n";
    echo "   📊 Response: " . substr($response, 0, 100) . "...\n\n";
} else {
    echo "   ❌ FAILED: HTTP $httpCode\n";
    echo "   Response: $response\n\n";
}

sleep(1);

// Test 4: Get All Schedules
echo "📋 [5/6] Testing GET schedules endpoint...\n";
$ch = curl_init("$baseUrl/schedules");
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $data = json_decode($response, true);
    $count = count($data['data'] ?? []);
    echo "   ✅ SUCCESS: Found $count schedules\n";
    echo "   📊 Response: " . substr($response, 0, 100) . "...\n\n";
} else {
    echo "   ❌ FAILED: HTTP $httpCode\n";
    echo "   Response: $response\n\n";
}

sleep(1);

// Test 5: Verify Database Changes
echo "💾 [6/6] Verifying database changes...\n";
use App\Models\Schedule;

$schedule10 = Schedule::find(10);
$schedule11 = Schedule::find(11);
$schedule12 = Schedule::find(12);

if ($schedule10) {
    echo "   Schedule 10: Status = {$schedule10->status}, Mitra = " . ($schedule10->mitra_id ?? 'NULL') . "\n";
}
if ($schedule11) {
    echo "   Schedule 11: Status = {$schedule11->status}, Started = " . ($schedule11->started_at ? $schedule11->started_at->format('H:i:s') : 'NULL') . "\n";
}
if ($schedule12) {
    echo "   Schedule 12: Status = {$schedule12->status}, Completed = " . ($schedule12->completed_at ? $schedule12->completed_at->format('H:i:s') : 'NULL') . ", Weight = " . ($schedule12->actual_weight ?? 'NULL') . " kg\n";
}

echo "\n";
echo "==============================================\n";
echo " ✅ API TESTING COMPLETE!\n";
echo "==============================================\n\n";

echo "📊 SUMMARY:\n";
echo "   - Accept endpoint: " . ($schedule10 && $schedule10->status === 'confirmed' ? '✅ WORKING' : '❌ FAILED') . "\n";
echo "   - Start endpoint: " . ($schedule11 && $schedule11->status === 'in_progress' ? '✅ WORKING' : '❌ FAILED') . "\n";
echo "   - Complete endpoint: " . ($schedule12 && $schedule12->status === 'completed' ? '✅ WORKING' : '❌ FAILED') . "\n";
echo "\n";
