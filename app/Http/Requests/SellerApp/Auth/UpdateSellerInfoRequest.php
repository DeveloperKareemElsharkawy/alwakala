<?php

namespace App\Http\Requests\SellerApp\Auth;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Shared\RegisterUserRequest;

class UpdateSellerInfoRequest extends FormRequest
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
            'name' => 'required|string|max:255',
//            'mobile' => 'required|mobile_number|unique:users,mobile,'.request()->user_id,
            'email' => 'required|email|unique:users,email,'.request()->user_id,

        ];

    }

}
