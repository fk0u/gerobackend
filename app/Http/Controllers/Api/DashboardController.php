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
        $active = Order::where('mitra_id', $mitraId)->whereNotIn('status', ['completed','cancelled'])->count();
        $todayCompleted = Order::where('mitra_id', $mitraId)->whereDate('completed_at', $today)->count();
        $points = BalanceEntry::where('user_id', $mitraId)->where('direction','credit')->sum('amount');
        $unread = Notification::where('user_id', $mitraId)->where('is_read', false)->count();
        return response()->json([
            'active_orders_count' => $active,
            'today_completed' => $todayCompleted,
            'total_points_earned' => $points,
            'unread_notifications' => $unread,
        ]);
    }

    public function user(int $userId)
    {
        $active = Order::where('user_id', $userId)->whereNotIn('status', ['completed','cancelled'])->count();
        $points = BalanceEntry::where('user_id', $userId)->sum('amount');
        $unread = Notification::where('user_id', $userId)->where('is_read', false)->count();
        return response()->json([
            'active_requests' => $active,
            'total_points' => $points,
            'unread_notifications' => $unread,
        ]);
    }
}
