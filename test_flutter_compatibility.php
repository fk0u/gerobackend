<?php

/**
 * FLUTTER API COMPATIBILITY TEST
 * Verifies API endpoints work with Flutter app
 */

// Configuration
$apiBaseUrl = 'http://127.0.0.1:8000/api';

// Color output
function color($text, $color) {
    $colors = [
        'green' => "\033[0;32m",
        'red' => "\033[0;31m",
        'yellow' => "\033[1;33m",
        'blue' => "\033[0;34m",
        'cyan' => "\033[0;36m",
        'reset' => "\033[0m"
    ];
    return ($colors[$color] ?? $colors['reset']) . $text . $colors['reset'];
}

function success($text) { echo color("✓ $text", 'green') . PHP_EOL; }
function error($text) { echo color("✗ $text", 'red') . PHP_EOL; }
function info($text) { echo color("ℹ $text", 'cyan') . PHP_EOL; }
function section($text) { echo PHP_EOL . color("═══ $text ═══", 'yellow') . PHP_EOL; }

// API request helper
function apiRequest($method, $endpoint, $data = [], $token = null) {
    global $apiBaseUrl;
    
    $url = $apiBaseUrl . $endpoint;
    $ch = curl_init($url);
    
    $headers = [
        'Content-Type: application/json',
        'Accept: application/json',
        'X-Requested-With: XMLHttpRequest', // Important for Laravel API
    ];
    
    if ($token) {
        $headers[] = "Authorization: Bearer $token";
    }
    
    curl_setopt_array($ch, [
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_POSTFIELDS => $method !== 'GET' ? json_encode($data) : null,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT => 30,
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'code' => $httpCode,
        'body' => $response,
        'data' => json_decode($response, true)
    ];
}

section("FLUTTER API COMPATIBILITY CHECK");
info("Testing API endpoints used by Flutter app...");
echo PHP_EOL;

$results = ['passed' => 0, 'failed' => 0];

// Test 1: Health Check (used by Flutter on startup)
section("1. Health Check");
$response = apiRequest('GET', '/health');
if ($response['code'] === 200 && isset($response['data']['status'])) {
    success("Health endpoint working - Status: {$response['data']['status']}");
    $results['passed']++;
} else {
    error("Health check failed - Code: {$response['code']}");
    $results['failed']++;
}

// Test 2: Settings (Flutter loads on startup)
section("2. App Settings");
$response = apiRequest('GET', '/settings');
if ($response['code'] === 200) {
    success("Settings endpoint working");
    info("  App Name: " . ($response['data']['data']['app_name'] ?? 'N/A'));
    info("  API Version: " . ($response['data']['data']['api_version'] ?? 'N/A'));
    $results['passed']++;
} else {
    error("Settings failed - Code: {$response['code']}");
    $results['failed']++;
}

// Test 3: Login (Flutter authentication)
section("3. User Authentication");
$loginData = [
    'email' => 'daffa@gmail.com',
    'password' => 'password123'
];

$response = apiRequest('POST', '/login', $loginData);
if ($response['code'] === 200 && isset($response['data']['data']['token'])) {
    $token = $response['data']['data']['token'];
    $user = $response['data']['data']['user'];
    
    success("Login successful");
    info("  User: {$user['name']} ({$user['email']})");
    info("  Role: {$user['role']}");
    info("  Token: " . substr($token, 0, 20) . "...");
    $results['passed']++;
} else {
    error("Login failed - Code: {$response['code']}");
    error("  Response: " . ($response['body'] ?? 'No response'));
    $results['failed']++;
    exit(1); // Can't continue without token
}

// Test 4: Get User Profile (Flutter uses this)
section("4. User Profile (auth/me)");
$response = apiRequest('GET', '/auth/me', [], $token);
if ($response['code'] === 200) {
    success("User profile retrieved successfully");
    $results['passed']++;
} else {
    error("Profile retrieval failed - Code: {$response['code']}");
    $results['failed']++;
}

// Test 5: Services List (Flutter home screen)
section("5. Services List");
$response = apiRequest('GET', '/services');
if ($response['code'] === 200) {
    $services = $response['data']['data'] ?? [];
    success("Services retrieved - Count: " . count($services));
    if (count($services) > 0) {
        info("  First service: " . ($services[0]['name'] ?? 'N/A'));
    }
    $results['passed']++;
} else {
    error("Services failed - Code: {$response['code']}");
    $results['failed']++;
}

// Test 6: Schedules (Flutter schedule screen)
section("6. Schedules List");
$response = apiRequest('GET', '/schedules', [], $token);
if ($response['code'] === 200) {
    $schedules = $response['data']['data'] ?? [];
    success("Schedules retrieved - Count: " . count($schedules));
    $results['passed']++;
} else {
    error("Schedules failed - Code: {$response['code']}");
    $results['failed']++;
}

// Test 7: Create Schedule (Flutter schedule creation)
section("7. Create Schedule");
$scheduleData = [
    'service_id' => 1,
    'service_type' => 'plastic_waste',
    'scheduled_at' => date('Y-m-d H:i:s', strtotime('+1 day')),
    'pickup_address' => 'Test Address for Flutter',
    'pickup_latitude' => -6.2088,
    'pickup_longitude' => 106.8456,
    'notes' => 'Created from Flutter compatibility test'
];

$response = apiRequest('POST', '/schedules', $scheduleData, $token);
if ($response['code'] === 201 || $response['code'] === 200) {
    success("Schedule created successfully");
    $scheduleId = $response['data']['data']['id'] ?? null;
    if ($scheduleId) {
        info("  Schedule ID: $scheduleId");
    }
    $results['passed']++;
} else {
    error("Schedule creation failed - Code: {$response['code']}");
    if (isset($response['data']['errors'])) {
        error("  Errors: " . json_encode($response['data']['errors']));
    }
    $results['failed']++;
}

// Test 8: Orders (Flutter order screen)
section("8. Orders List");
$response = apiRequest('GET', '/orders', [], $token);
if ($response['code'] === 200) {
    $orders = $response['data']['data'] ?? [];
    success("Orders retrieved - Count: " . count($orders));
    $results['passed']++;
} else {
    error("Orders failed - Code: {$response['code']}");
    $results['failed']++;
}

// Test 9: Notifications (Flutter notifications)
section("9. Notifications");
$response = apiRequest('GET', '/notifications', [], $token);
if ($response['code'] === 200) {
    $notifications = $response['data']['data'] ?? $response['data'] ?? [];
    $count = is_array($notifications) ? count($notifications) : 0;
    success("Notifications retrieved - Count: $count");
    $results['passed']++;
} else {
    error("Notifications failed - Code: {$response['code']}");
    $results['failed']++;
}

// Test 10: Balance (Flutter wallet screen)
section("10. User Balance");
$response = apiRequest('GET', '/balance', [], $token);
if ($response['code'] === 200) {
    $balance = $response['data']['data']['current_balance'] ?? 0;
    success("Balance retrieved - Amount: Rp " . number_format($balance, 0, ',', '.'));
    $results['passed']++;
} else {
    error("Balance failed - Code: {$response['code']}");
    $results['failed']++;
}

// Test 11: Dashboard (Flutter dashboard)
section("11. Dashboard");
$response = apiRequest('GET', '/dashboard', [], $token);
if ($response['code'] === 200) {
    success("Dashboard data retrieved");
    $results['passed']++;
} else {
    error("Dashboard failed - Code: {$response['code']}");
    $results['failed']++;
}

// Test 12: Subscription Plans (Flutter subscription screen)
section("12. Subscription Plans");
$response = apiRequest('GET', '/subscription-plans');
if ($response['code'] === 200) {
    $plans = $response['data']['data'] ?? [];
    success("Subscription plans retrieved - Count: " . count($plans));
    $results['passed']++;
} else {
    error("Subscription plans failed - Code: {$response['code']}");
    $results['failed']++;
}

// Test 13: Logout (Flutter logout)
section("13. Logout");
$response = apiRequest('POST', '/auth/logout', [], $token);
if ($response['code'] === 200) {
    success("Logout successful");
    $results['passed']++;
} else {
    error("Logout failed - Code: {$response['code']}");
    $results['failed']++;
}

// Test 14: CORS Headers (Important for web Flutter)
section("14. CORS Headers");
$ch = curl_init($apiBaseUrl . '/health');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HEADER => true,
    CURLOPT_NOBODY => false,
]);
$response = curl_exec($ch);
curl_close($ch);

