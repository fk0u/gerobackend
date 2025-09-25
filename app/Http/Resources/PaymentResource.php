<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'user_id' => $this->user_id,
            'type' => $this->type,
            'method' => $this->method,
            'amount' => $this->amount,
            'status' => $this->status,
            'reference' => $this->reference,
            'paid_at' => $this->paid_at,
            'created_at' => $this->created_at,
            'order' => $this->whenLoaded('order', fn () => [
                'id' => $this->order?->id,
                'status' => $this->order?->status,
                'total_price' => $this->order?->total_price,
            ]),
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
