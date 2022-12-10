<?php

namespace App\Http\Requests\Shared;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'email' => 'required|max:50',
            'password' => 'required|min:6|max:25'
        ];
    }

    public function messages()
    {
        return [
            'email.required' => trans('seller_validation.required'),
            'email.max' => trans('seller_validation.max'),
            'password.required' => trans('seller_validation.required'),
            'password.min' => trans('seller_validation.min'),
            'password.max' => trans('seller_validation.max'),
        ];
    }
}
