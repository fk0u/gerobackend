<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BalanceEntry;
use App\Http\Resources\BalanceEntryResource;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BalanceController extends Controller
{
    use ApiResponseTrait;
    
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

    public function topup(Request $request)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:10000|max:10000000', // Min 10k, Max 10M
            'payment_method' => 'required|string|in:bank_transfer,e_wallet,credit_card',
            'payment_reference' => 'required|string|max:100',
        ]);

        $user = Auth::user();
        
        try {
            DB::beginTransaction();
            
            // Create balance entry
            $balanceEntry = BalanceEntry::create([
                'user_id' => $user->id,
                'amount' => $data['amount'],
                'direction' => 'credit',
                'description' => 'Top-up via ' . $data['payment_method'],
                'reference' => $data['payment_reference'],
                'metadata' => json_encode([
                    'payment_method' => $data['payment_method'],
                    'processed_at' => now(),
                ]),
            ]);
            
            DB::commit();
            
            return $this->successResponse(
                new BalanceEntryResource($balanceEntry->load('user')),
                'Top-up successful',
                201
            );
            
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Top-up failed: ' . $e->getMessage(), 500);
        }
    }

    public function withdraw(Request $request)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:50000', // Min 50k withdrawal
            'bank_account' => 'required|string|max:50',
            'bank_name' => 'required|string|max:100',
        ]);

        $user = Auth::user();
        
        // Check current balance
        $credit = BalanceEntry::where('user_id', $user->id)
            ->where('direction', 'credit')->sum('amount');
        $debit = BalanceEntry::where('user_id', $user->id)
            ->where('direction', 'debit')->sum('amount');
        $currentBalance = $credit - $debit;
        
        if ($currentBalance < $data['amount']) {
            return $this->errorResponse('Insufficient balance', 422);
        }
        
        try {
            DB::beginTransaction();
            
            // Create withdrawal entry
            $balanceEntry = BalanceEntry::create([
                'user_id' => $user->id,
                'amount' => $data['amount'],
                'direction' => 'debit',
                'description' => 'Withdrawal to ' . $data['bank_name'],
                'reference' => 'WD-' . time() . '-' . $user->id,
                'metadata' => json_encode([
                    'bank_account' => $data['bank_account'],
                    'bank_name' => $data['bank_name'],
                    'status' => 'pending',
                    'requested_at' => now(),
                ]),
            ]);
            
            DB::commit();
            
            return $this->successResponse(
                new BalanceEntryResource($balanceEntry->load('user')),
                'Withdrawal request submitted',
                201
            );
            
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Withdrawal failed: ' . $e->getMessage(), 500);
        }
    }
}