<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;

class CreateProductRequest extends FormRequest
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
            'category_id' => 'required|numeric|exists:categories,id',
            'stores' => 'required|array',
            'stores.*.packing_unit_id' => 'required|numeric|exists:packing_units,id',
            'stores.*.basic_unit_count' => 'required|numeric',
            'stores.*.child_id' => 'required|numeric|exists:packing_units,id',
            'stores.*.purchase_price' => 'required|numeric',
            'stores.*.sales_price' => 'required|numeric',
            'stores.*.stock' => 'required|numeric',
            'stores.*.publish_app_at' => 'required|date_format:Y-m-d',
            'stores.*.product_store_attributes' => 'required|array',
            'stores.*.product_store_attributes.*.size'=>'required',
            'stores.*.product_store_attributes.*.color'=>'required',
            'stores.*.product_store_attributes.*.quantity'=>'required|numeric',
            'stores.*.product_store_attributes.*.images'=>'required|array',
            'stores.*.product_store_attributes.*.images.*.image'=>'required',
//            'stores.*.bundle' => 'array|max:3',
//            'stores.*.bundle.*.quantity'=>'numeric',
//            'stores.*.bundle.*.price'=>'numeric',
            'barcodes'=> 'required|array',
            'barcodes.*.barcode' => 'required|max:255|unique:barcode_product'
        ];
    }
}
