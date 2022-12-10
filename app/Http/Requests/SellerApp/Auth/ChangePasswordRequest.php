<?php

namespace App\Http\Requests\SellerApp\Auth;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Shared\RegisterUserRequest;

class ChangePasswordRequest extends FormRequest
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
            'type' => 'required|string',
            'key' => 'required|string|exists:users,' . request()->type,
            'confirm_code' => 'required|string|max:4',
            'password' => 'required|min:8|max:25|'
        ];

    }

    public function messages()
    {
        return [
            'type.required' => trans('seller_validation.required'),
        ];
    }
}
