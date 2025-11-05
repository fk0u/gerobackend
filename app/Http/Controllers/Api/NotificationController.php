<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Http\Resources\NotificationResource;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request) {
        $q = Notification::query()->with('user');
        if ($request->filled('user_id')) {
            $q->where('user_id', $request->integer('user_id'));
        }
        if ($request->filled('role_scope')) {
            $q->where('role_scope', (string) $request->string('role_scope'));
        }
        if ($request->boolean('unread_only')) {
            $q->where('is_read', false);
        }
        $perPage = min(max($request->integer('per_page', 20), 1), 100);
        $page = $q->orderByDesc('created_at')->paginate($perPage);
        $page->getCollection()->transform(fn($n) => new NotificationResource($n));
        return response()->json($page);
    }

    public function store(Request $request) {
        $data = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'role_scope' => 'nullable|string',
            'title' => 'required|string',
            'body' => 'required|string',
            'type' => 'nullable|string',
        ]);
        $n = Notification::create($data);
        return response()->json(new NotificationResource($n->fresh('user')), 201);
    }

    public function markRead(Request $request) {
        $ids = $request->input('ids', []);
        if (is_array($ids) && count($ids)) {
            Notification::whereIn('id', $ids, 'and', false)->update(['is_read' => true, 'read_at' => now()]);
        }
        $updated = Notification::with('user')->whereIn('id', $ids, 'and', false)->get();
        return NotificationResource::collection($updated)->additional([
            'updated' => count($ids),
        ]);
    }

    public function show(int $id) {
        $notification = Notification::with('user')->findOrFail($id);
        return response()->json(new NotificationResource($notification));
    }

    public function update(Request $request, int $id) {
        $notification = Notification::findOrFail($id);
        
        // Users can update their own notifications
        $user = request()->user();
        if ($notification->user_id && $notification->user_id !== $user->id && $user->role !== 'admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'Forbidden: You can only update your own notifications'
            ], 403);
        }
        
        $data = $request->validate([
            'is_read' => 'sometimes|boolean',
            'read_at' => 'sometimes|nullable|date',
        ]);

        $notification->update($data);
        return response()->json(new NotificationResource($notification->fresh('user')));
    }

    public function destroy(int $id) {
        $notification = Notification::findOrFail($id);
        
        // Users can delete their own notifications
        $user = request()->user();
        if ($notification->user_id && $notification->user_id !== $user->id && $user->role !== 'admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'Forbidden: You can only delete your own notifications'
            ], 403);
        }
        
        $notification->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Notification deleted successfully'
        ], 200);
    }
}
