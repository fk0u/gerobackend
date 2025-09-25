<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['name' => 'Pickup Organik', 'description' => 'Penjemputan sampah organik rumah tangga', 'base_points' => 5, 'base_price' => 0],
            ['name' => 'Pickup Anorganik', 'description' => 'Plastik, kaca, logam terpilah', 'base_points' => 10, 'base_price' => 0],
            ['name' => 'Premium Express', 'description' => 'Penjemputan kilat < 4 jam', 'base_points' => 15, 'base_price' => 15000],
        ];
        foreach ($data as $d) {
            Service::updateOrCreate(['name' => $d['name']], $d);
        }
    }
}
