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
            'title' => $this->title,
            'description' => $this->description,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'status' => $this->status,
            'assigned_to' => $this->assigned_to,
            'scheduled_at' => $this->scheduled_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'assigned_user' => new UserResource($this->whenLoaded('assignedUser')),
            'trackings_count' => $this->when(isset($this->trackings_count), $this->trackings_count),
            'trackings' => TrackingResource::collection($this->whenLoaded('trackings')),
        ];
    }
}
