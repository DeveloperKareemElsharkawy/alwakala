<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            'id' => 'required|numeric|exists:users,id',
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $this->id,
            'mobile' => 'required|max:11|min:11|mobile_number|unique:users,mobile,' . $this->id,
            'password' => 'required|min:6',
            'role_id' => 'required|numeric|exists:roles,id'
        ];
    }
}
