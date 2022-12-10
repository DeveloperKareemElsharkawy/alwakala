<?php

namespace App\Http\Requests\SellerApp\QRCode;

use App\Rules\Seller\Cart\QuantityCheck;
use Illuminate\Foundation\Http\FormRequest;

class MakeOrderByQrRequest extends FormRequest
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
            'store_id' => 'required|integer|exists:stores,id',
//            'products.*.product_id' => 'required|exists:products,id',
//            'products.*.store_id' => 'required|numeric|exists:stores,id',
//            'products.*.color_id' => 'required|numeric|exists:colors,id',
//            'products.*.quantity' => [
//                'required',
//                new QuantityCheck($this->input('products.*.product_id'), $this->input('products.*.store_id'), $this->input('products.*.color_id'))
//            ],
//            'packing_unit_id' => 'numeric|exists:packing_units,id',
        ];
    }
}
