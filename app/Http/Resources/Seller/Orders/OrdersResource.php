<?php

namespace App\Http\Resources\Seller\Orders;

use Illuminate\Http\Resources\Json\JsonResource;

class OrdersResource extends JsonResource
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
            'color_key' => $this->color_key,
            "discount" => $this->discount,
            "created_at" => $this->created_at,
            "number" => $this->number,
            "product_count" => $this->items()->sum('quantity'),
            "status" => $this->when($this->last_status, new OrderStatusResource($this->last_status)),
            "order_address" => $this->when($this->order_address, new OrderAddressResource($this->order_address)),
            'product' => new ProductsListResource(collect($this->products)->first()),
            'products_more_counter' => $this->productsMoreCounter()
        ];
    }


    public function productsMoreCounter()
    {
        if (count($this->products) > 3) {
            return count($this->products) - 3;
        }
        return 0;
    }
}
