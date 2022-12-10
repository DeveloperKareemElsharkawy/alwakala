<?php

namespace App\Http\Resources\Seller;

use Illuminate\Http\Resources\Json\JsonResource;

class StoreFixedProductResource extends JsonResource
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
            "category_id" => $this->category_id,
            "image" => $this->image,
            "price" => $this->price,
            "net_price" => $this->net_price,
            "discount" => $this->discount,
            "price_range" => $this->price_range,

        ];
    }
}
