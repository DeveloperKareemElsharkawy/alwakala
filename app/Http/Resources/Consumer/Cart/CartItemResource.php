<?php

namespace App\Http\Resources\Consumer\Cart;

use App\Http\Resources\Consumer\Product\Relations\ProductColorResource;
use App\Http\Resources\Seller\Sizes\SizesResource;
use App\Lib\Helpers\Lang\LangHelper;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'cart_item_id' => $this['id'],
            'product_id' => $this['product_id'],
            'product_name' => $this['product']['name'],
            'product_image' => $this['image'] ? config('filesystems.aws_base_url') . $this['image'] : null,
            'quantity' => $this['quantity'],
            'unit_price' =>  $this['item_price'],
            'sub_total' => (float)$this['sub_total'],
            'item_count' => (float)$this['item_count'],
            'color' => new ProductColorResource($this->color),
            'size' => new SizesResource($this->size)
        ];
    }
}
