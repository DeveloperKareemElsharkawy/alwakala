<?php

namespace App\Http\Resources\Seller\Offers;

use App\Http\Resources\Dashboard\Orders\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class OfferStoresStatusesResource extends JsonResource
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
            "store" => new OfferStoreResource($this->store),
            "offer" => new OfferMiniDataResource($this->offer),
            'status' => $this->status,
            'is_approved' => (bool)$this->status == "approved"
        ];
    }
}
