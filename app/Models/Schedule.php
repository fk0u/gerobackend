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
        'title',
        'description',
        'latitude',
        'longitude',
        'status',
        'assigned_to',
        'scheduled_at',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'scheduled_at' => 'datetime',
    ];

    public function trackings(): HasMany
    {
        return $this->hasMany(Tracking::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
