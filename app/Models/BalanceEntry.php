<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BalanceEntry extends Model
{
    use HasFactory;

    protected $table = 'balance_ledger';

    protected $fillable = [
        'user_id','direction','amount','source_type','source_id','description'
    ];

    protected $casts = [
        'amount' => 'integer',
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
