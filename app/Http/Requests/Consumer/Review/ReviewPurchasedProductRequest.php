<?php

namespace App\Http\Requests\Consumer\Review;

use App\Rules\Consumer\Cart\QuantityCheck;
use Illuminate\Foundation\Http\FormRequest;

class ReviewPurchasedProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'product_id' => 'required|numeric|exists:products,id',
            'store_id' => 'required|numeric|exists:stores,id',
            'rate' => 'required|numeric|min:1|max:5',
            'review' => 'required|max:255',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg',
        ];
    }

}
