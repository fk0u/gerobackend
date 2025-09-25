<?php

namespace App\Services;

use App\Models\Order;
use App\Models\BalanceEntry;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    /** Allowed status transitions */
    private array $transitions = [
        'pending' => ['assigned','cancelled'],
        'assigned' => ['in_progress','cancelled'],
        'in_progress' => ['completed','cancelled'],
        'completed' => [],
        'cancelled' => [],
    ];

    public function updateStatus(Order $order, string $to): Order
    {
        $current = $order->status ?? 'pending';
        if (!isset($this->transitions[$current]) || !in_array($to, $this->transitions[$current], true)) {
            throw ValidationException::withMessages([
                'status' => ["Invalid transition from {$current} to {$to}"]
            ]);
        }
        $order->status = $to;
        if ($to === 'completed') {
            $order->completed_at = now();
            $this->handleCompletion($order);
        }
        if ($to === 'cancelled') {
            $order->cancelled_at = now();
        }
        $order->save();
        return $order;
    }

    private function handleCompletion(Order $order): void
    {
        // Simple reward logic: points proportional to total_points or fallback constant
        $points = $order->total_points ?? 10;
        DB::transaction(function () use ($order, $points) {
            if ($order->user_id) {
                BalanceEntry::create([
                    'user_id' => $order->user_id,
                    'direction' => 'credit',
                    'amount' => $points,
                    'source_type' => 'order',
                    'source_id' => $order->id,
                    'description' => 'Reward points for completed order'
                ]);
            }
            if ($order->mitra_id) {
                BalanceEntry::create([
                    'user_id' => $order->mitra_id,
                    'direction' => 'credit',
                    'amount' =>  (int) round(($order->total_price ?? 0) * 0.1),
                    'source_type' => 'order',
                    'source_id' => $order->id,
                    'description' => 'Mitra commission for completed order'
                ]);
            }
        });
    }
}
