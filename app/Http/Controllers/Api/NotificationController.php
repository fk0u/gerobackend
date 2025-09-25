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
            $q->where('role_scope', $request->string('role_scope'));
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
            Notification::whereIn('id', $ids)->update(['is_read' => true, 'read_at' => now()]);
        }
        $updated = Notification::with('user')->whereIn('id', $ids)->get();
        return NotificationResource::collection($updated)->additional([
            'updated' => count($ids),
        ]);
    }
}
