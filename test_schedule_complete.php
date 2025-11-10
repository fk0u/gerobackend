<?php
/**
 * COMPREHENSIVE API TEST - ALL SCHEDULE ENDPOINTS
 * Tests complete schedule lifecycle for Gerobaks API
 */

echo "🧪 COMPREHENSIVE SCHEDULE API TEST\n";
echo str_repeat("=", 80) . "\n\n";

$baseUrl = 'http://127.0.0.1:8000';
$token = null;
$mitraToken = null;
$scheduleId = null;

// Helper function to make API request
function apiRequest($method, $endpoint, $data = null, $token = null) {
    global $baseUrl;
    
    $ch = curl_init("{$baseUrl}{$endpoint}");
    
    $headers = [
        'Content-Type: application/json',
        'Accept: application/json',
    ];
    
    if ($token) {
        $headers[] = "Authorization: Bearer {$token}";
    }
    
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_CUSTOMREQUEST => $method,
    ]);
    
    if ($data && in_array($method, ['POST', 'PATCH', 'PUT'])) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    curl_close($ch);
    
    $decoded = json_decode($response, true);
    
    return [
        'code' => $httpCode,
        'type' => $contentType,
        'body' => $decoded,
        'raw' => $response,
    ];
}

// Test suite
$tests = [];
$passed = 0;
$failed = 0;

// ============================================================================
// 1. HEALTH & CONNECTIVITY
// ============================================================================
echo "📡 PHASE 1: HEALTH & CONNECTIVITY\n";
echo str_repeat("-", 80) . "\n";

$result = apiRequest('GET', '/api/health');
echo "✓ Health Check: " . ($result['code'] == 200 ? "✅ OK" : "❌ FAILED") . "\n";
if ($result['code'] != 200) $failed++; else $passed++;

$result = apiRequest('GET', '/api/ping');
echo "✓ Ping: " . ($result['code'] == 200 ? "✅ OK" : "❌ FAILED") . "\n";
if ($result['code'] != 200) $failed++; else $passed++;

echo "\n";

// ============================================================================
// 2. AUTHENTICATION
// ============================================================================
echo "🔐 PHASE 2: AUTHENTICATION\n";
echo str_repeat("-", 80) . "\n";

// Login as end_user (Daffa)
$result = apiRequest('POST', '/api/login', [
    'email' => 'daffa@gmail.com',
    'password' => 'daffa123'
]);

if ($result['code'] == 200 && isset($result['body']['data']['token'])) {
    $token = $result['body']['data']['token'];
    echo "✅ Login (End User - Daffa): SUCCESS\n";
    echo "   Token: " . substr($token, 0, 20) . "...\n";
    $passed++;
} else {
    echo "❌ Login (End User - Daffa): FAILED\n";
    echo "   Response: " . json_encode($result['body']) . "\n";
    $failed++;
}

// Login as mitra
$result = apiRequest('POST', '/api/login', [
    'email' => 'driver.jakarta@gerobaks.com',
    'password' => 'mitra123'
]);

if ($result['code'] == 200 && isset($result['body']['data']['token'])) {
    $mitraToken = $result['body']['data']['token'];
    echo "✅ Login (Mitra): SUCCESS\n";
    echo "   Token: " . substr($mitraToken, 0, 20) . "...\n";
    $passed++;
} else {
    echo "❌ Login (Mitra): FAILED\n";
    echo "   Response: " . json_encode($result['body']) . "\n";
    $failed++;
}

// Get current user
$result = apiRequest('GET', '/api/auth/me', null, $token);
if ($result['code'] == 200) {
    echo "✅ Get Current User: SUCCESS\n";
    echo "   Name: {$result['body']['data']['user']['name']}\n";
    echo "   Email: {$result['body']['data']['user']['email']}\n";
    $passed++;
} else {
    echo "❌ Get Current User: FAILED\n";
    $failed++;
}

echo "\n";

// ============================================================================
// 3. SCHEDULE MANAGEMENT (END USER)
// ============================================================================
echo "📅 PHASE 3: SCHEDULE MANAGEMENT (END USER)\n";
echo str_repeat("-", 80) . "\n";

