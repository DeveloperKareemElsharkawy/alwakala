<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
            'id' => 'required|numeric|exists:products,id',
            'name' => 'required|max:255',
            'description' => 'required|max:255',
            'brand_id' => 'required|numeric|exists:brands,id',
            'category_id' => 'numeric|exists:categories,id',
            'images.*' => 'image|mimes:jpg,jpeg,png'
        ];
    }
}
