<?php

namespace App\Http\Resources\Seller\Coupons;

use App\Http\Resources\Seller\Orders\OrdersResource;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponPurchaseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'coupon_id' => $this->coupon_id,
            'order_id' => $this->order_id,
            "order" => $this->when($this->order, new OrdersResource($this->order)),
        ];
    }
}
