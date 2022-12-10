<?php

namespace App\Http\Resources\Dashboard\ParentOrders;

use Illuminate\Http\Resources\Json\JsonResource;

class ParentOrderDetailsOrderStatusResource extends JsonResource
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
            "status_en" => $this->status_en,
        ];
    }
}
