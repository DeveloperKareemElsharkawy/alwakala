<?php

namespace App\Http\Resources\Dashboard\Orders;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderStoreResource extends JsonResource
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
            "store" => new StoreResource($this->store),
            "products" => OrderProductResource::collection($this->products),
            "total_price" => $this->total_price,
        ];
    }
}
