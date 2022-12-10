<?php

namespace App\Http\Resources\Dashboard\ParentOrders;

use Illuminate\Http\Resources\Json\JsonResource;

class ParentOrderDetailsResource extends JsonResource
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
            "order_price" => $this->order_price,
            "created_at" => $this->created_at,
            "user" => $this->when($this->user, new UserResource($this->user)),
            "orders" => ParentOrderDetailsOrderResource::collection($this->orders),
        ];
    }
}
