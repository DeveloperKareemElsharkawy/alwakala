<?php

namespace App\Http\Requests\Admins;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class UpdateAdmin extends FormRequest
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
     * @param Request $request
     * @return array
     */
    public function rules(Request $request)
    {
        return [
            'id' => 'required|numeric|exists:users,id',
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $request->id,
            'mobile' => 'required|unique:users,mobile,' . $request->id,
            'password' => array(
                'min:8',
                'regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/'
            ),
        ];
    }

    public function messages()
    {
        return [
            'password.min' => 'Password must be at least 8 characters',
            'password.regex' => trans('validation.message.pass_hint')
        ];
    }
}
