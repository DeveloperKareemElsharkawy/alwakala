<?php

namespace App\Http\Requests\SellerApp\Order;

use Illuminate\Foundation\Http\FormRequest;

class OrderGetRequest extends FormRequest
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
            'id' => 'required|numeric|exists:orders,id',
            'order_type'=>'required|numeric|in:2,3'
        ];
    }
}
