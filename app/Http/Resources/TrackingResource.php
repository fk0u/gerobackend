<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TrackingResource extends JsonResource
{
    /**
     * Safely cast decimal values, return null if invalid
     */
    private function safeDecimal($value, int $precision = 7): ?float
    {
        if ($value === null || $value === '' || $value === '0' || $value === 0) {
            return null;
        }
        
        return is_numeric($value) ? round((float) $value, $precision) : null;
    }

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'schedule_id' => $this->schedule_id,
            'latitude' => $this->safeDecimal($this->latitude, 7),
            'longitude' => $this->safeDecimal($this->longitude, 7),
            'speed' => $this->safeDecimal($this->speed, 2),
            'heading' => $this->safeDecimal($this->heading, 2),
            'recorded_at' => $this->recorded_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'schedule' => new ScheduleResource($this->whenLoaded('schedule')),
        ];
    }
}
