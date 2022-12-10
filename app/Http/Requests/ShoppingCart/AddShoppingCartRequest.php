<?php

namespace App\Http\Requests\ShoppingCart;

use Illuminate\Foundation\Http\FormRequest;

class AddShoppingCartRequest extends FormRequest
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
            'products' => 'required|array',
            'products.*.product_id' => 'required|numeric|digits_between: 1,5|exists:products,id',
            'products.*.store_id' => 'required|numeric|digits_between: 1,5|exists:stores,id',
            'products.*.size_id' => 'required_unless:products.*.packing_unit_id,==,1|numeric|digits_between: 1,5|exists:sizes,id',
            'products.*.color_id' => 'required|numeric|min:1|max:1000|exists:colors,id',
            'products.*.packing_unit_id' => 'required|numeric|digits_between: 1,5|exists:packing_units,id',
            'products.*.purchased_item_count' => 'required|numeric|digits_between: 1,5'
        ];
    }
}
