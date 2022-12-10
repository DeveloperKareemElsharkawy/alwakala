<?php

namespace App\Http\Resources\Seller\Coupons;

use App\Enums\DiscountTypes\DiscountTypes;
use App\Http\Resources\Seller\Brands\BrandsResource;
use App\Http\Resources\Seller\SellerResource;
use App\Http\Resources\shared\UserResource;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponDiscountsResource extends JsonResource
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
            'id' => $this->id,
            'amount_from' => (double)$this->amount_from,
            'amount_to' => (double)$this->amount_to,
            'discount_type' => DiscountTypes::getDiscountType($this->discount_type),
            'discount' => (double)$this->discount,

        ];
    }
}
