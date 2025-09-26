<?php

namespace App\Models;

use App\Casts\AesEncrypted;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','mitra_id','service_id','schedule_id','address_text','latitude','longitude','status',
        'requested_at','completed_at','cancelled_at','notes','total_points','total_price'
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'requested_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'address_text' => AesEncrypted::class,
        'notes' => AesEncrypted::class,
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function mitra(): BelongsTo { return $this->belongsTo(User::class, 'mitra_id'); }
    public function service(): BelongsTo { return $this->belongsTo(Service::class); }
    public function schedule(): BelongsTo { return $this->belongsTo(Schedule::class); }
    public function payments(): HasMany { return $this->hasMany(Payment::class); }
    public function ratings(): HasMany { return $this->hasMany(Rating::class); }
}
