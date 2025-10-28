<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdditionalWaste extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'schedule_id',
        'waste_type',
        'estimated_weight',
        'notes',
    ];

    protected $casts = [
        'estimated_weight' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the schedule that owns this additional waste
     */
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }
}
