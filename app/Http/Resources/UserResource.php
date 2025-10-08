<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /** @return array<string,mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'profile_picture' => $this->profile_picture,
            'phone' => $this->phone,
            'address' => $this->address,
            'subscription_status' => $this->subscription_status,
            'points' => $this->points ?? 0,
            'employee_id' => $this->employee_id,
            'vehicle_type' => $this->vehicle_type,
            'vehicle_plate' => $this->vehicle_plate,
            'work_area' => $this->work_area,
            'status' => $this->status ?? 'active',
            'rating' => $this->rating ? number_format($this->rating, 2) : null,
            'total_collections' => $this->total_collections ?? 0,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
