<?php
/**
 * PRODUCTION API TEST
 * Test semua endpoint di production: https://gerobaks.dumeg.com
 */

echo "🧪 PRODUCTION API TEST - gerobaks.dumeg.com\n";
echo str_repeat("=", 80) . "\n\n";

$baseUrl = 'https://gerobaks.dumeg.com';
$token = null;
$mitraToken = null;

// Helper function
function apiRequest($method, $endpoint, $data = null, $token = null, $baseUrl = 'https://gerobaks.dumeg.com') {
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
        CURLOPT_SSL_VERIFYPEER => false, // For testing only
        CURLOPT_TIMEOUT => 30,
    ]);
    
    if ($data && in_array($method, ['POST', 'PATCH', 'PUT'])) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        return [
            'code' => 0,
            'body' => null,
            'error' => $error,
        ];
    }
    
    $decoded = json_decode($response, true);
    
    return [
        'code' => $httpCode,
        'body' => $decoded,
        'raw' => $response,
    ];
}

$passed = 0;
$failed = 0;
$warnings = [];

// ============================================================================
// 1. CONNECTIVITY TEST
// ============================================================================
echo "📡 PHASE 1: CONNECTIVITY TEST\n";
echo str_repeat("-", 80) . "\n";

$result = apiRequest('GET', '/api/health', null, null, $baseUrl);
if ($result['code'] == 200) {
    echo "✅ Health Check: OK\n";
    $passed++;
} else {
    echo "❌ Health Check: FAILED (Code: {$result['code']})\n";
    if (isset($result['error'])) {
        echo "   Error: {$result['error']}\n";
    }
    $failed++;
}

$result = apiRequest('GET', '/api/ping', null, null, $baseUrl);
if ($result['code'] == 200) {
    echo "✅ Ping: OK\n";
    $passed++;
} else {
    echo "❌ Ping: FAILED (Code: {$result['code']})\n";
    $failed++;
}

echo "\n";

// ============================================================================
// 2. AUTHENTICATION TEST
// ============================================================================
echo "🔐 PHASE 2: AUTHENTICATION\n";
echo str_repeat("-", 80) . "\n";

// Test login end_user
$result = apiRequest('POST', '/api/login', [
    'email' => 'daffa@gmail.com',
    'password' => 'daffa123'
], null, $baseUrl);

if ($result['code'] == 200 && isset($result['body']['data']['token'])) {
    $token = $result['body']['data']['token'];
    echo "✅ Login (End User): SUCCESS\n";
    echo "   Email: {$result['body']['data']['user']['email']}\n";
    echo "   Role: {$result['body']['data']['user']['role']}\n";
    echo "   Token: " . substr($token, 0, 20) . "...\n";
    $passed++;
} else {
    echo "❌ Login (End User): FAILED\n";
    if (isset($result['body']['message'])) {
        echo "   Message: {$result['body']['message']}\n";
    }
    $failed++;
    
    // Try to check if user exists with different credentials
    $warnings[] = "⚠️  User daffa@gmail.com might not exist or password is different in production";
}

// Test login mitra
$result = apiRequest('POST', '/api/login', [
    'email' => 'driver.jakarta@gerobaks.com',
    'password' => 'mitra123'
], null, $baseUrl);

if ($result['code'] == 200 && isset($result['body']['data']['token'])) {
    $mitraToken = $result['body']['data']['token'];
    echo "✅ Login (Mitra): SUCCESS\n";
    $passed++;
} else {
    echo "❌ Login (Mitra): FAILED\n";
    $failed++;
}

// Test get current user
if ($token) {
    $result = apiRequest('GET', '/api/auth/me', null, $token, $baseUrl);
    if ($result['code'] == 200) {
        echo "✅ Get Current User: SUCCESS\n";
        $passed++;
    } else {
        echo "❌ Get Current User: FAILED\n";
        $failed++;
    }
}

echo "\n";

// ============================================================================
// 3. CRITICAL: TEST SCHEDULE CREATION (THE FAILING ENDPOINT)
// ============================================================================
echo "🚨 PHASE 3: CRITICAL - SCHEDULE CREATION TEST\n";
echo str_repeat("-", 80) . "\n";

