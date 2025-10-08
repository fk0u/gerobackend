<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Schedule;
use App\Http\Resources\OrderResource;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $q = Order::query()->with([
            'user','mitra','service','schedule.assignedUser','payments','ratings'
        ]);
        if ($request->filled('user_id')) $q->where('user_id', $request->integer('user_id'));
        if ($request->filled('mitra_id')) $q->where('mitra_id', $request->integer('mitra_id'));
        if ($request->filled('status')) $q->where('status', $request->string('status'));
        $page = $q->latest()->paginate(20);
        $page->getCollection()->transform(fn($o) => new OrderResource($o));
        return response()->json($page);
    }

    public function show(int $id)
    {
        $order = Order::with([
            'user','mitra','service','schedule.assignedUser','payments','ratings'
        ])->findOrFail($id);
        return response()->json(new OrderResource($order));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'service_id' => 'nullable|exists:services,id',
            'address_text' => 'required|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'notes' => 'nullable|string',
        ]);
        $data['requested_at'] = now();
        $data['status'] = 'pending';
        $order = Order::create($data);

        // optional: create schedule automatically (simplified)
        $schedule = Schedule::create([
            'title' => 'Order #'.$order->id,
            'description' => $data['notes'] ?? null,
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'status' => 'pending',
            'assigned_to' => null,
            'scheduled_at' => Carbon::now()->addHour(),
        ]);
        $order->schedule_id = $schedule->id;
        $order->save();

        return response()->json(
            new OrderResource($order->fresh([
                'user','mitra','service','schedule.assignedUser','payments','ratings'
            ])),
            201
        );
    }

    public function assign(Request $request, int $id)
    {
        $order = Order::with(['user','mitra','service','schedule.assignedUser','payments','ratings'])->findOrFail($id);
        $data = $request->validate([
            'mitra_id' => 'required|exists:users,id',
        ]);
        $order->mitra_id = $data['mitra_id'];
        if (!in_array($order->status, [null,'pending','assigned'])) {
            return response()->json(['error' => ['message' => 'Cannot assign in current status']], 422);
        }
        $order->status = 'assigned';
        $order->save();
        return response()->json(new OrderResource($order->fresh([
            'user','mitra','service','schedule.assignedUser','payments','ratings'
        ])));
    }

    public function updateStatus(Request $request, int $id, OrderService $service)
    {
        $order = Order::with(['user','mitra','service','schedule.assignedUser','payments','ratings'])->findOrFail($id);
        $data = $request->validate([
            'status' => 'required|string|in:assigned,in_progress,completed,cancelled',
        ]);
        $service->updateStatus($order, $data['status']);
        return response()->json(new OrderResource($order->fresh([
            'user','mitra','service','schedule.assignedUser','payments','ratings'
        ])));
    }

    public function cancel(Request $request, int $id)
    {
        $order = Order::with(['user','mitra','service','schedule.assignedUser','payments','ratings'])->findOrFail($id);
        
        // Check if user owns this order
        if ($order->user_id !== Auth::id()) {
            return response()->json(['error' => ['message' => 'Unauthorized to cancel this order']], 403);
        }
        
        // Check if order can be cancelled
        if (!in_array($order->status, ['pending', 'assigned'])) {
            return response()->json(['error' => ['message' => 'Cannot cancel order in current status']], 422);
        }
        
        $order->status = 'cancelled';
        $order->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Order cancelled successfully',
            'data' => new OrderResource($order->fresh([
                'user','mitra','service','schedule.assignedUser','payments','ratings'
            ]))
        ]);
    }
}
