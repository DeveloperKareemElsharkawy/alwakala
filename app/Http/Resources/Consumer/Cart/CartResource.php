<?php

namespace App\Http\Resources\Consumer\Cart;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
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
            // 'id' => $this['id'],
            'stores' => CartStoresResource::collection($this['stores']),
            'cart_total' => intval(($this['cart_total'] * 100)) / 100,
            'cart_count' => $this['cart_count'],
            'item_count' => $this['item_count'],
            'has_coupon' => $this['coupon']['has_coupon'],
            'coupon' => [
                'coupon_name' => $this['coupon']['coupon_name'],
                'coupon_code' => $this['coupon']['coupon_code'],
                'coupon_products_count' => $this['coupon']['coupon_products_count'],
                'coupon_products' => $this['coupon']['coupon_products'],
                'discountAmount' => $this['coupon']['discountAmount'],
                'couponAmount' => $this['coupon']['couponAmount'],
                'discountType' => $this['coupon']['discountType'],
            ]
        ];
    }
}
