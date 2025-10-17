<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

echo "=== SANCTUM DEBUG TEST ===\n\n";

// Get test user
$user = User::where('email', 'daffa@gmail.com')->first();
if (!$user) {
    echo "❌ User not found!\n";
    exit(1);
}

echo "✅ User found: {$user->name} ({$user->email})\n";
echo "   Role: {$user->role}\n\n";

// Delete old tokens
$user->tokens()->delete();
echo "🗑️  Deleted old tokens\n";

// Create new token
$token = $user->createToken('debug-test');
$plainToken = $token->plainTextToken;

echo "✅ Token created: $plainToken\n\n";

// Check token in database
$tokenModel = $user->tokens()->first();
echo "=== TOKEN IN DATABASE ===\n";
echo "Token ID: {$tokenModel->id}\n";
echo "Tokenable ID: {$tokenModel->tokenable_id}\n";
echo "Tokenable Type: {$tokenModel->tokenable_type}\n";
echo "Name: {$tokenModel->name}\n";
echo "Token (hashed): " . substr($tokenModel->token, 0, 20) . "...\n";
echo "Abilities: " . json_encode($tokenModel->abilities) . "\n";
echo "Last Used At: {$tokenModel->last_used_at}\n";
echo "Expires At: {$tokenModel->expires_at}\n";
echo "Created At: {$tokenModel->created_at}\n\n";

// Try to find token using Sanctum's method
echo "=== TESTING TOKEN RETRIEVAL ===\n";
$tokenId = explode('|', $plainToken, 2)[0];
$tokenValue = explode('|', $plainToken, 2)[1];

echo "Token ID from plain: $tokenId\n";
echo "Token value from plain: " . substr($tokenValue, 0, 20) . "...\n\n";

// Hash the token value like Sanctum does
$hashedToken = hash('sha256', $tokenValue);
echo "Hashed token value: " . substr($hashedToken, 0, 40) . "...\n\n";

// Try to find the token
$foundToken = \Laravel\Sanctum\PersonalAccessToken::findToken($plainToken);
if ($foundToken) {
    echo "✅ Token found via findToken()!\n";
    echo "   Owner: {$foundToken->tokenable->name}\n";
} else {
    echo "❌ Token NOT found via findToken()!\n";
}

echo "\n=== TESTING HTTP REQUEST ===\n";

// Test with curl
$url = 'http://localhost:8000/api/auth/me';
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $plainToken",
    "Accept: application/json",
    "Content-Type: application/json"
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response: $response\n";

if ($httpCode === 200) {
    echo "\n✅ SUCCESS! Authentication working!\n";
} else {
    echo "\n❌ FAILED! Still getting $httpCode\n";
}

echo "\n=== CHECKING AUTH CONFIG ===\n";
$authConfig = config('auth');
echo "Default guard: " . $authConfig['defaults']['guard'] . "\n";
echo "Guards: " . json_encode(array_keys($authConfig['guards'])) . "\n";

echo "\n=== CHECKING SANCTUM CONFIG ===\n";
$sanctumConfig = config('sanctum');
echo "Guard: " . json_encode($sanctumConfig['guard']) . "\n";
echo "Expiration: " . ($sanctumConfig['expiration'] ?? 'null') . "\n";
echo "Token prefix: " . ($sanctumConfig['token_prefix'] ?? 'none') . "\n";
