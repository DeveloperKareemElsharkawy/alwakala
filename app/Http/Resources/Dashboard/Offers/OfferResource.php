<?php

namespace App\Http\Resources\Dashboard\Offers;

use App\Http\Resources\Dashboard\Orders\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class OfferResource extends JsonResource
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
            "activation" => $this->activation,
            "from" => $this->from,
            "to" => $this->to,
            'created_at' => $this->created_at,
            "owner" => $this->when($this->user, $this->user->stores),
            "type" => $this->when($this->type, $this->type),
            "bulk_price" => $this->bulk_price,
            "retail_price" => $this->retail_price,
            "products" => $this->when($this->offers_products, OfferProductResource::collection($this->offers_products)),
             "user" => $this->when($this->user , UserResource::collection($this->user)),
        ];
    }
}
