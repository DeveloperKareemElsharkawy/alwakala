<?php

namespace App\Http\Resources\Seller\Orders1;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderRequestedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "from" => $this->from,
            "to" => $this->to,
            'created_at' => $this->created_at,
        ];
    }
}
