<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TOKEN DEBUG ===" . PHP_EOL;

// Check if personal access tokens table exists and has data
$tokens = Laravel\Sanctum\PersonalAccessToken::all();
echo "Total tokens: " . $tokens->count() . PHP_EOL;

foreach ($tokens as $token) {
    echo "Token ID: " . $token->id . PHP_EOL;
    echo "Token Name: " . $token->name . PHP_EOL;
    echo "User Email: " . ($token->tokenable ? $token->tokenable->email : 'NULL USER') . PHP_EOL;
    echo "Created: " . $token->created_at . PHP_EOL;
    echo "---" . PHP_EOL;
}

// Test specific token
$testToken = "10|sLxW3ZpzbXHh4CEvPyZp8czZwkPO4qoooFXa0JNr15f28020";
echo PHP_EOL . "Testing token: " . $testToken . PHP_EOL;

$token = Laravel\Sanctum\PersonalAccessToken::findToken($testToken);
if ($token) {
    echo "Token found!" . PHP_EOL;
    echo "Token belongs to: " . $token->tokenable->email . PHP_EOL;
    echo "Token abilities: " . implode(', ', $token->abilities) . PHP_EOL;
} else {
    echo "Token not found!" . PHP_EOL;
}