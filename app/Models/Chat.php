<?php

namespace App\Models;

use App\Casts\AesEncrypted;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id','sender_id','receiver_id','message','message_type'
    ];

    protected $casts = [
        'message' => AesEncrypted::class,
    ];

    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function sender(): BelongsTo { return $this->belongsTo(User::class, 'sender_id'); }
    public function receiver(): BelongsTo { return $this->belongsTo(User::class, 'receiver_id'); }
}