<?php

namespace App\Http\Requests\Admins;

use Illuminate\Foundation\Http\FormRequest;

class RegisterAdmin extends FormRequest
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
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'mobile' => 'required|unique:users,mobile',
            'activation' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            'password' => 'required|min:8'
        ];
    }

    public function messages()
    {
        return [
            'password.min' => 'Password must be at least 8 characters',
            'password.regex' => trans('validation.message.pass_hint'),
        ];
    }
}
