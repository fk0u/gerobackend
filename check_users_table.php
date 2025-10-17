<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CHECKING USERS TABLE STRUCTURE ===\n\n";

$columns = DB::select("SHOW COLUMNS FROM users");

foreach ($columns as $column) {
    echo sprintf("%-20s %-20s %-10s %-10s %-20s\n", 
        $column->Field, 
        $column->Type, 
        $column->Null, 
        $column->Key,
        $column->Extra ?? ''
    );
}