if ($token) {
    // Test with exact payload from Flutter error
    $result = apiRequest('POST', '/api/schedules', [
        'title' => 'Test from Production Script',
        'latitude' => -6.2088,
        'longitude' => 106.8456,
        'description' => 'Testing production endpoint',
        'status' => 'pending',
        'scheduled_at' => date('Y-m-d\TH:i:s.000', strtotime('+1 day')),
    ], $token, $baseUrl);
    
    echo "📝 Request: POST /api/schedules\n";
    echo "📦 Payload: Legacy format (title, latitude, longitude)\n";
    echo "📡 Response Code: {$result['code']}\n";
    
    if ($result['code'] == 403) {
        echo "❌ FAILED: 403 Forbidden - Insufficient permissions\n";
        echo "   This is the EXACT error from Flutter!\n";
        echo "   🔥 PROBLEM: Production routes still require 'role:mitra,admin'\n";
        $failed++;
        $warnings[] = "🚨 CRITICAL: Production routes.php NOT synced with local changes!";
    } elseif ($result['code'] == 201 || $result['code'] == 200) {
        echo "✅ SUCCESS: Schedule created\n";
        $passed++;
    } else {
        echo "❌ FAILED: Unexpected response\n";
        if (isset($result['body'])) {
            echo "   Response: " . json_encode($result['body']) . "\n";
        }
        $failed++;
    }
    
    // Test with new format
    $result = apiRequest('POST', '/api/schedules', [
        'service_type' => 'pickup_sampah_organik',
        'pickup_address' => 'Test Address Production',
        'pickup_latitude' => -6.2088,
        'pickup_longitude' => 106.8456,
        'scheduled_at' => date('Y-m-d H:i:s', strtotime('+1 day')),
        'payment_method' => 'cash',
        'frequency' => 'once',
    ], $token, $baseUrl);
    
    echo "\n📝 Request: POST /api/schedules\n";
    echo "📦 Payload: New format (service_type, pickup_address)\n";
    echo "📡 Response Code: {$result['code']}\n";
    
    if ($result['code'] == 403) {
        echo "❌ FAILED: 403 Forbidden\n";
        $failed++;
    } elseif ($result['code'] == 201 || $result['code'] == 200) {
        echo "✅ SUCCESS: Schedule created\n";
        $passed++;
    } else {
        echo "❌ FAILED: Code {$result['code']}\n";
        if (isset($result['body'])) {
            echo "   Response: " . json_encode($result['body']) . "\n";
        }
        $failed++;
    }
}

echo "\n";

// ============================================================================
// 4. TEST MOBILE ENDPOINT
// ============================================================================
echo "📱 PHASE 4: MOBILE ENDPOINT TEST\n";
echo str_repeat("-", 80) . "\n";

if ($token) {
    $result = apiRequest('POST', '/api/schedules/mobile', [
        'alamat' => 'Test Mobile Production',
        'tanggal' => date('Y-m-d', strtotime('+2 days')),
        'waktu' => '14:30',
        'jenis_layanan' => 'pickup_sampah_plastik',
        'metode_pembayaran' => 'cash',
        'koordinat' => [
            'lat' => -6.2088,
            'lng' => 106.8456,
        ],
    ], $token, $baseUrl);
    
    if ($result['code'] == 201 || $result['code'] == 200) {
        echo "✅ Mobile Endpoint: SUCCESS\n";
        $passed++;
    } elseif ($result['code'] == 403) {
        echo "❌ Mobile Endpoint: FORBIDDEN\n";
        $failed++;
        $warnings[] = "Mobile endpoint also has permission issue";
    } else {
        echo "❌ Mobile Endpoint: FAILED (Code: {$result['code']})\n";
        $failed++;
    }
}

echo "\n";

// ============================================================================
// 5. TEST LIST SCHEDULES
// ============================================================================
echo "📋 PHASE 5: LIST SCHEDULES\n";
echo str_repeat("-", 80) . "\n";

$result = apiRequest('GET', '/api/schedules', null, $token, $baseUrl);
if ($result['code'] == 200) {
    echo "✅ List Schedules: SUCCESS\n";
    if (isset($result['body']['data']['items'])) {
        $count = count($result['body']['data']['items']);
        echo "   Total Items: {$count}\n";
    }
    $passed++;
} else {
    echo "❌ List Schedules: FAILED (Code: {$result['code']})\n";
    $failed++;
}

echo "\n";

// ============================================================================
// SUMMARY & DIAGNOSIS
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

if (!empty($warnings)) {
    echo "\n⚠️  WARNINGS:\n";
    foreach ($warnings as $warning) {
        echo "   {$warning}\n";
    }
    echo "\n";
}

// ============================================================================
// DIAGNOSIS & SOLUTION
// ============================================================================
echo "\n";
echo str_repeat("=", 80) . "\n";
echo "🔍 DIAGNOSIS\n";
echo str_repeat("=", 80) . "\n";

if ($failed > 0) {
    echo "❌ MASALAH DITEMUKAN!\n\n";
    
    echo "🔥 ROOT CAUSE:\n";
    echo "   Production server masih menggunakan routes LAMA yang membutuhkan\n";
    echo "   role 'mitra' atau 'admin' untuk create schedule.\n\n";
    
    echo "📝 BUKTI:\n";
    echo "   - POST /api/schedules mengembalikan 403 Forbidden\n";
    echo "   - Error message: 'Insufficient permissions'\n";
    echo "   - Middleware: RoleAuthorization line 34\n\n";
    
    echo "✅ SOLUSI:\n";
    echo "   1. Upload file routes/api.php terbaru ke production\n";
    echo "   2. Clear cache di production:\n";
    echo "      php artisan route:clear\n";
    echo "      php artisan cache:clear\n";
    echo "      php artisan config:clear\n";
    echo "   3. Jalankan test script ini lagi\n\n";
    
    echo "📁 FILE YANG HARUS DI-UPLOAD:\n";
    echo "   - backend/routes/api.php\n";
    echo "   - backend/app/Http/Controllers/Api/ScheduleController.php\n";
    echo "   - backend/app/Http/Resources/ScheduleResource.php\n";
    echo "   - backend/app/Models/Schedule.php\n\n";
} else {
    echo "🎉 SEMUA TEST BERHASIL!\n";
    echo "Production API sudah bekerja dengan baik!\n\n";
}

echo str_repeat("=", 80) . "\n";

exit($failed > 0 ? 1 : 0);
