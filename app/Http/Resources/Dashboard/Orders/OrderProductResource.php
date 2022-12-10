<?php

namespace App\Http\Resources\Dashboard\Orders;

use App\Http\Resources\Dashboard\Orders\OrderStatusResource;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderProductResource extends JsonResource
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
            "purchased_item_count" => $this->purchased_item_count,
            "item_price" => $this->item_price,
            "color" => $this->color,
            "unit_details" => OrderUnitDetailsResource::collection($this->unit_details),
            "product" => $this->product,
            "total_price" => $this->total_price,
            "basic_unit_count" => $this->basic_unit_count,
            "quantity" => $this->quantity,
            "last_status" => $this->when($this->last_status, new OrderStatusResource($this->last_status)),
        ];
    }
}
