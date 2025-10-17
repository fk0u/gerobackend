<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

use Illuminate\Http\Request;

echo "=== TESTING LOGIN FOR MITRA ===\n\n";

try {
    // Create request with JSON body
    $request = Request::create('/api/login', 'POST', [], [], [], [
        'CONTENT_TYPE' => 'application/json',
        'HTTP_ACCEPT' => 'application/json'
    ], json_encode([
        'email' => 'mitra@test.com',
        'password' => 'password123'
    ]));
    
    // Handle request
    $response = $kernel->handle($request);
    
    echo "Status Code: " . $response->getStatusCode() . "\n";
    echo "Response: " . $response->getContent() . "\n";
    
    $kernel->terminate($request, $response);
    
} catch (\Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "\nStack trace:\n";
    echo $e->getTraceAsString() . "\n";
}
