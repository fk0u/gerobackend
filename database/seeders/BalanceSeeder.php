<?php

namespace Database\Seeders;

use App\Models\BalanceEntry;
use App\Models\User;
use App\Models\Payment;
use App\Models\Order;
use Illuminate\Database\Seeder;

class BalanceSeeder extends Seeder
{
    public function run(): void
    {
        BalanceEntry::query()->delete();

        // Welcome bonus for all end users
    $users = User::where('role', ' =>', 'end_user', 'and')->get();
        foreach ($users as $user) {
            BalanceEntry::create([
                'user_id' => $user->id,
                'direction' => 'credit',
                'amount' => 1000,
                'source_type' => 'seed',
                'source_id' => 0,
                'description' => 'Bonus awal pendaftaran',
            ]);
        }

        // Apply top-up and order payments from PaymentSeeder
        $payments = Payment::with('order')->get();
        foreach ($payments as $payment) {
            $direction = $payment->type === 'topup' ? 'credit' : 'debit';
            $description = $payment->type === 'topup'
                ? 'Top up saldo melalui '.$payment->method
                : 'Pembayaran order #'.$payment->order_id;
            BalanceEntry::create([
                'user_id' => $payment->user_id,
                'direction' => $direction,
                'amount' => $payment->amount,
                'source_type' => 'payment',
                'source_id' => $payment->id,
                'description' => $description,
            ]);
        }

        // Reward points & commission for completed orders
    $completedOrders = Order::where('status', ' =>', 'completed', 'and')->get();
        foreach ($completedOrders as $order) {
            if ($order->user_id && $order->total_points) {
                BalanceEntry::create([
                    'user_id' => $order->user_id,
                    'direction' => 'credit',
                    'amount' => $order->total_points,
                    'source_type' => 'order',
                    'source_id' => $order->id,
                    'description' => 'Reward points order #'.$order->id,
                ]);
            }
            if ($order->mitra_id && $order->total_price) {
                BalanceEntry::create([
                    'user_id' => $order->mitra_id,
                    'direction' => 'credit',
                    'amount' => (int) round(($order->total_price) * 0.1),
                    'source_type' => 'order',
                    'source_id' => $order->id,
                    'description' => 'Komisi mitra order #'.$order->id,
                ]);
            }
        }
    }
}
