<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RatingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'user_id' => $this->user_id,
            'mitra_id' => $this->mitra_id,
            'score' => $this->score,
            'comment' => $this->comment,
            'created_at' => $this->created_at,
            'order' => $this->whenLoaded('order', fn () => [
                'id' => $this->order?->id,
                'status' => $this->order?->status,
            ]),
            'user' => new UserResource($this->whenLoaded('user')),
            'mitra' => new UserResource($this->whenLoaded('mitra')),
        ];
    }
}
