<?php

namespace App\Http\Requests\SellerApp\Cart;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RemoveCartRequest extends FormRequest
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
            'cart_id' => ['required', Rule::exists('cart_items', 'id')->where(function ($query) {
                return $query->where('user_id', request()['user_id']);
            })],
        ];
    }
}
