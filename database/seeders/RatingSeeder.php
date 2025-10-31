<?php

namespace Database\Seeders;

use App\Models\Rating;
use App\Models\Order;
use Illuminate\Database\Seeder;

class RatingSeeder extends Seeder
{
    public function run(): void
    {
    $order = Order::whereNotNull('mitra_id', 'and')->first();
        if (!$order) return;
        Rating::updateOrCreate(
            ['order_id' => $order->id, 'user_id' => $order->user_id],
            [
                'mitra_id' => $order->mitra_id,
                'score' => 5,
                'comment' => 'Pelayanan cepat dan ramah'
            ]
        );
    }
}
