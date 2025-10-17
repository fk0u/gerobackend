<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get user
$user = App\Models\User::where('email', 'daffa@gmail.com')->first();

if (!$user) {
    echo "User not found!\n";
    exit(1);
}

echo "=== USER INFO ===\n";
echo "ID: {$user->id}\n";
echo "Name: {$user->name}\n";
echo "Email: {$user->email}\n";
echo "Role: {$user->role}\n\n";

// Create fresh token
$token = $user->createToken('test-from-script');
$plainToken = $token->plainTextToken;

echo "=== NEW TOKEN GENERATED ===\n";
echo "Plain Token: {$plainToken}\n\n";

// Test authentication
$headers = [
    'Authorization: Bearer ' . $plainToken,
    'Accept: application/json',
    'Content-Type: application/json'
];

$ch = curl_init('http://localhost:8000/api/auth/me');
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "=== TESTING /api/auth/me ===\n";
echo "HTTP Code: {$httpCode}\n";
echo "Response: {$response}\n\n";

if ($httpCode === 200) {
    echo "✅ SUCCESS! Authentication working!\n";
} else {
    echo "❌ FAILED! Still getting {$httpCode}\n";
    
    // Check token in database
    echo "\n=== TOKEN IN DATABASE ===\n";
    $dbToken = DB::table('personal_access_tokens')
        ->where('tokenable_id', $user->id)
        ->latest()
        ->first();
    
    if ($dbToken) {
        echo "Token ID: {$dbToken->id}\n";
        echo "Name: {$dbToken->name}\n";
        echo "Abilities: {$dbToken->abilities}\n";
        echo "Last Used: {$dbToken->last_used_at}\n";
        echo "Created: {$dbToken->created_at}\n";
    } else {
        echo "No token found in database!\n";
    }
}
