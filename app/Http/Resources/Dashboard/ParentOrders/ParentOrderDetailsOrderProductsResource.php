<?php

namespace App\Http\Resources\Dashboard\ParentOrders;

use Illuminate\Http\Resources\Json\JsonResource;

class ParentOrderDetailsOrderProductsResource extends JsonResource
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
            "purchased_item_count" => $this->purchased_item_count,
            "item_price" => $this->item_price,
            "color_id" => $this->color_id,
            "product" =>$this->product,
            "total_price" =>$this->total_price,
            "basic_unit_count" =>$this->basic_unit_count,
        ];
    }
}
