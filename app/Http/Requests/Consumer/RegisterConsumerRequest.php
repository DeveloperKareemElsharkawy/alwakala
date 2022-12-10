<?php

namespace App\Http\Requests\Consumer;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Shared\RegisterUserRequest;

class RegisterConsumerRequest extends FormRequest
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
            'name' => 'required|string|min:6|max:25',
            'email' => 'required|email|unique:users,email|max:50',
            'mobile' => 'required|unique:users,mobile|size:11|regex:/^(?=.*[0-9])[+0-9-]+$/',
            'password' => 'required|min:6|max:25',
            'password_confirmation' => 'required|same:password',
           // 'user_lang' => 'required',
            'image' => 'nullable|mimes:jpeg,jpg,png|max:1024',
        ];
    }
    public function messages()
    {
        return [
            'image.max' => trans('validation.message.image_max'),
            'image.mimes' => trans('validation.message.image_mimes'),
            'password.regex' => trans('validation.message.pass_hint'),
        ];
    }
}
