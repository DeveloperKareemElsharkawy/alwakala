<?php

namespace App\Http\Requests\SellerApp;

use Illuminate\Foundation\Http\FormRequest;

class EditAddressRequest extends FormRequest
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
            'id' => 'required|numeric|exists:addresses,id',
            'type' => 'required|in:home,work',
            'name' => 'required|string|max:255',
            'city_id' => 'required|numeric|exists:cities,id',
            'address' => 'required|string|max:255',
            'mobile' => 'required|mobile_number|size:11'
        ];
    }
}
