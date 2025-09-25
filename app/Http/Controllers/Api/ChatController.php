<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Http\Resources\ChatResource;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index(Request $request) {
        $q = Chat::query()->with(['order', 'sender', 'receiver']);
        if ($request->filled('order_id')) $q->where('order_id', $request->integer('order_id'));
        if ($request->filled('user_id') && $request->filled('peer_id')) {
            $u = $request->integer('user_id');
            $p = $request->integer('peer_id');
            $q->where(function ($qq) use ($u, $p) {
                $qq->where(function ($x) use ($u, $p) { $x->where('sender_id', $u)->where('receiver_id', $p); })
                   ->orWhere(function ($x) use ($u, $p) { $x->where('sender_id', $p)->where('receiver_id', $u); });
            });
        }
        $limit = min(max($request->integer('limit', 200), 1), 500);
        $messages = $q->orderByDesc('created_at')->limit($limit)->get()->reverse()->values();
        return ChatResource::collection($messages);
    }

    public function store(Request $request) {
        $data = $request->validate([
            'order_id' => 'nullable|exists:orders,id',
            'sender_id' => 'required|exists:users,id',
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string',
            'message_type' => 'nullable|string',
        ]);
        $chat = Chat::create($data);
        return response()->json(new ChatResource($chat->load(['order', 'sender', 'receiver'])), 201);
    }
}