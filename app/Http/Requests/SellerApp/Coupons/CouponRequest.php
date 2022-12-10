<?php

namespace App\Http\Requests\SellerApp\Coupons;

use App\Models\Store;
use Illuminate\Foundation\Http\FormRequest;

class CouponRequest extends FormRequest
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

    protected function onCreate()
    {
        // dd($this->store_id);
        return [
            'name' => ['required' , 'string' , 'max:255'],
            'code' => ['required' , 'unique:coupons,code'],
//            'type' => ['required'],
            'percentage' => ['required_if:amount,null'],
            'amount' => ['required_if:percentage,null'],
            'quantity' => $this->unlimited == 0 ? ['required' , 'numeric'] : '',
            'unlimited' => 'required|boolean',
            'start_date' => ['required' , 'date'],
            'end_date' => ['required' , 'date'],
//            'purchased_amount' => ['required' , 'numeric'],
            'brand_id' => ['required_if:products,null'],
            'products' => ['required','array' , 'required_if:brand_id,null'],
            'products.*' => ['required','numeric','exists:products,id,owner_id,' . $this->user_id],
            'discounts.*' => ['required','array','min:1'],
            'discounts.*.amount_from' => ['required','numeric'],
            'discounts.*.amount_to' => ['required','numeric'],
            'discounts.*.discount_type' => 'required|in:1,2',
            'discounts.*.discount' => ['required','numeric'],
        ];
    }

    protected function onUpdate()
    {
        return [
            'id' => 'required|exists:coupons,id',
            'name' => ['required' , 'string' , 'max:255'],
            'code' => ['required' , 'unique:coupons,code'],
//            'type' => ['required'],
            'percentage' => ['required_if:amount,null'],
            'amount' => ['required_if:percentage,null'],
            'quantity' => $this->unlimited == 0 ? ['required' , 'numeric'] : '',
            'unlimited' => 'required|boolean',
            'start_date' => ['required' , 'date'],
            'end_date' => ['required' , 'date'],
//            'purchased_amount' => ['required' , 'numeric'],
            'brand_id' => ['required_if:products,null'],
            'products' => ['required','array' , 'required_if:brand_id,null'],
            'products.*' => ['required','numeric','exists:products,id,owner_id,' . $this->user_id],
            'discounts.*' => ['required','array','min:1'],
            'discounts.*.amount_from' => ['required','numeric'],
            'discounts.*.amount_to' => ['required','numeric'],
            'discounts.*.discount_type' => 'required|in:1,2',
            'discounts.*.discount' => ['required','numeric'],
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // dd($this->all());
        return $this->isMethod('post') ? $this->onCreate() : $this->onUpdate();
    }
}
