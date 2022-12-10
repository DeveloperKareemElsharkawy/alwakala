<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInfoRequest extends FormRequest
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
            'id' => 'required|exits:products,id',
            'name' => 'required|string|max:255',
            'brand_id' => 'nullable|exits:brands,id',
            'category_id' => 'required|exits:categories,id',
            'consumer_price' => 'nullable|numeric'
        ];
    }
}
