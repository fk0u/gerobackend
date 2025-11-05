<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use App\Models\Order;
use App\Http\Resources\RatingResource;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class RatingController extends Controller
{
    public function index(Request $request) {
        $q = Rating::query()->with(['order','user','mitra']);
        if ($request->filled('mitra_id')) {
            $q->where('mitra_id', $request->integer('mitra_id'));
        }
        if ($request->filled('order_id')) {
            $q->where('order_id', $request->integer('order_id'));
        }
        $perPage = min(max($request->integer('per_page', 20), 1), 100);
        $page = $q->latest()->paginate($perPage);
        $page->getCollection()->transform(fn($r) => new RatingResource($r));
        return response()->json($page);
    }

    public function store(Request $request) {
        $data = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'user_id' => 'required|exists:users,id',
            'score' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $order = Order::with('mitra')->findOrFail($data['order_id']);
        if ($order->user_id !== $data['user_id']) {
            throw ValidationException::withMessages([
                'user_id' => ['User is not allowed to rate this order.']
            ]);
        }
        if ($order->status !== 'completed') {
            throw ValidationException::withMessages([
                'order_id' => ['Order must be completed before rating.']
            ]);
        }
        if (! $order->mitra_id) {
            throw ValidationException::withMessages([
                'order_id' => ['Order has no assigned mitra to rate.']
            ]);
        }
        $exists = Rating::where('order_id', $order->id)
            ->where('user_id', $data['user_id'])
            ->exists();
        if ($exists) {
            throw ValidationException::withMessages([
                'order_id' => ['You have already rated this order.']
            ]);
        }

        $rating = Rating::create([
            'order_id' => $order->id,
            'user_id' => $data['user_id'],
            'mitra_id' => $order->mitra_id,
            'score' => $data['score'],
            'comment' => $data['comment'] ?? null,
        ]);

        return response()->json(new RatingResource($rating->load(['order','user','mitra'])), 201);
    }

    public function show(int $id) {
        $rating = Rating::with(['order','user','mitra'])->findOrFail($id);
        return response()->json(new RatingResource($rating));
    }

    public function update(Request $request, int $id) {
        $rating = Rating::findOrFail($id);
        
        // Only the user who created the rating can update it
        if ($rating->user_id !== request()->user()->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Forbidden: You can only update your own ratings'
            ], 403);
        }
        
        $data = $request->validate([
            'score' => 'sometimes|integer|min:1|max:5',
            'comment' => 'sometimes|nullable|string',
        ]);

        $rating->update($data);
        return response()->json(new RatingResource($rating->fresh(['order','user','mitra'])));
    }

    public function destroy(int $id) {
        $rating = Rating::findOrFail($id);
        
        // Only the user who created the rating can delete it
        if ($rating->user_id !== request()->user()->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Forbidden: You can only delete your own ratings'
            ], 403);
        }
        
        $rating->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Rating deleted successfully'
        ], 200);
    }
}