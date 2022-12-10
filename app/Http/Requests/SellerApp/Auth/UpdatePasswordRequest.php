<?php

namespace App\Http\Requests\SellerApp\Auth;

use App\Rules\Store\Auth\OldPassword;
use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Shared\RegisterUserRequest;

class UpdatePasswordRequest extends FormRequest
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
            'old_password' => ['required',new OldPassword],
            'new_password' => 'required|min:8|max:25',
            'password_confirmation' => 'required|same:new_password',
        ];

    }

    public function messages()
    {
        return [
            'new_password.different' => trans('messages.auth.chose_diff_pass'),
        ];
    }
}
