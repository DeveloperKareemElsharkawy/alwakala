<?php

namespace App\Http\Resources\Seller;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            "store_id" => $this->store_id,
            "name" => $this->name,
            "price_range" => $this->price_range,
            'consumer_price' => $this->consumer_price,
            "store_name" => $this->store_name,
            "created_at" => $this->created_at,
            "rate" => $this->rate,
            "image" => $this->productImage['image_full'] ?? null,
        ];
    }
}
