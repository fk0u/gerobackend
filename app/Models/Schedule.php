<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'mitra_id',
        'service_type',
        'pickup_address',
        'pickup_latitude',
        'pickup_longitude',
        'scheduled_at',
        'estimated_duration',
        'notes',
        'status',
        'payment_method',
        'price',
        // Legacy fields for backward compatibility
        'title',
        'description',
        'latitude',
        'longitude',
        'assigned_to',
    ];

    protected $casts = [
        'pickup_latitude' => 'decimal:8',
        'pickup_longitude' => 'decimal:8',
        'scheduled_at' => 'datetime',
        'price' => 'decimal:2',
        'estimated_duration' => 'integer',
        // Legacy casts
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function trackings(): HasMany
    {
        return $this->hasMany(Tracking::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function mitra(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mitra_id');
    }

    // Legacy relationships for backward compatibility
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Status helper methods
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }
}
