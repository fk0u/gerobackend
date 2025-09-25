<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Http\Resources\PaymentResource;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request) {
        $q = Payment::query()->with(['order','user']);
        if ($request->filled('user_id')) $q->where('user_id', $request->integer('user_id'));
        if ($request->filled('type')) $q->where('type', $request->string('type'));
        if ($request->filled('status')) $q->where('status', $request->string('status'));
        $perPage = min(max($request->integer('per_page', 20), 1), 100);
        $page = $q->latest()->paginate($perPage);
        $page->getCollection()->transform(fn($p) => new PaymentResource($p));
        return response()->json($page);
    }

    public function store(Request $request) {
        $data = $request->validate([
            'order_id' => 'nullable|exists:orders,id',
            'user_id' => 'required|exists:users,id',
            'type' => 'required|string',
            'method' => 'required|string',
            'amount' => 'required|integer|min:0',
        ]);
        $payment = Payment::create($data);
        return response()->json(new PaymentResource($payment->load(['order','user'])), 201);
    }

    public function update(Request $request, int $id) {
        $payment = Payment::with(['order','user'])->findOrFail($id);
        $data = $request->validate([
            'status' => 'sometimes|string',
            'reference' => 'sometimes|nullable|string',
            'paid_at' => 'sometimes|nullable|date',
        ]);
        $payment->update($data);
        return response()->json(new PaymentResource($payment->fresh(['order','user'])));
    }

    public function markPaid(int $id, PaymentService $service) {
        $payment = Payment::with(['order','user'])->findOrFail($id);
        $service->markPaid($payment);
        return response()->json(new PaymentResource($payment->fresh(['order','user'])));
    }
}
