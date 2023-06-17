<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'customer_id' => $this->id,
            'user_id' => $this->user_id,
            'fullname' => $this->fullname,
            'phone_number' => $this->phone_number,
            'state' => $this->state,
            'province' => $this->province,
            'city' => $this->city,
            'postal_code' => $this->postal_code,
            'address' => $this->address,
        ];
    }
}
