<?php

namespace App\Http\Resources\Dashboard\ParentOrders;

use Illuminate\Http\Resources\Json\JsonResource;

class ParentOrderDetailsOrderResource extends JsonResource
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
            "total_price" => $this->total_price,
            "delivery_date" => $this->delivery_date,
            "order_address" => $this->order_address,
            "products" => $this->when($this->items,ParentOrderDetailsOrderProductsResource::collection($this->items)),
            "store" => $this->when($this->store, new ParentOrderDetailsOrderStoreResource($this->store)),
            "status" => $this->when($this->status, new ParentOrderDetailsOrderStatusResource($this->status)),
            "payment_method" => $this->when($this->payment_method,new ParentOrderDetailsOrderPaymentMethodResource($this->payment_method)),
        ];
    }
}
