<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Schedule;
use Carbon\Carbon;

/**
 * Seeder: MITRA ROLE IMPLEMENTATION - Schedule Test Data
 * 
 * Purpose: Create test schedules for complete Mitra workflow testing
 * Date: 2025-10-21 15:45:00
 * Feature: Test data for Accept → Start → Complete/Cancel flow
 * 
 * Creates schedules with different statuses:
 * 1. Pending - Ready for Mitra to accept
 * 2. Confirmed - Accepted by Mitra, ready to start
 * 3. In Progress - Mitra started pickup (with started_at)
 * 4. Completed - Finished pickup (with completed_at, actual_weight)
 * 5. Cancelled - Cancelled schedule (with cancelled_at)
 * 
 * Usage: php artisan db:seed --class=MitraRoleImplementationSeeder
 */
class MitraRoleImplementationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "\n🚀 === MITRA ROLE IMPLEMENTATION SEEDER ===\n";
        echo "📅 Date: 2025-10-21 15:45:00\n";
        echo "🎯 Purpose: Create test data for Mitra workflow\n\n";

        // Get test users
        $endUser = User::where('email', 'daffa@gmail.com')->first();
        $mitra = User::where('email', 'driver.jakarta@gerobaks.com')->first();

        if (!$endUser) {
            echo "❌ End user not found (daffa@gmail.com)\n";
            echo "   Run: php artisan db:seed --class=UserSeeder\n";
            return;
        }

        if (!$mitra) {
            echo "❌ Mitra not found (driver.jakarta@gerobaks.com)\n";
            echo "   Run: php artisan db:seed --class=UserSeeder\n";
            return;
        }

        echo "✅ Users found:\n";
        echo "   - End User: {$endUser->name}\n";
        echo "   - Mitra: {$mitra->name}\n\n";

        // Clean old test data (optional - comment out if you want to keep existing data)
        echo "🧹 Cleaning old Mitra test schedules...\n";
        Schedule::where('title', 'LIKE', '[MITRA TEST]%')->delete();
        echo "✅ Cleanup complete\n\n";

        // 1. PENDING Schedule - Ready for Accept
        echo "📝 Creating PENDING schedule...\n";
        $pendingSchedule = Schedule::create([
            'user_id' => $endUser->id,
            'title' => '[MITRA TEST] Pengambilan Sampah Organik - Pending',
            'description' => 'Lokasi: Jl. Merdeka No. 123, Jakarta Pusat',
            'scheduled_at' => Carbon::now()->addDay(),
            'status' => 'pending',
            'waste_items' => [
                ['category' => 'Organik', 'weight' => 10.0, 'unit' => 'kg'],
                ['category' => 'Plastik', 'weight' => 3.5, 'unit' => 'kg'],
            ],
            'total_estimated_weight' => 13.5,
            'latitude' => -6.1751,
            'longitude' => 106.8650,
            'notes' => 'Test schedule untuk Accept endpoint',
        ]);
        echo "   ✅ ID: {$pendingSchedule->id} - Status: pending\n";

        // 2. CONFIRMED Schedule - Accepted by Mitra, ready to Start
        echo "📝 Creating CONFIRMED schedule...\n";
        $confirmedSchedule = Schedule::create([
            'user_id' => $endUser->id,
            'mitra_id' => $mitra->id,
            'title' => '[MITRA TEST] Pengambilan Sampah Kertas - Confirmed',
            'description' => 'Lokasi: Jl. Sudirman No. 456, Jakarta Selatan',
            'scheduled_at' => Carbon::now()->addHours(2),
            'status' => 'confirmed',
            'waste_items' => [
                ['category' => 'Kertas', 'weight' => 5.0, 'unit' => 'kg'],
                ['category' => 'Kardus', 'weight' => 8.0, 'unit' => 'kg'],
            ],
            'total_estimated_weight' => 13.0,
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'notes' => 'Test schedule untuk Start endpoint',
        ]);
        echo "   ✅ ID: {$confirmedSchedule->id} - Status: confirmed (assigned to Mitra)\n";

        // 3. IN_PROGRESS Schedule - Mitra started pickup
        echo "📝 Creating IN_PROGRESS schedule...\n";
        $inProgressSchedule = Schedule::create([
            'user_id' => $endUser->id,
            'mitra_id' => $mitra->id,
            'title' => '[MITRA TEST] Pengambilan Sampah Plastik - In Progress',
            'description' => 'Lokasi: Jl. Thamrin No. 789, Jakarta Pusat',
            'scheduled_at' => Carbon::now()->subMinutes(30),
            'started_at' => Carbon::now()->subMinutes(15),
            'status' => 'in_progress',
            'waste_items' => [
                ['category' => 'Plastik', 'weight' => 7.0, 'unit' => 'kg'],
                ['category' => 'Botol', 'weight' => 4.5, 'unit' => 'kg'],
            ],
            'total_estimated_weight' => 11.5,
            'latitude' => -6.1944,
            'longitude' => 106.8229,
            'notes' => 'Test schedule untuk Complete endpoint',
        ]);
        echo "   ✅ ID: {$inProgressSchedule->id} - Status: in_progress (started: " . $inProgressSchedule->started_at->format('H:i') . ")\n";

        // 4. COMPLETED Schedule - Finished pickup
        echo "📝 Creating COMPLETED schedule...\n";
        $completedSchedule = Schedule::create([
            'user_id' => $endUser->id,
            'mitra_id' => $mitra->id,
            'title' => '[MITRA TEST] Pengambilan Sampah Logam - Completed',
            'description' => 'Lokasi: Jl. Gatot Subroto No. 321, Jakarta Selatan',
            'scheduled_at' => Carbon::now()->subHours(3),
            'started_at' => Carbon::now()->subHours(2),
            'completed_at' => Carbon::now()->subHour(),
            'status' => 'completed',
            'waste_items' => [
                ['category' => 'Logam', 'weight' => 12.0, 'unit' => 'kg'],
                ['category' => 'Kaleng', 'weight' => 6.0, 'unit' => 'kg'],
            ],
            'total_estimated_weight' => 18.0,
            'actual_weight' => 17.5, // Actual collected weight
            'latitude' => -6.2297,
            'longitude' => 106.8081,
            'notes' => 'Pickup berhasil, sedikit lebih ringan dari estimasi',
        ]);
        echo "   ✅ ID: {$completedSchedule->id} - Status: completed\n";
        echo "      Estimated: {$completedSchedule->total_estimated_weight} kg | Actual: {$completedSchedule->actual_weight} kg\n";

        // 5. CANCELLED Schedule
        echo "📝 Creating CANCELLED schedule...\n";
        $cancelledSchedule = Schedule::create([
            'user_id' => $endUser->id,
            'mitra_id' => $mitra->id,
            'title' => '[MITRA TEST] Pengambilan Sampah Kaca - Cancelled',
            'description' => 'Lokasi: Jl. Rasuna Said No. 654, Jakarta Selatan',
            'scheduled_at' => Carbon::now()->addHours(5),
            'cancelled_at' => Carbon::now(),
            'status' => 'cancelled',
            'waste_items' => [
                ['category' => 'Kaca', 'weight' => 4.0, 'unit' => 'kg'],
            ],
            'total_estimated_weight' => 4.0,
            'latitude' => -6.2215,
            'longitude' => 106.8440,
            'notes' => 'Dibatalkan karena lokasi tidak ditemukan',
        ]);
        echo "   ✅ ID: {$cancelledSchedule->id} - Status: cancelled\n";

        // Summary
        echo "\n✨ === SEEDER SUMMARY ===\n";
        echo "📊 Total schedules created: 5\n";
        echo "   1. Pending (ID: {$pendingSchedule->id}) - Ready for /accept\n";
        echo "   2. Confirmed (ID: {$confirmedSchedule->id}) - Ready for /start\n";
        echo "   3. In Progress (ID: {$inProgressSchedule->id}) - Ready for /complete\n";
        echo "   4. Completed (ID: {$completedSchedule->id}) - Reference example\n";
        echo "   5. Cancelled (ID: {$cancelledSchedule->id}) - Reference example\n";
        echo "\n🧪 Test with these IDs:\n";
        echo "   POST /api/schedules/{$pendingSchedule->id}/accept\n";
        echo "   POST /api/schedules/{$confirmedSchedule->id}/start\n";
        echo "   POST /api/schedules/{$inProgressSchedule->id}/complete\n";
        echo "\n✅ Mitra Role Implementation Seeder Complete!\n\n";
    }
}
