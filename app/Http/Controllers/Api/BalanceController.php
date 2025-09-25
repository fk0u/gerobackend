<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BalanceEntry;
use App\Http\Resources\BalanceEntryResource;
use Illuminate\Http\Request;

class BalanceController extends Controller
{
    public function ledger(Request $request) {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'direction' => 'sometimes|in:credit,debit',
        ]);

        $perPage = min(max($request->integer('per_page', 50), 1), 200);
        $query = BalanceEntry::with('user')->where('user_id', $request->integer('user_id'));
        if ($request->filled('direction')) {
            $query->where('direction', $request->string('direction'));
        }
        $page = $query->orderByDesc('created_at')->paginate($perPage);
        $page->getCollection()->transform(fn($entry) => new BalanceEntryResource($entry));
        return response()->json($page);
    }

    public function summary(Request $request) {
        $request->validate(['user_id' => 'required|exists:users,id']);
        $userId = $request->integer('user_id');
        $credit = BalanceEntry::where('user_id', $userId)->where('direction', 'credit')->sum('amount');
        $debit = BalanceEntry::where('user_id', $userId)->where('direction', 'debit')->sum('amount');
        $balance = $credit - $debit;
        $recentEntries = BalanceEntryResource::collection(
            BalanceEntry::with('user')
                ->where('user_id', $userId)
                ->orderByDesc('created_at')
                ->limit(10)
                ->get()
        )->toArray($request);

        return response()->json([
            'user_id' => $userId,
            'balance' => $balance,
            'credit' => $credit,
            'debit' => $debit,
            'recent_entries' => $recentEntries,
        ]);
    }
}