// Create schedule (standard format)
$scheduledTime = date('Y-m-d H:i:s', strtotime('+2 days'));
$result = apiRequest('POST', '/api/schedules', [
    'service_type' => 'pickup_sampah_organik',
    'pickup_address' => 'Jl. Test Automation No. 123, Jakarta',
    'pickup_latitude' => -6.2088,
    'pickup_longitude' => 106.8456,
    'scheduled_at' => $scheduledTime,
    'notes' => 'Test schedule creation via API',
    'payment_method' => 'cash',
    'frequency' => 'once',
    'waste_type' => 'organik',
    'estimated_weight' => 5.5,
    'contact_name' => 'Daffa Test',
    'contact_phone' => '081234567890',
], $token);

if ($result['code'] == 201 && isset($result['body']['data']['id'])) {
    $scheduleId = $result['body']['data']['id'];
    echo "✅ Create Schedule (Standard Format): SUCCESS\n";
    echo "   Schedule ID: {$scheduleId}\n";
    echo "   Service Type: {$result['body']['data']['service_type']}\n";
    echo "   Status: {$result['body']['data']['status']}\n";
    $passed++;
} else {
    echo "❌ Create Schedule (Standard Format): FAILED\n";
    echo "   Response: " . json_encode($result['body']) . "\n";
    $failed++;
}

// Create schedule (mobile format)
$scheduledDate = date('Y-m-d', strtotime('+3 days'));
$scheduledTimeOnly = '14:30';
$result = apiRequest('POST', '/api/schedules/mobile', [
    'alamat' => 'Jl. Mobile App Test No. 456, Jakarta',
    'tanggal' => $scheduledDate,
    'waktu' => $scheduledTimeOnly,
    'catatan' => 'Test schedule via mobile format',
    'koordinat' => [
        'lat' => -6.2088,
        'lng' => 106.8456,
    ],
    'jenis_layanan' => 'pickup_sampah_plastik',
    'metode_pembayaran' => 'cash',
], $token);

if ($result['code'] == 201) {
    echo "✅ Create Schedule (Mobile Format): SUCCESS\n";
    echo "   Schedule ID: {$result['body']['data']['id']}\n";
    $passed++;
} else {
    echo "❌ Create Schedule (Mobile Format): FAILED\n";
    echo "   Response: " . json_encode($result['body']) . "\n";
    $failed++;
}

// List all schedules
$result = apiRequest('GET', '/api/schedules?per_page=20', null, $token);
if ($result['code'] == 200 && isset($result['body']['data']['items'])) {
    $count = count($result['body']['data']['items']);
    echo "✅ List Schedules: SUCCESS\n";
    echo "   Total Items: {$count}\n";
    echo "   Current Page: {$result['body']['data']['meta']['current_page']}\n";
    $passed++;
} else {
    echo "❌ List Schedules: FAILED\n";
    $failed++;
}

// Get single schedule
if ($scheduleId) {
    $result = apiRequest('GET', "/api/schedules/{$scheduleId}", null, $token);
    if ($result['code'] == 200) {
        echo "✅ Get Schedule Details: SUCCESS\n";
        echo "   ID: {$result['body']['data']['id']}\n";
        echo "   Address: {$result['body']['data']['pickup_address']}\n";
        $passed++;
    } else {
        echo "❌ Get Schedule Details: FAILED\n";
        $failed++;
    }
}

// Update schedule
if ($scheduleId) {
    $result = apiRequest('PATCH', "/api/schedules/{$scheduleId}", [
        'notes' => 'Updated notes via API test',
        'estimated_weight' => 7.5,
    ], $token);
    
    if ($result['code'] == 200) {
        echo "✅ Update Schedule: SUCCESS\n";
        echo "   Updated notes: {$result['body']['data']['notes']}\n";
        $passed++;
    } else {
        echo "❌ Update Schedule: FAILED\n";
        echo "   Response: " . json_encode($result['body']) . "\n";
        $failed++;
    }
}

echo "\n";

// ============================================================================
// 4. SCHEDULE LIFECYCLE (MITRA ACTIONS)
// ============================================================================
echo "🚛 PHASE 4: SCHEDULE LIFECYCLE (MITRA ACTIONS)\n";
echo str_repeat("-", 80) . "\n";

