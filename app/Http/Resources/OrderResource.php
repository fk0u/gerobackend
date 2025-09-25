<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'mitra_id' => $this->mitra_id,
            'service_id' => $this->service_id,
            'schedule_id' => $this->schedule_id,
            'address_text' => $this->address_text,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'status' => $this->status,
            'requested_at' => $this->requested_at,
            'completed_at' => $this->completed_at,
            'cancelled_at' => $this->cancelled_at,
            'notes' => $this->notes,
            'total_points' => $this->total_points,
            'total_price' => $this->total_price,
            'user' => new UserResource($this->whenLoaded('user')),
            'mitra' => new UserResource($this->whenLoaded('mitra')),
            'service' => new ServiceResource($this->whenLoaded('service')),
            'schedule' => new ScheduleResource($this->whenLoaded('schedule')),
            'payments' => PaymentResource::collection($this->whenLoaded('payments')),
            'ratings' => RatingResource::collection($this->whenLoaded('ratings')),
        ];
    }
}
