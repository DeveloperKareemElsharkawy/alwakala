<?php

namespace App\Http\Requests\ShoppingCart;

use Illuminate\Foundation\Http\FormRequest;

class EditShoppingCart extends FormRequest
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
            'stores' => 'required|array',
            'stores.*.products' => 'required|array',
            'stores.*.products.*.packing_unit_product_store_attributes_id' =>'required|numeric|digits_between: 1,5|exists:packing_unit_product_store_attributes,id',

            'stores.*.products.*.purchased_item_count' =>'required|numeric|digits_between: 1,5'
        ];
    }
}
