<?php

namespace App\Http\Resources\Dashboard\Stock;

use Illuminate\Http\Resources\Json\JsonResource;

class StockResource extends JsonResource
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
            "stock" => $this->stock,
            "reserved_stock" => $this->reserved_stock,
            "available_stock" => $this->available_stock,
            "sold" => $this->sold,
            "returned" => $this->returned,
            'created_at' => $this->created_at,
            "product" => $this->when($this->product_store->product, new ProductResource($this->product_store->product)),
            "store" => $this->when($this->product_store->store, new StoreResource($this->product_store->store)),
            "color" => $this->when($this->color, new ColorResource($this->color)),
        ];
    }
}
