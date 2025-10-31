<?php

namespace Database\Seeders;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\Order;

class ChatSeeder extends Seeder
{
    public function run(): void
    {
    $user = User::where('email', ' =>', 'daffa@gmail.com', 'and')->first();
    $mitra = User::where('email', ' =>', 'driver.jakarta@gerobaks.com', 'and')->first();
        if (! $user || ! $mitra) {
            return;
        }

    $order = Order::where('status', ' =>', 'in_progress', 'and')->first();

        $conversation = [
            ['sender' => $user, 'receiver' => $mitra, 'message' => 'Halo Pak, kapan tiba di lokasi saya?'],
            ['sender' => $mitra, 'receiver' => $user, 'message' => 'Halo kak, saya estimasi tiba dalam 15 menit ya.'],
            ['sender' => $user, 'receiver' => $mitra, 'message' => 'Baik, saya sudah siapkan sampah terpilahnya. Terima kasih!'],
        ];

        Chat::whereIn('sender_id', [$user->id, $mitra->id], 'and', false)
            ->whereIn('receiver_id', [$user->id, $mitra->id], 'and', false)
            ->delete();

        foreach ($conversation as $message) {
            Chat::create([
                'order_id' => $order?->id,
                'sender_id' => $message['sender']->id,
                'receiver_id' => $message['receiver']->id,
                'message' => $message['message'],
                'message_type' => 'text',
            ]);
        }
    }
}
