<?php

namespace App\Http\Requests\Consumer;

use Illuminate\Foundation\Http\FormRequest;

class UpdateConsumerRequest extends FormRequest
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
            'id'=>'required|numeric|exists:users,id',
            'email' => 'required|email|unique:users,email,'.request()->id.'|max:50',
            'password' => 'nullable|confirmed|min:8|max:25',

        ];
    }
    public function messages()
    {
        return [
            'password.regex' => trans('validation.message.pass_hint')
        ];
    }

}
