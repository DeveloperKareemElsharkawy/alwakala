<?php

namespace App\Http\Requests\Products;

use Illuminate\Foundation\Http\FormRequest;

class GetProductsRequest extends FormRequest
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
            'product_id' => 'nullable|numeric|exists:products,id',
            //'category_id' => 'nullable|numeric|exists:categories,id',
            //'brand_id' => 'nullable|array',
            //'brand_id' => 'nullable|exists:brands,id',
            //'color_id' => 'nullable|numeric|exists:colors,id',
            //'size_id' => 'nullable|numeric|exists:sizes,id',
            'price_to' => 'nullable|numeric|min:0',
            'price_from' => 'nullable|numeric|max:1000000',
            'sort_by_date' => 'nullable|string|in:asc,desc,ASC,DESC',
            'sort_by_price' => 'nullable|string|in:asc,desc,ASC,DESC',
            'sort_by_rate' => 'nullable|string|in:asc,desc,ASC,DESC',
            'sort_by_most_selling' => 'nullable|string|in:asc,desc,ASC,DESC',
        ];
    }
}
