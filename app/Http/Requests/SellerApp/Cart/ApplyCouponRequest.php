<?php

namespace App\Http\Requests\SellerApp\Cart;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

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
            'coupon_code' => ['required', 'exists:coupons,code'],
            'share_coupon_code' => ['sometimes'],
            'has_share_coupon_code' => ['required'],
        ];
    }

    protected function prepareForValidation()
    {
        $coupon_code = $this->input('coupon_code');
        $has_share_coupon = false;
        $share_coupon_code = $this->input('coupon_code');

        if (Str::contains($this->input('coupon_code'), '_share_from_')) {
            $coupon_code = strstr($coupon_code, '_share_from_', true);
            $share_coupon_code = substr(strrchr($share_coupon_code, '_share_from_'),1);
            $has_share_coupon = true;
        }

        $this->merge([
            'coupon_code' => $coupon_code,
            'share_coupon_code' => $share_coupon_code,
            'has_share_coupon_code' => $has_share_coupon,
        ]);
    }
}
