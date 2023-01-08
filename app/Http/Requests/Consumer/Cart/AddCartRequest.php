<?php

namespace App\Http\Requests\Consumer\Cart;

use App\Rules\Consumer\Cart\QuantityCheck;
use Illuminate\Foundation\Http\FormRequest;

class AddCartRequest extends FormRequest
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
            'product_id' => 'required|integer|exists:products,id',
            'store_id' => ['required', 'numeric', 'exists:stores,id'],
            'color_id' => 'required|numeric|exists:colors,id',
            'size_id' => 'required|numeric|exists:sizes,id',
            'quantity' => ['required','integer', new QuantityCheck()],//, new UnitCountCheck()],
        ];
    }

}
