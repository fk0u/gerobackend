#!/usr/bin/env php
<?php
// test_laravel_api.php - Script untuk memastikan Laravel API berjalan dengan benar
// dan rute-rute penting seperti login dan register sudah terdaftar

$host = $argc > 1 ? $argv[1] : 'localhost';
$port = $argc > 2 ? $argv[2] : '8000';

$baseUrl = "http://{$host}:{$port}";
echo "Testing Laravel API di: {$baseUrl}\n\n";

// Daftar endpoint untuk diuji
$endpoints = [
    '/api/ping' => 'GET',
    '/api/login' => 'POST',
    '/api/register' => 'POST',
    '/api/auth/me' => 'GET',
];

// Data untuk endpoint POST
$testData = [
    '/api/login' => json_encode([
        'email' => 'test@example.com',
        'password' => 'password',
    ]),
    '/api/register' => json_encode([
        'name' => 'Test User',
        'email' => 'newuser@example.com',
        'password' => 'password123',
    ]),
];

// Warna untuk output
$colors = [
    'reset' => "\033[0m",
    'red' => "\033[31m",
    'green' => "\033[32m",
    'yellow' => "\033[33m",
    'blue' => "\033[34m",
    'magenta' => "\033[35m",
    'cyan' => "\033[36m",
];

// Fungsi untuk mengirim request ke endpoint
function sendRequest($url, $method = 'GET', $data = null) {
    global $colors;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Accept: application/json',
            ]);
        }
    }
    
    echo "Testing {$colors['blue']}{$method} {$url}{$colors['reset']}... ";
    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        echo "{$colors['red']}ERROR: " . curl_error($ch) . "{$colors['reset']}\n";
        curl_close($ch);
        return false;
    }
    
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode >= 200 && $httpCode < 300) {
        echo "{$colors['green']}OK ({$httpCode}){$colors['reset']}\n";
    } else {
        echo "{$colors['yellow']}WARNING ({$httpCode}){$colors['reset']}\n";
    }
    
    // Tampilkan respon JSON jika ada
    if ($response) {
        $json = json_decode($response, true);
        if ($json) {
            echo "  Response: ";
            print_r($json);
            echo "\n";
        } else {
            echo "  Raw Response: " . substr($response, 0, 100) . (strlen($response) > 100 ? "..." : "") . "\n";
        }
    }
    
    return $httpCode;
}

// Cek apakah server Laravel berjalan
echo "Checking Laravel server status...\n";
if (!sendRequest($baseUrl)) {
    echo "{$colors['red']}ERROR: Laravel server tidak dapat diakses di {$baseUrl}{$colors['reset']}\n";
    echo "Pastikan server Laravel berjalan dengan perintah:\n";
    echo "  php artisan serve --host=0.0.0.0 --port=8000\n\n";
    exit(1);
}

// Test semua endpoint
echo "\nTesting API endpoints:\n";
foreach ($endpoints as $endpoint => $method) {
    $data = isset($testData[$endpoint]) ? $testData[$endpoint] : null;
    sendRequest($baseUrl . $endpoint, $method, $data);
    echo "\n";
}

// Informasi tambahan
echo "{$colors['cyan']}INFORMASI CORS:{$colors['reset']}\n";
echo "Jika ada masalah CORS, pastikan middleware CORS Laravel dikonfigurasi dengan benar.\n";
echo "Check file app/Http/Kernel.php dan pastikan middleware CORS terdaftar.\n\n";

echo "{$colors['cyan']}INFORMASI KONEKSI FLUTTER:{$colors['reset']}\n";
echo "Untuk emulator Android: API_BASE_URL=http://10.0.2.2:8000\n";
echo "Untuk simulator iOS: API_BASE_URL=http://127.0.0.1:8000\n";
echo "Untuk perangkat fisik: API_BASE_URL=http://[IP_KOMPUTER]:8000\n\n";

echo "{$colors['green']}Testing selesai!{$colors['reset']}\n";