<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\User;
use App\Models\Service;
use App\Models\Schedule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role','end_user')->get()->keyBy('email');
        $mitras = User::where('role','mitra')->get()->keyBy('email');
        $services = Service::orderBy('id')->get();

        if ($users->isEmpty() || $services->isEmpty()) {
            return;
        }

        $serviceCycle = $services->values();
        $now = Carbon::now();

        $orders = [
            [
                'code' => 'ORD-001',
                'user' => 'daffa@gmail.com',
                'mitra' => null,
                'status' => 'pending',
                'schedule_status' => 'pending',
                'address' => 'Jl. Merdeka No. 1, Jakarta',
                'lat' => -6.2000,
                'lng' => 106.8167,
                'requested_at' => $now->copy()->subDays(1),
                'notes' => 'Penjemputan sampah rumah tangga reguler',
                'total_price' => 0,
                'total_points' => 15,
            ],
            [
                'code' => 'ORD-002',
                'user' => 'sansan@gmail.com',
                'mitra' => 'driver.jakarta@gerobaks.com',
                'status' => 'assigned',
                'schedule_status' => 'assigned',
                'address' => 'Jl. Sudirman No. 2, Bandung',
                'lat' => -6.9147,
                'lng' => 107.6098,
                'requested_at' => $now->copy()->subDays(2),
                'notes' => 'Layanan prioritas pelanggan corporate',
                'total_price' => 25000,
                'total_points' => 30,
            ],
            [
                'code' => 'ORD-003',
                'user' => 'wahyuh@gmail.com',
                'mitra' => 'driver.bandung@gerobaks.com',
                'status' => 'in_progress',
                'schedule_status' => 'in_progress',
                'address' => 'Jl. Thamrin No. 3, Surabaya',
                'lat' => -7.2575,
                'lng' => 112.7521,
                'requested_at' => $now->copy()->subDays(3),
                'notes' => 'Pengangkutan sampah elektronik berat',
                'total_price' => 45000,
                'total_points' => 50,
            ],
            [
                'code' => 'ORD-004',
                'user' => 'daffa@gmail.com',
                'mitra' => 'supervisor.surabaya@gerobaks.com',
                'status' => 'completed',
                'schedule_status' => 'completed',
                'address' => 'Komplek Harmoni Blok B2, Jakarta',
                'lat' => -6.1900,
                'lng' => 106.8320,
                'requested_at' => $now->copy()->subDays(7),
                'completed_at' => $now->copy()->subDays(7)->addHours(4),
                'notes' => 'Layanan express dengan tambahan edukasi daur ulang',
                'total_price' => 60000,
                'total_points' => 80,
            ],
            [
                'code' => 'ORD-005',
                'user' => 'sansan@gmail.com',
                'mitra' => null,
                'status' => 'cancelled',
                'schedule_status' => 'cancelled',
                'address' => 'Jl. Asia Afrika No. 10, Bandung',
                'lat' => -6.9200,
                'lng' => 107.6200,
                'requested_at' => $now->copy()->subDays(5),
                'cancelled_at' => $now->copy()->subDays(5)->addHours(2),
                'notes' => 'Dibatalkan karena pelanggan tidak berada ditempat',
                'total_price' => 0,
                'total_points' => 0,
            ],
        ];

        foreach ($orders as $index => $blueprint) {
            $user = $users[$blueprint['user']] ?? null;
            if (! $user) {
                continue;
            }

            $mitra = $blueprint['mitra'] ? ($mitras[$blueprint['mitra']] ?? null) : null;
            $service = $serviceCycle[$index % $serviceCycle->count()];

            $schedule = Schedule::updateOrCreate(
                ['title' => 'Pickup '.$blueprint['code']],
                [
                    'description' => $blueprint['notes'] ?? null,
                    'latitude' => $blueprint['lat'],
                    'longitude' => $blueprint['lng'],
                    'status' => $blueprint['schedule_status'],
                    'assigned_to' => $mitra?->id,
                    'scheduled_at' => $blueprint['requested_at']->copy()->addHours(6),
                ]
            );

            $order = Order::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'address_text' => $blueprint['address'],
                ],
                [
                    'user_id' => $user->id,
                    'mitra_id' => $mitra?->id,
                    'service_id' => $service->id,
                    'schedule_id' => $schedule->id,
                    'address_text' => $blueprint['address'],
                    'latitude' => $blueprint['lat'],
                    'longitude' => $blueprint['lng'],
                    'status' => $blueprint['status'],
                    'requested_at' => $blueprint['requested_at'],
                    'completed_at' => $blueprint['completed_at'] ?? null,
                    'cancelled_at' => $blueprint['cancelled_at'] ?? null,
                    'notes' => $blueprint['notes'] ?? null,
                    'total_points' => $blueprint['total_points'],
                    'total_price' => $blueprint['total_price'],
                ]
            );

            if (! $order->wasRecentlyCreated && $order->schedule_id !== $schedule->id) {
                $order->schedule_id = $schedule->id;
                $order->save();
            }
        }
    }
}
