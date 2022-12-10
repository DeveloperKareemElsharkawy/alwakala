<?php

namespace App\Http\Requests\SellerApp\Auth;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Shared\RegisterUserRequest;

class validateFirstScreenRequest extends FormRequest
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
            'name' => "required|string|max:25|min:2",
            'store_name' => "required|string|max:25|min:2",
            'legal_name' => "nullable|string|max:50|min:2",
            'email' => 'email|nullable|unique:users,email|max:255',
            'mobile' => 'required|unique:users,mobile|max:11|min:11|mobile_number',
            'store_mobile' => 'required|max:11|min:11|mobile_number',
//                'store_type_id' => 'required|numeric|exists:store_types,id',
            'password' => 'required|min:8|max:25',
           // 'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
           // 'licence' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
        ];

    }

    public function messages()
    {
        return [
            'store_name.required' => trans('seller_validation.required'),
        ];
    }
}
