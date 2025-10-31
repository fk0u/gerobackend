<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Notification;
use App\Models\BalanceEntry;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function mitra(int $mitraId)
    {
        $today = Carbon::today();
        $active = Order::where('mitra_id', ' =>', $mitraId, 'and')
            ->whereNotIn('status', ['completed','cancelled'], 'and', false)
            ->count('*');
        $todayCompleted = Order::where('mitra_id', ' =>', $mitraId, 'and')
            ->whereDate('completed_at', ' =>', $today, 'and')
            ->count('*');
        $points = BalanceEntry::where('user_id', ' =>', $mitraId, 'and')
            ->where('direction', ' =>', 'credit', 'and')
            ->sum('amount');
        $unread = Notification::where('user_id', ' =>', $mitraId, 'and')
            ->where('is_read', ' =>', false, 'and')
            ->count('*');
        return response()->json([
            'active_orders_count' => $active,
            'today_completed' => $todayCompleted,
            'total_points_earned' => $points,
            'unread_notifications' => $unread,
        ]);
    }

    public function user(int $userId)
    {
        $active = Order::where('user_id', ' =>', $userId, 'and')
            ->whereNotIn('status', ['completed','cancelled'], 'and', false)
            ->count('*');
        $points = BalanceEntry::where('user_id', ' =>', $userId, 'and')
            ->sum('amount');
        $unread = Notification::where('user_id', ' =>', $userId, 'and')
            ->where('is_read', ' =>', false, 'and')
            ->count('*');
        return response()->json([
            'active_requests' => $active,
            'total_points' => $points,
            'unread_notifications' => $unread,
        ]);
    }
}
