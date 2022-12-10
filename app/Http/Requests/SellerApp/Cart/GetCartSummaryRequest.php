<?php

namespace App\Http\Requests\SellerApp\Cart;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GetCartSummaryRequest extends FormRequest
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
            'address_id' => [
                'sometimes',
                'nullable',
                'integer',
                Rule::exists('addresses', 'id')->where(function ($query) {
                    $query->where('user_id', request()->user('api')->id);
                }),
            ],
            'payment_method_id' => 'required|exists:payment_methods,id',
         ];
    }
}
