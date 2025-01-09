<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ReservationDetailsResponse extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "name" => $this->reservation->name,
            "room" => $this->room->name,
            "room_id" => $this->room_id,
            "reservation_id" => $this->reservation_id,
            "email" => $this->reservation->email,
            "address" => $this->reservation->address,
            "phone" => $this->reservation->phone,
            "nationality" => $this->reservation->nationality,
            "type" => $this->reservation->type,
            "start_date" => $this->check_in_date,
            "end_date" => $this->check_out_date,
            "remarks" => $this->reservation->remarks,
            "status" => $this->reservation->status,
        ];
    }
}
