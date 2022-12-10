<?php

namespace App\Http\Resources\Seller\Offers;

use App\Http\Resources\Dashboard\Orders\UserResource;
use App\Http\Resources\Seller\Store\StoreMiniDataResource;
use App\Http\Resources\Seller\StoreResource;
use App\Models\Product;
use App\Repositories\StoreRepository;
use Illuminate\Http\Resources\Json\JsonResource;

class OfferStoreResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "logo" => $this->logos ? config('filesystems.aws_base_url') . $this->logos : null,
            "store_type_id" => $this->store_type_id,
        ];
    }
}
