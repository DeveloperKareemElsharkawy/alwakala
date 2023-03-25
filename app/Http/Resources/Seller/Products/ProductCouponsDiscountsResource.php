<?php

namespace App\Http\Resources\Seller\Products;

use App\Enums\DiscountTypes\DiscountTypes;
use App\Lib\Helpers\Lang\LangHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductCouponsDiscountsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        $lang = LangHelper::getDefaultLang($request);

        return [
            'id' => $this->id,
            'buy_up_to_price' => $this->amount_to,
            'discount_type' => DiscountTypes::getDiscountType($this->discount_type),
            'discount_amount' => $this->discount,
            'offer_title' => trans('messages.coupon.offer_title', ['amount' => $this->discount]),
            'offer_sub_title' => trans('messages.coupon.offer_sub_title', ['amount' => $this->amount_to]),
            'offer_description' => $this->{'description_' . $lang},
        ];
    }
}
