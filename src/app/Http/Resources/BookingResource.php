<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'room_id' => $this->room_id,
            'booking_date' => $this->booking_date->format('Y-m-d'),
            'created_at' => $this->created_at->toDateTimeString(),
            'room' => new RoomResource($this->whenLoaded('room')),
        ];
    }
}
