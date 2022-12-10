<?php

namespace App\Http\Resources\Seller;

use Illuminate\Http\Resources\Json\JsonResource;

class InventoryResource extends JsonResource
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
            "product_name" => $this->product_name,
            "product_id" => $this->product_id,
            "color" => $this->color,
            "size" => $this->size,
            "stock" => $this->stock,
            "available_stock" => $this->available_stock,
            "reserved_stock" => $this->reserved_stock,
            "sold" => $this->sold,
            "publish_at_date" => $this->publish_at_date,
            "item_price" => $this->item_price,
            "image" => $this->image,
            "stock_status" => $this->stock_status
        ];
    }
}
