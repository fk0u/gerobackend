<?php
/**
 * Comprehensive API Test Script
 * Tests API endpoints and verifies responses are JSON, not HTML
 */

echo "🧪 COMPREHENSIVE API TEST\n";
echo str_repeat("=", 70) . "\n\n";

$baseUrl = 'http://127.0.0.1:8000';

// Test cases
$tests = [
    [
        'name' => 'Health Check',
        'method' => 'GET',
        'endpoint' => '/api/health',
        'data' => null,
        'expect_json' => true,
    ],
    [
        'name' => 'Ping',
        'method' => 'GET',
        'endpoint' => '/api/ping',
        'data' => null,
        'expect_json' => true,
    ],
    [
        'name' => 'Login (Valid)',
        'method' => 'POST',
        'endpoint' => '/api/login',
        'data' => ['email' => 'daffa@gmail.com', 'password' => 'password123'],
        'expect_json' => true,
    ],
    [
        'name' => 'Login (Invalid)',
        'method' => 'POST',
        'endpoint' => '/api/login',
        'data' => ['email' => 'wrong@test.com', 'password' => 'wrongpass'],
        'expect_json' => true,
    ],
    [
        'name' => 'Register (New User)',
        'method' => 'POST',
        'endpoint' => '/api/register',
        'data' => [
            'name' => 'Test User ' . time(),
            'email' => 'test' . time() . '@example.com',
            'password' => 'password123',
        ],
        'expect_json' => true,
    ],
];

foreach ($tests as $test) {
    echo "📋 Test: {$test['name']}\n";
    echo "   URL: {$baseUrl}{$test['endpoint']}\n";
    echo "   Method: {$test['method']}\n";
    
    $ch = curl_init("{$baseUrl}{$test['endpoint']}");
    
    $headers = [
        'Content-Type: application/json',
        'Accept: application/json',
    ];
    
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_CUSTOMREQUEST => $test['method'],
    ]);
    
    if ($test['data'] && $test['method'] === 'POST') {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($test['data']));
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    curl_close($ch);
    
    echo "   HTTP Status: $httpCode\n";
    echo "   Content-Type: $contentType\n";
    
    // Check if response is JSON or HTML
    $isJson = false;
    $isHtml = false;
    
    if (strpos($contentType, 'application/json') !== false) {
        $isJson = true;
    }
    
    if (strpos($response, '<!DOCTYPE') !== false || strpos($response, '<html') !== false) {
        $isHtml = true;
    }
    
    if ($test['expect_json']) {
        if ($isJson && !$isHtml) {
            echo "   ✅ Response: JSON (Expected)\n";
            
            $decoded = json_decode($response, true);
            if ($decoded) {
                if (isset($decoded['success'])) {
                    echo "      Success: " . ($decoded['success'] ? 'true' : 'false') . "\n";
                }
                if (isset($decoded['message'])) {
                    echo "      Message: {$decoded['message']}\n";
                }
                if (isset($decoded['data']['user']['name'])) {
                    echo "      User: {$decoded['data']['user']['name']}\n";
                }
                if (isset($decoded['data']['token'])) {
                    echo "      Token: " . substr($decoded['data']['token'], 0, 15) . "...\n";
                }
            }
        } else if ($isHtml) {
            echo "   ❌ Response: HTML (Expected JSON!)\n";
            echo "   🚨 PROBLEM: API returned HTML instead of JSON\n";
            echo "   Response preview:\n";
            echo "   " . substr($response, 0, 200) . "...\n";
        } else {
            echo "   ⚠️  Response: Unknown format\n";
            echo "   Preview: " . substr($response, 0, 200) . "\n";
        }
    }
    
    echo "\n" . str_repeat("-", 70) . "\n\n";
}

echo "✅ All tests completed!\n";
echo "\n" . str_repeat(" =>", 70) . "\n";
echo "📊 SUMMARY:\n";
echo "   - If you see HTML responses where JSON expected → CORS/Route issue\n";
echo "   - All responses should be JSON with proper Content-Type\n";
echo "   - Valid logins return 200 with token\n";
echo "   - Invalid logins return 422 with error message\n";
echo str_repeat("=", 70) . "\n";
