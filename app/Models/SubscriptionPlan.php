<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'billing_cycle', // monthly, yearly
        'features',
        'max_schedules_per_month',
        'max_pickup_locations',
        'priority_support',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'features' => 'array',
        'max_schedules_per_month' => 'integer',
        'max_pickup_locations' => 'integer',
        'priority_support' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class)->where('status', 'active');
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format((float)$this->price, 0, ',', '.');
    }

    public function getBillingCycleTextAttribute(): string
    {
        return match($this->billing_cycle) {
            'monthly' => 'per bulan',
            'yearly' => 'per tahun',
            default => $this->billing_cycle,
        };
    }
}