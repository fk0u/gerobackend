<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserAndMitraSeeder extends Seeder
{
    public function run(): void
    {
        // End users (pelanggan)
        $endUsers = [
            [
                'email' => 'daffa@gmail.com',
                'password' => 'password123',
                'name' => 'User Daffa',
                'role' => 'end_user',
                'profile_picture' => 'assets/img_friend1.png',
                'phone' => '081234567890',
                'address' => 'Jl. Merdeka No. 1, Jakarta',
                'points' => 50,
                'subscription_status' => 'active',
                'created_at' => '2024-01-15',
            ],
            [
                'email' => 'sansan@gmail.com',
                'password' => 'password456',
                'name' => 'Jane San',
                'role' => 'end_user',
                'profile_picture' => 'assets/img_friend2.png',
                'phone' => '087654321098',
                'address' => 'Jl. Sudirman No. 2, Bandung',
                'points' => 125,
                'subscription_status' => 'active',
                'created_at' => '2024-02-20',
            ],
            [
                'email' => 'wahyuh@gmail.com',
                'password' => 'password789',
                'name' => 'Lionel Wahyu',
                'role' => 'end_user',
                'profile_picture' => 'assets/img_friend3.png',
                'phone' => '089876543210',
                'address' => 'Jl. Thamrin No. 3, Surabaya',
                'points' => 75,
                'subscription_status' => 'active',
                'created_at' => '2024-03-10',
            ],
        ];

        foreach ($endUsers as $u) {
            User::updateOrCreate(
                ['email' => $u['email']],
                [
                    'name' => $u['name'],
                    'password' => Hash::make($u['password']),
                    'role' => 'end_user',
                    'profile_picture' => $u['profile_picture'],
                    'phone' => $u['phone'],
                    'address' => $u['address'],
                    'points' => $u['points'],
                    'subscription_status' => $u['subscription_status'],
                    'email_verified_at' => now(),
                ]
            );
        }

        // Mitras (petugas/driver)
        $mitras = [
            [
                'email' => 'driver.jakarta@gerobaks.com',
                'password' => 'mitra123',
                'name' => 'Ahmad Kurniawan',
                'role' => 'mitra',
                'profile_picture' => 'assets/img_friend4.png',
                'phone' => '081345678901',
                'employee_id' => 'DRV-JKT-001',
                'vehicle_type' => 'Truck Sampah',
                'vehicle_plate' => 'B 1234 ABC',
                'work_area' => 'Jakarta Pusat',
                'status' => 'active',
                'rating' => 4.8,
                'total_collections' => 1250,
                'created_at' => '2023-06-15',
            ],
            [
                'email' => 'driver.bandung@gerobaks.com',
                'password' => 'mitra123',
                'name' => 'Budi Santoso',
                'role' => 'mitra',
                'profile_picture' => 'assets/img_friend1.png',
                'phone' => '081456789012',
                'employee_id' => 'DRV-BDG-002',
                'vehicle_type' => 'Truck Sampah',
                'vehicle_plate' => 'D 5678 EFG',
                'work_area' => 'Bandung Utara',
                'status' => 'active',
                'rating' => 4.9,
                'total_collections' => 980,
                'created_at' => '2023-08-20',
            ],
            [
                'email' => 'supervisor.surabaya@gerobaks.com',
                'password' => 'mitra123',
                'name' => 'Siti Nurhaliza',
                'role' => 'mitra',
                'profile_picture' => 'assets/img_friend2.png',
                'phone' => '081567890123',
                'employee_id' => 'SPV-SBY-003',
                'vehicle_type' => 'Motor Supervisor',
                'vehicle_plate' => 'L 9012 HIJ',
                'work_area' => 'Surabaya Timur',
                'status' => 'active',
                'rating' => 4.7,
                'total_collections' => 750,
                'created_at' => '2023-09-10',
            ],
        ];

        foreach ($mitras as $m) {
            User::updateOrCreate(
                ['email' => $m['email']],
                [
                    'name' => $m['name'],
                    'password' => Hash::make($m['password']),
                    'role' => 'mitra',
                    'profile_picture' => $m['profile_picture'],
                    'phone' => $m['phone'],
                    'employee_id' => $m['employee_id'],
                    'vehicle_type' => $m['vehicle_type'],
                    'vehicle_plate' => $m['vehicle_plate'],
                    'work_area' => $m['work_area'],
                    'status' => $m['status'],
                    'rating' => $m['rating'],
                    'total_collections' => $m['total_collections'],
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}