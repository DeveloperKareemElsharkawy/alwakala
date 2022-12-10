<?php

namespace App\Http\Resources\Dashboard\Offers;

use Illuminate\Http\Resources\Json\JsonResource;

class OfferProductResource extends JsonResource
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
            "product" => $this->when($this->product, new ProductResource($this->product)),
        ];
    }
}
