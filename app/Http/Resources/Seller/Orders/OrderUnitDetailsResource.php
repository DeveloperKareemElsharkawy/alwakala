<?php

namespace App\Http\Resources\Seller\Orders;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderUnitDetailsResource extends JsonResource
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
            "quantity" => $this->quantity,
            "size" => $this->size,
        ];
    }
}
