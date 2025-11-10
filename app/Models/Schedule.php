<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
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
        'frequency',
        'waste_type',
        'estimated_weight',
        'contact_name',
        'contact_phone',
        'is_paid',
        'amount',
        // Legacy fields for backward compatibility
        'title',
        'description',
        'latitude',
        'longitude',
        'assigned_to',
        'assigned_at',
        'assigned_by',
        'accepted_at',
        'rejected_at',
        'rejection_reason',
        'confirmed_at',
        'started_at',
        'completed_at',
        'cancelled_at',
        'completion_notes',
        'actual_duration',
        'mitra_rating',
        'user_rating',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'estimated_duration' => 'integer',
    ];

    /**
     * Get attributes with safe decimal casting
     */
    protected function casts(): array
    {
        return [
            'pickup_latitude' => 'decimal:8',
            'pickup_longitude' => 'decimal:8',
            'price' => 'decimal:2',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'estimated_weight' => 'decimal:2',
            'amount' => 'decimal:2',
            'is_paid' => 'boolean',
        ];
    }

    public function trackings(): HasMany
    {
        return $this->hasMany(Tracking::class);
    }

    public function additionalWastes(): HasMany
    {
        return $this->hasMany(AdditionalWaste::class);
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
     * Accessor for safe decimal casting - prevents "Unable to cast value to decimal" error
     */
    protected function pickupLatitude(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value !== null && $value !== '' ? (string) $value : null,
        );
    }

    protected function pickupLongitude(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value !== null && $value !== '' ? (string) $value : null,
        );
    }

    protected function latitude(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value !== null && $value !== '' ? (string) $value : null,
        );
    }

    protected function longitude(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value !== null && $value !== '' ? (string) $value : null,
        );
    }

    protected function price(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value !== null && $value !== '' ? (string) $value : null,
        );
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
