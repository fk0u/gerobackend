<?php

require __DIR__ . '/vendor/autoload.php';

use App\Models\User;

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$mitra = User::where('email', 'driver.jakarta@gerobaks.com')->first();
if (!$mitra) {
    echo "Mitra not found\n";
    exit(1);
}

$token = $mitra->createToken('test-token')->plainTextToken;
echo "Mitra Token:\n{$token}\n";
