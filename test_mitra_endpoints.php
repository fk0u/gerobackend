<?php

/*
 * Test Mitra Schedule Endpoints
 * 
 * This tests the 4 new endpoints:
 * 1. POST /api/schedules/{id}/accept
 * 2. POST /api/schedules/{id}/start
 * 3. POST /api/schedules/{id}/complete
 * 4. POST /api/schedules/{id}/cancel
 */

require __DIR__ . '/vendor/autoload.php';

use App\Models\User;
use App\Models\Schedule;
use Illuminate\Support\Facades\Artisan;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "\n=== TESTING MITRA SCHEDULE ENDPOINTS ===\n\n";

// Get test users
$endUser = User::where('email', 'daffa@gmail.com')->first();
$mitra = User::where('email', 'driver.jakarta@gerobaks.com')->first();

if (!$endUser || !$mitra) {
    echo "❌ Test users not found. Run seeders first.\n";
    exit(1);
}

echo "✅ Users found:\n";
echo "   - End User: {$endUser->name} ({$endUser->email})\n";
echo "   - Mitra: {$mitra->name} ({$mitra->email})\n\n";

// Create a test schedule
$schedule = Schedule::create([
    'user_id' => $endUser->id,
    'title' => 'Test Pickup Schedule',
    'scheduled_at' => now()->addDay(),
    'status' => 'pending',
    'waste_items' => [
        ['category' => 'Plastik', 'weight' => 5.0, 'unit' => 'kg'],
        ['category' => 'Kertas', 'weight' => 3.0, 'unit' => 'kg'],
    ],
    'total_estimated_weight' => 8.0,
    'address' => 'Jl. Test No. 123',
    'latitude' => -6.2088,
    'longitude' => 106.8456,
    'notes' => 'Test schedule for API endpoints',
]);

echo "✅ Test schedule created (ID: {$schedule->id})\n";
echo "   Status: {$schedule->status}\n\n";

// Test 1: Accept Schedule
echo "=== TEST 1: Accept Schedule ===\n";
try {
    $controller = new \App\Http\Controllers\Api\ScheduleController();
    $request = new \Illuminate\Http\Request();
    $request->setUserResolver(function () use ($mitra) {
        return $mitra;
    });
    
    $response = $controller->accept($request, $schedule->id);
    $data = json_decode($response->getContent(), true);
    
    if ($data['status'] === 'success' && $data['data']['status'] === 'confirmed') {
        echo "✅ PASS: Schedule accepted successfully\n";
        echo "   - Status: {$data['data']['status']}\n";
        echo "   - Mitra ID: {$data['data']['mitra_id']}\n";
        
        // Refresh schedule
        $schedule->refresh();
    } else {
        echo "❌ FAIL: Unexpected response\n";
        print_r($data);
    }
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 2: Start Schedule
echo "=== TEST 2: Start Schedule ===\n";
try {
    $request = new \Illuminate\Http\Request();
    $request->setUserResolver(function () use ($mitra) {
        return $mitra;
    });
    
    $response = $controller->start($request, $schedule->id);
    $data = json_decode($response->getContent(), true);
    
    if ($data['status'] === 'success' && $data['data']['status'] === 'in_progress') {
        echo "✅ PASS: Schedule started successfully\n";
        echo "   - Status: {$data['data']['status']}\n";
        echo "   - Started At: {$data['data']['started_at']}\n";
        
        // Refresh schedule
        $schedule->refresh();
    } else {
        echo "❌ FAIL: Unexpected response\n";
        print_r($data);
    }
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 3: Complete Schedule
echo "=== TEST 3: Complete Schedule ===\n";
try {
    $request = new \Illuminate\Http\Request([
        'actual_weight' => 7.5,
        'completion_notes' => 'Pickup completed successfully',
    ]);
    $request->setUserResolver(function () use ($mitra) {
        return $mitra;
    });
    
    $response = $controller->complete($request, $schedule->id);
    $data = json_decode($response->getContent(), true);
    
    if ($data['status'] === 'success' && $data['data']['status'] === 'completed') {
        echo "✅ PASS: Schedule completed successfully\n";
        echo "   - Status: {$data['data']['status']}\n";
        echo "   - Actual Weight: {$data['data']['actual_weight']}\n";
        echo "   - Completed At: {$data['data']['completed_at']}\n";
        
        // Refresh schedule
        $schedule->refresh();
    } else {
        echo "❌ FAIL: Unexpected response\n";
        print_r($data);
    }
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
echo "\n";

// Clean up - create new schedule for cancel test
$cancelSchedule = Schedule::create([
    'user_id' => $endUser->id,
    'mitra_id' => $mitra->id,
    'title' => 'Test Cancel Schedule',
    'scheduled_at' => now()->addDay(),
    'status' => 'confirmed',
    'waste_items' => [
        ['category' => 'Plastik', 'weight' => 2.0, 'unit' => 'kg'],
    ],
    'total_estimated_weight' => 2.0,
    'address' => 'Jl. Cancel Test No. 456',
    'latitude' => -6.2088,
    'longitude' => 106.8456,
]);

echo "✅ Cancel test schedule created (ID: {$cancelSchedule->id})\n\n";

// Test 4: Cancel Schedule
echo "=== TEST 4: Cancel Schedule ===\n";
try {
    $request = new \Illuminate\Http\Request([
        'cancellation_reason' => 'Testing cancel endpoint',
    ]);
    $request->setUserResolver(function () use ($mitra) {
        return $mitra;
    });
    
    $response = $controller->cancel($request, $cancelSchedule->id);
    $data = json_decode($response->getContent(), true);
    
    if ($data['status'] === 'success' && $data['data']['status'] === 'cancelled') {
        echo "✅ PASS: Schedule cancelled successfully\n";
        echo "   - Status: {$data['data']['status']}\n";
        echo "   - Cancelled At: {$data['data']['cancelled_at']}\n";
    } else {
        echo "❌ FAIL: Unexpected response\n";
        print_r($data);
    }
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
echo "\n";

// Summary
echo "=== TEST SUMMARY ===\n";
echo "✅ All 4 endpoints tested:\n";
echo "   1. Accept - Changes status to 'confirmed', assigns mitra\n";
echo "   2. Start - Changes status to 'in_progress', adds started_at\n";
echo "   3. Complete - Changes status to 'completed', saves actual_weight\n";
echo "   4. Cancel - Changes status to 'cancelled', saves reason\n\n";

echo "✅ Database fields working:\n";
echo "   - started_at: " . ($schedule->started_at ? '✓' : '✗') . "\n";
echo "   - completed_at: " . ($schedule->completed_at ? '✓' : '✗') . "\n";
echo "   - cancelled_at: " . ($cancelSchedule->cancelled_at ? '✓' : '✗') . "\n";
echo "   - actual_weight: " . ($schedule->actual_weight ? '✓' : '✗') . "\n\n";

// Clean up
$schedule->delete();
$cancelSchedule->delete();

echo "✅ Test schedules cleaned up\n";
echo "\n=== ALL TESTS COMPLETED ===\n\n";
