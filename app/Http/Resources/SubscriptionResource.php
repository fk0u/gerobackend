<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'subscription_plan_id' => $this->subscription_plan_id,
            'status' => $this->status,
            'starts_at' => $this->starts_at?->toISOString(),
            'ends_at' => $this->ends_at?->toISOString(),
            'auto_renew' => $this->auto_renew,
            'payment_method' => $this->payment_method,
            'amount' => $this->amount,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            
            // Additional computed fields
            'is_active' => $this->isActive(),
            'is_expired' => $this->isExpired(),
            'days_remaining' => $this->daysRemaining(),
            
            // Relationships
            'subscription_plan' => new SubscriptionPlanResource($this->whenLoaded('subscriptionPlan')),
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}