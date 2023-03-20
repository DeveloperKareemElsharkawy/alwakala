<?php

namespace App\Http\Resources\Seller\Cart;

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
            'product_name' => $this['product_name'],
            'product_image' => $this['image'] ? config('filesystems.aws_base_url') . $this['image'] : null,
            'unit_details'=>$this['unit_details'],
            'color' => $this['color_name'],
            'color_code' => $this['color_code'],
            'basic_unit_count' => $this['basic_unit_count'],
            'quantity' => $this['quantity'],
            'unit_price' => (float)$this['net_price'],
            'sub_total' => (float)$this['sub_total'],
            'item_count' => (float)$this['item_count'],

        ];
    }
}
