<?php

namespace App\Http\Requests\AdminPanel;

use Illuminate\Foundation\Http\FormRequest;

class VendorRequest extends FormRequest
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
           'user_phone' => 'required',
            'user_email' => 'required',
            'password' => 'required',
            'user_name' => 'required',
            'store_name' => 'required',
            'store_phone' => 'required',
            'city_id' => 'required',
            'store_address' => 'required',
            'logo' => 'required',
            'cover' => 'required',
            'licence' => 'required',
            'identity' => 'required',
            'text_card' => 'required',
            'brands' => 'required',
        ];
    }
}
