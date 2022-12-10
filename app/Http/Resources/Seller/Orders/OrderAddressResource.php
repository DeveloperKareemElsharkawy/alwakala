<?php

namespace App\Http\Resources\Seller\Orders;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderAddressResource extends JsonResource
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
            "name" => $this->name,
            "address" => $this->address,
            "latitude" => $this->latitude,
            "longitude" => $this->longitude,
            "building_no" => $this->building_no,
            "landmark" => $this->landmark,
            "main_street" => $this->main_street,
            "side_street" => $this->side_street,
            "mobile" => $this->mobile,
            "city" => $this->when($this->city, new OrderAddressCityResource($this->city)),
        ];
    }
}