if ($scheduleId && $mitraToken) {
    // Mitra updates schedule to confirmed
    $result = apiRequest('PATCH', "/api/schedules/{$scheduleId}", [
        'status' => 'confirmed',
        'notes' => 'Mitra confirmed this schedule',
    ], $mitraToken);
    
    if ($result['code'] == 200) {
        echo "✅ Mitra Confirm Schedule: SUCCESS\n";
        echo "   Status: {$result['body']['data']['status']}\n";
        $passed++;
    } else {
        echo "❌ Mitra Confirm Schedule: FAILED\n";
        $failed++;
    }
    
    // Mitra starts schedule
    $result = apiRequest('PATCH', "/api/schedules/{$scheduleId}", [
        'status' => 'in_progress',
    ], $mitraToken);
    
    if ($result['code'] == 200) {
        echo "✅ Mitra Start Schedule: SUCCESS\n";
        echo "   Status: {$result['body']['data']['status']}\n";
        $passed++;
    } else {
        echo "❌ Mitra Start Schedule: FAILED\n";
        $failed++;
    }
    
    // Mitra completes schedule
    $result = apiRequest('POST', "/api/schedules/{$scheduleId}/complete", [
        'completion_notes' => 'Pickup completed successfully',
        'actual_duration' => 45,
    ], $mitraToken);
    
    if ($result['code'] == 200) {
        echo "✅ Mitra Complete Schedule: SUCCESS\n";
        echo "   Status: {$result['body']['data']['status']}\n";
        echo "   Completion Notes: {$result['body']['data']['completion_notes']}\n";
        $passed++;
    } else {
        echo "❌ Mitra Complete Schedule: FAILED\n";
        echo "   Response: " . json_encode($result['body']) . "\n";
        $failed++;
    }
}

// Test cancel schedule (create new one first)
$scheduledTime = date('Y-m-d H:i:s', strtotime('+5 days'));
$result = apiRequest('POST', '/api/schedules', [
    'service_type' => 'pickup_sampah_campuran',
    'pickup_address' => 'Jl. Cancel Test',
    'pickup_latitude' => -6.2088,
    'pickup_longitude' => 106.8456,
    'scheduled_at' => $scheduledTime,
], $token);

if ($result['code'] == 201) {
    $cancelScheduleId = $result['body']['data']['id'];
    
    // Cancel it
    $result = apiRequest('POST', "/api/schedules/{$cancelScheduleId}/cancel", [
        'cancellation_reason' => 'User requested cancellation via API test',
    ], $token);
    
    if ($result['code'] == 200) {
        echo "✅ Cancel Schedule: SUCCESS\n";
        echo "   Status: {$result['body']['data']['status']}\n";
        echo "   Cancellation Reason: {$result['body']['data']['cancellation_reason']}\n";
        $passed++;
    } else {
        echo "❌ Cancel Schedule: FAILED\n";
        $failed++;
    }
}

echo "\n";

// ============================================================================
// 5. FILTERING & SEARCH
// ============================================================================
echo "🔍 PHASE 5: FILTERING & SEARCH\n";
echo str_repeat("-", 80) . "\n";

// Filter by status
$result = apiRequest('GET', '/api/schedules?status=pending', null, $token);
if ($result['code'] == 200) {
    echo "✅ Filter by Status (pending): SUCCESS\n";
    $passed++;
} else {
    echo "❌ Filter by Status: FAILED\n";
    $failed++;
}

// Filter by date range
$dateFrom = date('Y-m-d');
$dateTo = date('Y-m-d', strtotime('+30 days'));
$result = apiRequest('GET', "/api/schedules?date_from={$dateFrom}&date_to={$dateTo}", null, $token);
if ($result['code'] == 200) {
    echo "✅ Filter by Date Range: SUCCESS\n";
    $passed++;
} else {
    echo "❌ Filter by Date Range: FAILED\n";
    echo "   Response: " . json_encode($result['body']) . "\n";
    $failed++;
}

echo "\n";

// ============================================================================
// SUMMARY
// ============================================================================
echo str_repeat("=", 80) . "\n";
echo "📊 TEST SUMMARY\n";
echo str_repeat("=", 80) . "\n";
$total = $passed + $failed;
$percentage = $total > 0 ? round(($passed / $total) * 100, 2) : 0;

echo "✅ Passed: {$passed}\n";
echo "❌ Failed: {$failed}\n";
echo "📈 Total:  {$total}\n";
echo "🎯 Success Rate: {$percentage}%\n";
echo str_repeat("=", 80) . "\n";

if ($failed == 0) {
    echo "\n🎉 ALL TESTS PASSED! Backend API is ready for production!\n";
    exit(0);
} else {
    echo "\n⚠️  Some tests failed. Please review the errors above.\n";
    exit(1);
}
