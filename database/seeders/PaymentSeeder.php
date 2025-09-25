<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $orders = Order::with('user')->get();
        $now = Carbon::now();

        foreach ($orders as $order) {
            if (! $order->user) {
                continue;
            }

            $status = $order->status === 'completed' ? 'paid' : 'pending';
            $paidAt = $status === 'paid' ? $order->completed_at ?? $now->copy()->subDay() : null;

            Payment::updateOrCreate(
                ['order_id' => $order->id, 'type' => 'order'],
                [
                    'user_id' => $order->user_id,
                    'method' => 'wallet',
                    'amount' => $order->total_price ?? 0,
                    'status' => $status,
                    'paid_at' => $paidAt,
                ]
            );
        }

        // Seed historical top-ups for demo balances
        $users = User::where('role', 'end_user')->get();
        foreach ($users as $index => $user) {
            Payment::updateOrCreate(
                ['order_id' => null, 'type' => 'topup', 'reference' => 'TOPUP-'.($index+1)],
                [
                    'user_id' => $user->id,
                    'method' => 'qris',
                    'amount' => 50000 + ($index * 25000),
                    'status' => 'paid',
                    'paid_at' => $now->copy()->subDays($index + 2),
                ]
            );
        }
    }
}
