<?php

namespace App\Http\Requests\SellerApp\Cart;

use Illuminate\Foundation\Http\FormRequest;

class ApplyCouponRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'coupon_code' => ['required','exists:coupons,code'],
            // 'products.*.product_id' => 'required|numeric|exists:coupon_products,product_id,' . $this->user_id
        ];
    }
}
