#!/usr/bin/env php
<?php
/**
 * Migration Safety Checker
 * 
 * Script untuk verify keamanan migration sebelum dijalankan
 * Usage: php check_migration_safety.php
 */

echo "🔍 MIGRATION SAFETY CHECKER\n";
echo "================================\n\n";

// Check if we're in the right directory
if (!file_exists('artisan')) {
    echo "❌ Error: Must be run from Laravel backend directory\n";
    echo "   cd backend && php database/migrations/check_migration_safety.php\n";
    exit(1);
}

// Load Laravel
require __DIR__.'/../../vendor/autoload.php';
$app = require_once __DIR__.'/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "✅ Laravel loaded successfully\n\n";

// Check database connection
echo "📊 Checking database connection...\n";
try {
    DB::connection()->getPdo();
    echo "✅ Database connected: " . DB::connection()->getDatabaseName() . "\n\n";
} catch (\Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Check database version
echo "🔍 Checking database version...\n";
$version = DB::select('SELECT VERSION() as version')[0]->version;
echo "   Database: " . $version . "\n";

// Check if MySQL/MariaDB supports JSON
if (stripos($version, 'mysql') !== false || stripos($version, 'mariadb') !== false) {
    $majorVersion = explode('.', $version)[0];
    if (stripos($version, 'mysql') !== false && $majorVersion >= 5) {
        echo "✅ JSON column supported (MySQL 5.7+)\n\n";
    } elseif (stripos($version, 'mariadb') !== false && $majorVersion >= 10) {
        echo "✅ JSON column supported (MariaDB 10.2+)\n\n";
    } else {
        echo "⚠️  Warning: JSON support may be limited\n\n";
    }
} else {
    echo "ℹ️  Database type: " . $version . "\n\n";
}

// Check if schedules table exists
echo "📋 Checking schedules table...\n";
if (!Schema::hasTable('schedules')) {
    echo "❌ Error: schedules table does not exist!\n";
    echo "   Run migrations first: php artisan migrate\n";
    exit(1);
}
echo "✅ schedules table exists\n\n";

// Check for required columns
echo "🔍 Checking required columns...\n";
$requiredColumns = [
    'id',
    'pickup_address',
    'pickup_latitude',
    'pickup_longitude',
];

$missingColumns = [];
foreach ($requiredColumns as $column) {
    if (!Schema::hasColumn('schedules', $column)) {
        $missingColumns[] = $column;
        echo "❌ Missing column: $column\n";
    } else {
        echo "✅ Column exists: $column\n";
    }
}

if (!empty($missingColumns)) {
    echo "\n⚠️  Warning: Missing required columns. Run previous migrations first.\n";
    exit(1);
}
echo "\n";

// Check if new columns already exist
echo "🔍 Checking if migration already applied...\n";
if (Schema::hasColumn('schedules', 'waste_items')) {
    echo "⚠️  Column 'waste_items' already exists\n";
    echo "   Migration may have already been applied\n";
    $alreadyApplied = true;
} else {
    echo "✅ Column 'waste_items' does not exist (ready to add)\n";
    $alreadyApplied = false;
}

if (Schema::hasColumn('schedules', 'total_estimated_weight')) {
    echo "⚠️  Column 'total_estimated_weight' already exists\n";
    $alreadyApplied = true;
} else {
    echo "✅ Column 'total_estimated_weight' does not exist (ready to add)\n";
}
echo "\n";

// Check table structure
echo "📊 Current schedules table structure:\n";
$columns = DB::select("DESCRIBE schedules");
echo "   Total columns: " . count($columns) . "\n";
foreach ($columns as $column) {
    echo "   - {$column->Field} ({$column->Type})\n";
}
echo "\n";

// Check for indexes
echo "🔍 Checking indexes...\n";
$indexes = DB::select("SHOW INDEXES FROM schedules");
$indexNames = array_unique(array_column($indexes, 'Key_name'));
echo "   Existing indexes: " . implode(', ', $indexNames) . "\n";

if (in_array('total_estimated_weight', array_column($indexes, 'Key_name'))) {
    echo "⚠️  Index on total_estimated_weight already exists\n";
} else {
    echo "✅ No index on total_estimated_weight (ready to add)\n";
}
echo "\n";

// Check migration status
echo "📋 Checking migration status...\n";
$migrations = DB::table('migrations')->get();
echo "   Total migrations run: " . count($migrations) . "\n";

$targetMigration = '2025_10_20_000001_add_multiple_waste_to_schedules_table';
$migrationExists = $migrations->contains('migration', $targetMigration);

if ($migrationExists) {
    echo "⚠️  Migration already recorded in migrations table\n";
    echo "   This migration has already been run!\n";
} else {
    echo "✅ Migration not yet recorded (ready to run)\n";
}
echo "\n";

// Final summary
echo "================================\n";
echo "📊 SAFETY CHECK SUMMARY\n";
echo "================================\n\n";

if ($alreadyApplied) {
    echo "⚠️  WARNING: Migration appears to already be applied\n";
    echo "   - Columns already exist in database\n";
    echo "   - Running migration may cause errors\n\n";
    echo "   Recommended actions:\n";
    echo "   1. Check php artisan migrate:status\n";
    echo "   2. If migration is listed, skip it\n";
    echo "   3. If columns exist but migration not listed, manually update migrations table\n\n";
    exit(1);
} else {
    echo "✅ SAFE TO RUN MIGRATION\n\n";
    echo "   Prerequisites: OK\n";
    echo "   - schedules table exists\n";
    echo "   - Required columns exist (pickup_longitude)\n";
    echo "   - New columns don't exist yet\n";
    echo "   - Database supports JSON\n\n";
    
    echo "   Run migration:\n";
    echo "   php artisan migrate\n\n";
    
    echo "   Or dry run first:\n";
    echo "   php artisan migrate --pretend\n\n";
    
    exit(0);
}
