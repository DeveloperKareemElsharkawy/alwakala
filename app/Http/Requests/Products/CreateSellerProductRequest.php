<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;

class CreateSellerProductRequest extends FormRequest
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
//            'name' => 'required|max:255',
//            'description' => 'required|max:255',
//            'category_id' => 'required|numeric|exists:categories,id',
//            'brand_id' => 'required|numeric|exists:brands,id',
//            'consumer_price' => 'required|numeric|digits_between:1,5',
//            'packing_unit_id' => 'required|numeric|exists:packing_units,id',
//            'product_store_attributes' => 'required|array',
//            'product_store_attributes.*.color'=>'required|string|max:255|min:1',
//            'product_store_attributes.*.barcode'=>'required|max:255|unique:barcode_product',
//            'product_store_attributes.*.images'=>'required|array',
//            'product_store_attributes.*.images.*'=>'required|image|mimes:jpeg,png,jpg,gif,svg'
        ];
    }
}
