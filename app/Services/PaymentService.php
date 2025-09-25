<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\BalanceEntry;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function markPaid(Payment $payment): Payment
    {
        if ($payment->status === 'paid') {
            return $payment; // idempotent
        }
        DB::transaction(function () use ($payment) {
            $payment->status = 'paid';
            $payment->paid_at = now();
            $payment->save();
            BalanceEntry::create([
                'user_id' => $payment->user_id,
                'direction' => 'debit', // assuming payment reduces user balance (or change if topup)
                'amount' => $payment->amount,
                'source_type' => 'payment',
                'source_id' => $payment->id,
                'description' => 'Payment processed'
            ]);
        });
        return $payment;
    }
}
