<?php

namespace App\Http\Resources\Seller\Coupons;

use App\Http\Resources\Seller\Brands\BrandsResource;
use App\Http\Resources\Seller\SellerResource;
use App\Http\Resources\shared\UserResource;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
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
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'status' => (boolean)$this->active,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'unlimited' => $this->unlimited,
            'quantity' => $this->quantity,
            'brand' => $this->brand_id ? $this->when($this->brand_id ,  new BrandsResource($this->brand)) : '',
            'products' => $this->when($this->coupon_products, CouponProductResource::collection($this->coupon_products)),
            'discounts' => $this->when($this->discounts, CouponDiscountsResource::collection($this->discounts)),
            'user' => $this->when($this->seller_id , new SellerResource($this->user)),

        ];
    }
}
