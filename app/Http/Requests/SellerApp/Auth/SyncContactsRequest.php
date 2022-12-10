<?php

namespace App\Http\Requests\SellerApp\Auth;

use App\Rules\Store\Auth\OldPassword;
use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Shared\RegisterUserRequest;

class SyncContactsRequest extends FormRequest
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
            'contacts' => 'required|array',
            'contacts.*.mobile' => 'required',
            'contacts.*.name' => 'required|string',
        ];

    }

    public function messages()
    {
        return [
            'contacts.required' => trans('seller_validation.required'),
        ];
    }
}
