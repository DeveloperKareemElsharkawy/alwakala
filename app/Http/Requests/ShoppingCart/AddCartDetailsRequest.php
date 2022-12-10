<?php

namespace App\Http\Requests\ShoppingCart;

use Illuminate\Foundation\Http\FormRequest;

class AddCartDetailsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
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
            'payment_method_id' => 'required|exists:payment_methods,id|numeric|max:1000000',
            'shipment_method_id' => 'required|exists:shipment_methods,id|numeric|max:1000000',
            'seller_address_id' => 'required|exists:seller_addresses,id|numeric|max:1000000'
        ];
    }
}
