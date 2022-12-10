<?php

namespace App\Http\Resources\Seller;

use Illuminate\Http\Resources\Json\JsonResource;

class StoreResource extends JsonResource
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
            "store_type_id" => $this->store_type_id,
            "name" => $this->name,
            "rate" => $this->rate,
            "logo" => $this->logo,
            "is_followed" => $this->is_followed,
            "number_of_followers" => $this->number_of_followers,
            "number_of_views" => $this->number_of_views,
            "products" => StoreFixedProductResource::collection($this->products),
        ];
    }
}
