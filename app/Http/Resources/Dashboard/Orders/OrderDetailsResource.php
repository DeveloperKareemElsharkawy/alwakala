<?php

namespace App\Http\Resources\Dashboard\Orders;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailsResource extends JsonResource
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
            "total_price" => $this->total_price,
            "discount" => $this->discount,
            "created_at" => $this->created_at,
            "number" => $this->number,
            "user" => $this->when($this->user, new UserResource($this->user)),
            "last_status" => $this->when($this->last_status, new OrderStatusResource($this->last_status)),
            "payment_method" => $this->when($this->payment_method, new OrderPaymentMethodResource($this->payment_method)),
            "order_address" => $this->when($this->order_address, new OrderAddressResource($this->order_address)),
            "stores" => OrderStoreResource::collection($this->stores()),
        ];
    }
}
