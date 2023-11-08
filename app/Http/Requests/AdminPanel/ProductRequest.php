<?php

namespace App\Http\Requests\AdminPanel;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
            'name' => "required",
            'description' => "required",
            'publish_app_at' => "required",
            'price' => "required",
            'consumer_old_price' => "required",
            'brand_id' => "required",
            'material_id' => "required",
            'shipping_method_id' => "required",
            'policy_id' => "required",
            'free_shipping' => "required",
            'barcode' => "required",
            'barcode_text' => "required",
            'category_id' => "required",
            'subcategory_id' => "required",
            'subsubcategory_id' => "required",
        ];
    }
}
