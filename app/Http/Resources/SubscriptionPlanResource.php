<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionPlanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $this->price,
            'formatted_price' => $this->formatted_price,
            'billing_cycle' => $this->billing_cycle,
            'billing_cycle_text' => $this->billing_cycle_text,
            'features' => $this->features,
            'max_schedules_per_month' => $this->max_schedules_per_month,
            'max_pickup_locations' => $this->max_pickup_locations,
            'priority_support' => $this->priority_support,
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}