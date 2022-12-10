<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;

class CreateProductSupplierStep1Request extends FormRequest
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
            'consumer_price' => 'numeric|digits_between:1,5',
            'category_id' => 'required|numeric|exists:categories,id|min:1|max:99999999',
            'publish_app_at' => 'required|date_format:Y-m-d',
            'discount' => 'required|numeric|min:0|max:100',
            'price' => 'required|numeric|min:1|max:99999999',
            'store_id' => 'required|numeric|exists:stores,id',
            'material_id' => 'required|numeric|exists:materials,id',
//            'material_rate' => 'required|numeric|min:1|max:'.(100-$this->get('material_rate_2',0)),
            'free_shipping' => 'required|in:0,1',
        ];
    }
}
