<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TrackingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'schedule_id' => $this->schedule_id,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'speed' => $this->speed,
            'heading' => $this->heading,
            'recorded_at' => $this->recorded_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'schedule' => new ScheduleResource($this->whenLoaded('schedule')),
        ];
    }
}
