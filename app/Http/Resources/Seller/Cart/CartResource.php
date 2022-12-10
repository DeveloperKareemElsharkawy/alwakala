<?php

namespace App\Http\Resources\Seller\Cart;

use App\Http\Resources\Seller\Coupons\CouponResource;
use App\Lib\Helpers\Lang\LangHelper;
use Illuminate\Contracts\Support\Arrayable;
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
            // 'coupon' => $this->when($this->coupon, CouponResource::collection($this->coupon))
        ];
    }
}
