<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class QuickScheduleSeeder extends Seeder
{
    /**
     * Generate fake schedule data for testing
     */
    public function run(): void
    {
        echo "🗓️  Generating fake schedule data...\n\n";
        
        // Jakarta area coordinates
        $locations = [
            ['name' => 'Menteng', 'lat' => -6.1944, 'lng' => 106.8294],
            ['name' => 'Kemang', 'lat' => -6.2615, 'lng' => 106.8166],
            ['name' => 'Senayan', 'lat' => -6.2267, 'lng' => 106.7994],
            ['name' => 'Kuningan', 'lat' => -6.2382, 'lng' => 106.8306],
            ['name' => 'Thamrin', 'lat' => -6.1944, 'lng' => 106.8229],
        ];
        
        $statuses = ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled'];
        
        // Create 10 sample schedules
        for ($i = 1; $i <= 10; $i++) {
            $pickupLocation = $locations[array_rand($locations)];
            $dropoffLocation = $locations[array_rand($locations)];
            
            // Ensure pickup != dropoff
            while ($pickupLocation['name'] === $dropoffLocation['name']) {
                $dropoffLocation = $locations[array_rand($locations)];
            }
            
            $scheduledTime = Carbon::now()->addHours(rand(-48, 48)); // Random time ±48 hours
            $status = $statuses[array_rand($statuses)];
            
            // Price based on distance (rough estimate)
            $latDiff = abs($pickupLocation['lat'] - $dropoffLocation['lat']);
            $lngDiff = abs($pickupLocation['lng'] - $dropoffLocation['lng']);
            $distance = sqrt($latDiff * $latDiff + $lngDiff * $lngDiff);
            $price = round(50000 + ($distance * 100000), 2); // Base 50k + distance
            
            $data = [
                'id' => Str::uuid(),
                'user_id' => 1, // Default user
                'mitra_id' => ($i % 3) + 1, // Rotate between 3 mitras
                'assigned_user_id' => ($i % 3) + 1,
                'price' => $price,
                'latitude' => $dropoffLocation['lat'],
                'longitude' => $dropoffLocation['lng'],
                'address' => $dropoffLocation['name'] . ', Jakarta Selatan, DKI Jakarta',
                'pickup_latitude' => $pickupLocation['lat'],
                'pickup_longitude' => $pickupLocation['lng'],
                'pickup_address' => $pickupLocation['name'] . ', Jakarta Pusat, DKI Jakarta',
                'status' => $status,
                'waste_type' => ['organic', 'plastic', 'paper', 'metal', 'glass'][array_rand(['organic', 'plastic', 'paper', 'metal', 'glass'])],
                'estimated_weight' => rand(5, 50),
                'notes' => "Sample schedule #{$i} - From {$pickupLocation['name']} to {$dropoffLocation['name']}",
                'scheduled_time' => $scheduledTime,
                'created_at' => Carbon::now()->subDays(rand(0, 7)),
                'updated_at' => Carbon::now(),
            ];
            
            try {
                DB::table('schedules')->insert($data);
                echo "   ✅ Schedule #{$i}: {$pickupLocation['name']} → {$dropoffLocation['name']} (Rp " . number_format($price, 0, ',', '.') . ") [{$status}]\n";
            } catch (\Exception $e) {
                echo "   ⚠️  Schedule #{$i}: Error - " . $e->getMessage() . "\n";
            }
        }
        
        echo "\n🎉 DONE! 10 schedules created\n\n";
        
        // Show statistics
        $stats = DB::table('schedules')
            ->selectRaw('
                COUNT(*) as total,
                MIN(price) as min_price,
                MAX(price) as max_price,
                AVG(price) as avg_price,
                COUNT(DISTINCT status) as statuses
            ')
            ->first();
        
        echo "📊 Statistics:\n";
        echo "   Total Schedules: {$stats->total}\n";
        echo "   Price Range: Rp " . number_format($stats->min_price, 0, ',', '.') . " - Rp " . number_format($stats->max_price, 0, ',', '.') . "\n";
        echo "   Average Price: Rp " . number_format($stats->avg_price, 0, ',', '.') . "\n";
        
        echo "\n✅ Test API: curl https://gerobaks.dumeg.com/api/schedules?limit=10\n";
    }
}
