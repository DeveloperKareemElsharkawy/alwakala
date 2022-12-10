<?php

namespace App\Http\Resources\Seller\Coupons;

use App\Http\Resources\Seller\ProductResource;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponProductResource extends JsonResource
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
            'product_id' => $this->product_id,
            'product' => $this->when($this->product, new ProductResource($this->product)),
        ];
    }
}