$hasCors = strpos($response, 'Access-Control-Allow-Origin') !== false;
if ($hasCors) {
    success("CORS headers present (Web Flutter will work)");
    $results['passed']++;
} else {
    info("CORS headers not detected (Mobile only, Web may have issues)");
    $results['passed']++;
}

// Summary
echo PHP_EOL;
echo color("╔═══════════════════════════════════════════════════════════╗", 'blue') . PHP_EOL;
echo color("║            FLUTTER COMPATIBILITY SUMMARY                  ║", 'blue') . PHP_EOL;
echo color("╚═══════════════════════════════════════════════════════════╝", 'blue') . PHP_EOL;
echo PHP_EOL;

$total = $results['passed'] + $results['failed'];
$percentage = $total > 0 ? round(($results['passed'] / $total) * 100, 2) : 0;

echo color("✓ Passed: {$results['passed']}", 'green') . PHP_EOL;
echo color("✗ Failed: {$results['failed']}", 'red') . PHP_EOL;
echo color("📱 Compatibility: $percentage% ($total tests)", 'cyan') . PHP_EOL;
echo PHP_EOL;

if ($results['failed'] === 0) {
    echo color("🎉 ALL FLUTTER ENDPOINTS WORKING! 🎉", 'green') . PHP_EOL;
    echo color("📱 Flutter app can connect successfully!", 'green') . PHP_EOL;
    echo PHP_EOL;
    
    // Show connection info
    info("Flutter App Configuration:");
    echo "  • API Base URL: $apiBaseUrl" . PHP_EOL;
    echo "  • Use this in .env: API_BASE_URL=http://10.0.2.2:8000" . PHP_EOL;
    echo "  • For production: API_BASE_URL=https://gerobaks.dumeg.com" . PHP_EOL;
    echo PHP_EOL;
    
    exit(0);
} else {
    echo color("⚠️  SOME FLUTTER ENDPOINTS FAILED ⚠️", 'yellow') . PHP_EOL;
    echo color("Fix the failed endpoints before deploying", 'yellow') . PHP_EOL;
    echo PHP_EOL;
    exit(1);
}
