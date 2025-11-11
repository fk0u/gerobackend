<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Notification;
use App\Models\BalanceEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics for authenticated user
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        if ($user->role === 'mitra') {
            return $this->mitra($user->id);
        } else {
            return $this->user($user->id);
        }
    }

    public function mitra(int $mitraId)
    {
        $today = Carbon::today();
        $active = Order::where('mitra_id', $mitraId)
            ->whereNotIn('status', ['completed','cancelled'], 'and', false)
            ->count('*');
        $todayCompleted = Order::where('mitra_id', $mitraId)
            ->whereDate('completed_at', ' =>', $today, 'and')
            ->count('*');
        $points = BalanceEntry::where('user_id', $mitraId)
            ->where('direction', 'credit')
            ->sum('amount');
        $unread = Notification::where('user_id', $mitraId)
            ->where('is_read', false)
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
        $active = Order::where('user_id', $userId)
            ->whereNotIn('status', ['completed','cancelled'], 'and', false)
            ->count('*');
        $points = BalanceEntry::where('user_id', $userId)
            ->sum('amount');
        $unread = Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->count('*');
        return response()->json([
            'active_requests' => $active,
            'total_points' => $points,
            'unread_notifications' => $unread,
        ]);
    }
}
