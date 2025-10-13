<?php
/**
 * Test CORS Headers
 */

echo "🌐 CORS CONFIGURATION TEST\n";
echo str_repeat("=", 70) . "\n\n";

$baseUrl = 'http://127.0.0.1:8000';

// Test CORS preflight (OPTIONS)
echo "📋 Test 1: CORS Preflight (OPTIONS)\n";
echo "   Simulating browser preflight request...\n\n";

$ch = curl_init("{$baseUrl}/api/login");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST => 'OPTIONS',
    CURLOPT_HTTPHEADER => [
        'Origin: http://localhost:3000',
        'Access-Control-Request-Method: POST',
        'Access-Control-Request-Headers: Content-Type, Authorization',
    ],
    CURLOPT_HEADER => true,
    CURLOPT_NOBODY => false,
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "   HTTP Status: $httpCode\n";

// Extract headers
preg_match_all('/^([\w-]+):\s*(.+)$/m', $response, $matches, PREG_SET_ORDER);

$corsHeaders = [];
foreach ($matches as $match) {
    $header = $match[1];
    $value = trim($match[2]);
    
    if (stripos($header, 'Access-Control') === 0 || $header === 'Vary') {
        $corsHeaders[$header] = $value;
    }
}

if (empty($corsHeaders)) {
    echo "   ❌ NO CORS HEADERS FOUND!\n";
} else {
    echo "   ✅ CORS Headers Found:\n";
    foreach ($corsHeaders as $header => $value) {
        echo "      $header: $value\n";
    }
}

echo "\n" . str_repeat("-", 70) . "\n\n";

// Test actual POST request with CORS
echo "📋 Test 2: POST Request with CORS Headers\n";
echo "   Simulating AJAX request from browser...\n\n";

$ch = curl_init("{$baseUrl}/api/login");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Accept: application/json',
        'Origin: http://localhost:3000',
    ],
    CURLOPT_POSTFIELDS => json_encode([
        'email' => 'daffa@gmail.com',
        'password' => 'password123',
    ]),
    CURLOPT_HEADER => true,
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "   HTTP Status: $httpCode\n";

// Extract headers
preg_match_all('/^([\w-]+):\s*(.+)$/m', $response, $matches, PREG_SET_ORDER);

$corsHeaders = [];
foreach ($matches as $match) {
    $header = $match[1];
    $value = trim($match[2]);
    
    if (stripos($header, 'Access-Control') === 0 || $header === 'Vary') {
        $corsHeaders[$header] = $value;
    }
}

if (empty($corsHeaders)) {
    echo "   ❌ NO CORS HEADERS IN RESPONSE!\n";
} else {
    echo "   ✅ CORS Headers in Response:\n";
    foreach ($corsHeaders as $header => $value) {
        echo "      $header: $value\n";
    }
}

// Check body
$parts = explode("\r\n\r\n", $response, 2);
$body = isset($parts[1]) ? $parts[1] : '';

if (!empty($body)) {
    $decoded = json_decode($body, true);
    if ($decoded) {
        echo "\n   ✅ Response Body is JSON\n";
        if (isset($decoded['success']) && $decoded['success']) {
            echo "      Login: SUCCESS\n";
            echo "      User: {$decoded['data']['user']['name']}\n";
        }
    } else {
        echo "\n   ❌ Response Body is NOT JSON\n";
        echo "      Preview: " . substr($body, 0, 100) . "\n";
    }
}

echo "\n" . str_repeat(" =>", 70) . "\n";
echo "📊 CORS CHECK SUMMARY:\n\n";

$requiredHeaders = [
    'Access-Control-Allow-Origin',
    'Access-Control-Allow-Methods',
    'Access-Control-Allow-Headers',
];

$missingHeaders = [];
foreach ($requiredHeaders as $required) {
    if (!isset($corsHeaders[$required])) {
        $missingHeaders[] = $required;
    }
}

if (empty($missingHeaders)) {
    echo "✅ ALL REQUIRED CORS HEADERS PRESENT\n";
    echo "✅ API is ready for cross-origin requests\n";
    echo "✅ Flutter app should be able to connect\n";
} else {
    echo "❌ MISSING CORS HEADERS:\n";
    foreach ($missingHeaders as $missing) {
        echo "   - $missing\n";
    }
    echo "\n⚠️  API may reject cross-origin requests!\n";
}

echo str_repeat("=", 70) . "\n";
