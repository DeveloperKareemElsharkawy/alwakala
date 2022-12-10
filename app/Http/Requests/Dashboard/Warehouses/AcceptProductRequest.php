<?php

namespace App\Http\Requests\Dashboard\Warehouses;

use Illuminate\Foundation\Http\FormRequest;

class AcceptProductRequest extends FormRequest
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
            'accept' => 'boolean',
            'warehouse_id' => 'required',
            'products.*.product_id' => 'required|numeric|exists:warehouse_products,product_id', 
        ];
    }
}
