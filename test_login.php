<?php
/**
 * Simple test script to verify login API works locally
 * Run with: php test_login.php
 */

$baseUrl = 'http://127.0.0.1:8000';

// Test credentials from seeder
$testCases = [
    ['email' => 'daffa@gmail.com', 'password' => 'password123', 'expected_role' => 'end_user'],
    ['email' => 'driver.jakarta@gerobaks.com', 'password' => 'mitra123', 'expected_role' => 'mitra'],
];

echo "🧪 Testing Login API at: $baseUrl/api/login\n\n";

foreach ($testCases as $test) {
    echo "Testing: {$test['email']} / {$test['password']}\n";
    
    $ch = curl_init("$baseUrl/api/login");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Accept: application/json'
        ],
        CURLOPT_POSTFIELDS => json_encode([
            'email' => $test['email'],
            'password' => $test['password']
        ])
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "HTTP Status: $httpCode\n";
    
    if ($httpCode === 200) {
        $data = json_decode($response, true);
        echo "✅ Login SUCCESS\n";
        echo "   Name: {$data['data']['user']['name']}\n";
        echo "   Role: {$data['data']['user']['role']}\n";
        echo "   Token: " . substr($data['data']['token'], 0, 20) . "...\n";
    } else {
        echo "❌ Login FAILED\n";
        echo "   Response: $response\n";
    }
    
    echo "\n" . str_repeat('-', 60) . "\n\n";
}

echo "✅ Test completed!\n";
