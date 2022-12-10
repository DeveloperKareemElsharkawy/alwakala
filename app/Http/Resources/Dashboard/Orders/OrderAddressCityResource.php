<?php

namespace App\Http\Resources\Dashboard\Orders;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderAddressCityResource extends JsonResource
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
            "name" => $this->name_en,
        ];
    }
}
