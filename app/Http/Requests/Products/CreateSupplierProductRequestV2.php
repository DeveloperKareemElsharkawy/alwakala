<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;

class CreateSupplierProductRequestV2 extends FormRequest
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
            'name' => 'required|max:255',
            'description' => 'required|max:255',
            'brand_id' => 'required|numeric|exists:brands,id',
            'category_id' => 'required|numeric|exists:categories,id|min:1|max:99999999',
            'publish_app_at' => 'required|date_format:Y-m-d',
            'discount' => 'required|numeric|min:0|max:100',
            'price' => 'required|numeric|min:1|max:99999999',
            'material_id' => 'required|numeric|exists:materials,id',
            'material_rate' => 'numeric|min:1|max:100',
            'free_shipping' => 'required',
            'product_attributes' => 'required|array',
            'product_attributes.*.size_id' => 'required|numeric|exists:sizes,id',
            'product_attributes.*.quantity' => 'required',
            'shipping_method_id' => 'required|exists:shipping_methods,id',
            'policy_id' => 'required|exists:policies,id',
            'consumer_price' => 'numeric|digits_between:1,5|required_if:policy_id,1',

//            'basic_unit_id' => 'required|numeric|exists:packing_units,id', //connected with categories set automatic
//            'bundles' => 'array|required',
//            'bundles.*.quantity' => 'required|numeric',
//            'bundles.*.price' => 'required|numeric',
        ];

    }
}
