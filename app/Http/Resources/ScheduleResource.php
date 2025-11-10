<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScheduleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'mitra_id' => $this->mitra_id,
            'user_name' => $this->user?->name,
            'mitra_name' => $this->mitra?->name,
            'service_type' => $this->service_type,
            'pickup_address' => $this->pickup_address,
            'pickup_latitude' => $this->safeDecimal($this->pickup_latitude),
            'pickup_longitude' => $this->safeDecimal($this->pickup_longitude),
            'scheduled_at' => $this->scheduled_at?->toDateTimeString(),
            'estimated_duration' => $this->estimated_duration,
            'notes' => $this->notes,
            'status' => $this->status,
            'payment_method' => $this->payment_method,
            'price' => $this->safeDecimal($this->price),
            'frequency' => $this->frequency ?? 'once',
            'waste_type' => $this->waste_type,
            'estimated_weight' => $this->safeDecimal($this->estimated_weight),
            'contact_name' => $this->contact_name,
            'contact_phone' => $this->contact_phone,
            'is_paid' => $this->is_paid ?? false,
            'amount' => $this->safeDecimal($this->amount),
            'completion_notes' => $this->completion_notes,
            'actual_duration' => $this->actual_duration,
            'cancellation_reason' => $this->rejection_reason,
            'additional_wastes' => $this->whenLoaded('additionalWastes', function() {
                return $this->additionalWastes->map(function($waste) {
                    return [
                        'id' => $waste->id,
                        'waste_type' => $waste->waste_type,
                        'estimated_weight' => $this->safeDecimal($waste->estimated_weight),
                        'notes' => $waste->notes,
                        'created_at' => $waste->created_at?->toDateTimeString(),
                    ];
                });
            }),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'completed_at' => $this->completed_at?->toDateTimeString(),
            'cancelled_at' => $this->cancelled_at?->toDateTimeString(),
            'confirmed_at' => $this->confirmed_at?->toDateTimeString(),
            'started_at' => $this->started_at?->toDateTimeString(),
            'assigned_at' => $this->assigned_at?->toDateTimeString(),
            'accepted_at' => $this->accepted_at?->toDateTimeString(),
            'rejected_at' => $this->rejected_at?->toDateTimeString(),
            // Legacy fields for backward compatibility
            'title' => $this->service_type ?? 'Pickup Service',
            'description' => $this->notes,
            'latitude' => $this->safeDecimal($this->latitude ?? $this->pickup_latitude),
            'longitude' => $this->safeDecimal($this->longitude ?? $this->pickup_longitude),
            'assigned_to' => $this->mitra_id,
            'assigned_user' => new UserResource($this->whenLoaded('mitra')),
            'trackings_count' => $this->when(isset($this->trackings_count), $this->trackings_count),
            'trackings' => TrackingResource::collection($this->whenLoaded('trackings')),
        ];
    }

    /**
     * Safely convert value to float, handling null/empty strings
     */
    private function safeDecimal($value): ?float
    {
        if ($value === null || $value === '' || $value === '0') {
            return null;
        }

        // Check if it's a valid number
        if (is_numeric($value)) {
            return (float) $value;
        }

        return null;
    }
}
