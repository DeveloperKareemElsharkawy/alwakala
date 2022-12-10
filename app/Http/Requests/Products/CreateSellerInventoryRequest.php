<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;

class CreateSellerInventoryRequest extends FormRequest
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
            'product_id' => 'required|integer|exists:products,id',
            'colors' => 'required|array',
            'colors.*.color' => 'required|string|max:255|min:1',
            'colors.*.size_id' => 'required|numeric|exists:sizes,id',
            'colors.*.stock' => 'required|numeric|digits_between:1,5',
            'publish_app_at' => 'required|date_format:Y-m-d'
        ];
    }
}
