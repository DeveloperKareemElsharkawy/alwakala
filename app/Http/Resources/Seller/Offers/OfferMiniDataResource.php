<?php

namespace App\Http\Resources\Seller\Offers;

use App\Http\Resources\Dashboard\Orders\UserResource;
use App\Http\Resources\Seller\Store\StoreMiniDataResource;
use App\Http\Resources\Seller\StoreResource;
use App\Models\Product;
use App\Repositories\StoreRepository;
use Illuminate\Http\Resources\Json\JsonResource;

class OfferMiniDataResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {

        $store = StoreRepository::getStoreByUserId($this->user_id);

        return [
            "id" => $this->id,
            "name" => $this->name,
            "description" => $this->description,
            'created_at' => $this->created_at,
            "products" => $this->when($this->offers_products, OfferProductResource::collection($this->offers_products)),

        ];
    }
}
