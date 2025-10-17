<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CHECKING personal_access_tokens TABLE STRUCTURE ===\n\n";

// Get table columns
$columns = DB::select("SHOW COLUMNS FROM personal_access_tokens");

foreach ($columns as $column) {
    echo sprintf("%-20s %-20s %-10s %-10s\n", 
        $column->Field, 
        $column->Type, 
        $column->Null, 
        $column->Key
    );
}

echo "\n=== SAMPLE DATA ===\n";
$tokens = DB::table('personal_access_tokens')->latest()->limit(1)->get();

foreach ($tokens as $token) {
    foreach ($token as $key => $value) {
        $displayValue = is_string($value) && strlen($value) > 50 
            ? substr($value, 0, 40) . '...' 
            : $value;
        echo "$key: $displayValue\n";
    }
}
