<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class QuickTrackingSeeder extends Seeder
{
    /**
     * Generate fake tracking data for testing
     * Creates realistic GPS tracking points for Jakarta area
     */
    public function run(): void
    {
        // Clear existing data
        DB::table('trackings')->truncate();
        
        echo "🚀 Generating fake tracking data...\n";
        
        // Jakarta coordinates (center point)
        $jakartaLat = -6.2088;
        $jakartaLng = 106.8456;
        
        // Generate 3 different routes
        $routes = [
            ['name' => 'Route 1: North Jakarta', 'schedule_id' => 1, 'points' => 50],
            ['name' => 'Route 2: South Jakarta', 'schedule_id' => 2, 'points' => 75],
            ['name' => 'Route 3: East Jakarta', 'schedule_id' => 3, 'points' => 100],
        ];
        
        $totalPoints = 0;
        
        foreach ($routes as $route) {
            echo "📍 Generating {$route['name']} ({$route['points']} points)...\n";
            
            $points = [];
            $baseTime = Carbon::now()->subHours(2); // Start 2 hours ago
            
            // Random starting offset for this route
            $latOffset = (rand(-100, 100) / 1000); // -0.1 to +0.1 degrees
            $lngOffset = (rand(-100, 100) / 1000);
            
            for ($i = 0; $i < $route['points']; $i++) {
                // Simulate movement (random walk)
                $latOffset += (rand(-10, 10) / 10000); // Small increments
                $lngOffset += (rand(-10, 10) / 10000);
                
                // Keep within Jakarta bounds
                $lat = round($jakartaLat + $latOffset, 7);
                $lng = round($jakartaLng + $lngOffset, 7);
                
                // Ensure coordinates are within valid GPS range
                $lat = max(-90, min(90, $lat));
                $lng = max(-180, min(180, $lng));
                
                // Random speed (0-80 km/h)
                $speed = round(rand(0, 800) / 10, 2);
                
                // Random heading (0-360 degrees)
                $heading = round(rand(0, 3600) / 10, 2);
                
                // Time progression (every 30 seconds)
                $recordedAt = $baseTime->copy()->addSeconds($i * 30);
                
                $points[] = [
                    'schedule_id' => $route['schedule_id'],
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'speed' => $speed,
                    'heading' => $heading,
                    'recorded_at' => $recordedAt,
                    'created_at' => $recordedAt,
                    'updated_at' => $recordedAt,
                ];
            }
            
            // Bulk insert
            DB::table('trackings')->insert($points);
            $totalPoints += count($points);
            
            echo "   ✅ {$route['name']}: {$route['points']} points inserted\n";
        }
        
        echo "\n🎉 DONE! Total: $totalPoints tracking points created\n\n";
        
        // Show statistics
        $stats = DB::table('trackings')
            ->selectRaw('
                COUNT(*) as total,
                COUNT(DISTINCT schedule_id) as schedules,
                MIN(latitude) as min_lat,
                MAX(latitude) as max_lat,
                MIN(longitude) as min_lng,
                MAX(longitude) as max_lng,
                MIN(speed) as min_speed,
                MAX(speed) as max_speed,
                MIN(recorded_at) as earliest,
                MAX(recorded_at) as latest
            ')
            ->first();
        
        echo "📊 Statistics:\n";
        echo "   Total Points: {$stats->total}\n";
        echo "   Schedules: {$stats->schedules}\n";
        echo "   Latitude Range: {$stats->min_lat} to {$stats->max_lat}\n";
        echo "   Longitude Range: {$stats->min_lng} to {$stats->max_lng}\n";
        echo "   Speed Range: {$stats->min_speed} to {$stats->max_speed} km/h\n";
        echo "   Time Range: {$stats->earliest} to {$stats->latest}\n";
        
        echo "\n✅ Test API: curl https://gerobaks.dumeg.com/api/tracking?limit=10\n";
    }
}
